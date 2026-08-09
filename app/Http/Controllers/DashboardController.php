<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();

        $summary = $user->transactions()
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense,
                COUNT(*) as total
            ")
            ->first();

        $totalIncome = (float) $summary->income;
        $totalExpense = (float) $summary->expense;
        $initialBalance = (float) $user->accounts()->sum('initial_balance');

        $accountTotals = $user->transactions()
            ->selectRaw("
                account_id,
                SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as income,
                SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as expense
            ")
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        $accounts = $user->accounts()
            ->withCount('transactions')
            ->orderBy('name')
            ->get()
            ->map(function ($account) use ($accountTotals) {
                $row = $accountTotals->get($account->id);
                $income = (float) ($row->income ?? 0);
                $expense = (float) ($row->expense ?? 0);

                $account->income = $income;
                $account->expense = $expense;
                $account->balance = (float) $account->initial_balance + $income - $expense;

                return $account;
            });

        $recentTransactions = $user->transactions()
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->latest('id')
            ->limit(5)
            ->get();

        $dailyRows = $user->transactions()
            ->selectRaw("
                transaction_date,
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense
            ")
            ->where('transaction_date', '>=', now()->subDays(6)->toDateString())
            ->groupBy('transaction_date')
            ->get()
            ->keyBy(fn ($row) => $row->transaction_date->toDateString());

        $daily = collect(range(6, 0))->map(function ($daysAgo) use ($dailyRows) {
            $date = now()->subDays($daysAgo);
            $row = $dailyRows->get($date->toDateString());

            return [
                'date' => $date,
                'label' => $date->format('d/m'),
                'income' => (float) ($row->income ?? 0),
                'expense' => (float) ($row->expense ?? 0),
            ];
        });

        return view('dashboard', [
            'balance' => $initialBalance + $totalIncome - $totalExpense,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalTransactions' => (int) $summary->total,
            'accounts' => $accounts,
            'recentTransactions' => $recentTransactions,
            'daily' => $daily,
            'chartMax' => max($daily->max('income'), $daily->max('expense')) ?: 1,
        ]);
    }
}
