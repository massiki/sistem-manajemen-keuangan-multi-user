<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();

        $selectedAccountId = $request->query('account_id');
        if ($selectedAccountId !== null) {
            $valid = $user->accounts()->where('id', $selectedAccountId)->exists();
            if (! $valid) {
                $selectedAccountId = null;
            }
        }

        $baseQuery = $user->transactions();
        if ($selectedAccountId !== null) {
            $baseQuery->where('account_id', $selectedAccountId);
        }

        $summary = (clone $baseQuery)
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense,
                COUNT(*) as total
            ")
            ->first();

        $totalIncome = (float) $summary->income;
        $totalExpense = (float) $summary->expense;

        if ($selectedAccountId !== null) {
            $initialBalance = (float) $user->accounts()->where('id', $selectedAccountId)->sum('initial_balance');
        } else {
            $initialBalance = (float) $user->accounts()->sum('initial_balance');
        }

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

        $recentTransactionsQuery = $user->transactions()
            ->with(['account', 'category'])
            ->latest('transaction_date')
            ->latest('id')
            ->limit(5);
        if ($selectedAccountId !== null) {
            $recentTransactionsQuery->where('account_id', $selectedAccountId);
        }
        $recentTransactions = $recentTransactionsQuery->get();

        $dailyRowsQuery = $user->transactions()
            ->selectRaw("
                transaction_date,
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) as income,
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) as expense
            ")
            ->where('transaction_date', '>=', now()->subDays(6)->toDateString())
            ->groupBy('transaction_date');
        if ($selectedAccountId !== null) {
            $dailyRowsQuery->where('account_id', $selectedAccountId);
        }
        $dailyRows = $dailyRowsQuery->get()
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

        $expenseByCategoryQuery = $user->transactions()
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('transactions.type', 'expense')
            ->selectRaw('categories.name, SUM(transactions.amount) as total')
            ->groupBy('categories.name')
            ->orderByDesc('total');
        if ($selectedAccountId !== null) {
            $expenseByCategoryQuery->where('transactions.account_id', $selectedAccountId);
        }
        $expenseByCategory = $expenseByCategoryQuery->get();

        return view('dashboard', [
            'balance' => $initialBalance + $totalIncome - $totalExpense,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'totalTransactions' => (int) $summary->total,
            'accounts' => $accounts,
            'recentTransactions' => $recentTransactions,
            'daily' => $daily,
            'chartMax' => max($daily->max('income'), $daily->max('expense')) ?: 1,
            'expenseByCategory' => $expenseByCategory,
            'selectedAccountId' => $selectedAccountId,
        ]);
    }
}
