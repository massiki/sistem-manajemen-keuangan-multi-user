<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $transactions = Auth::user()->transactions()
            ->with(['account', 'category'])
            ->when($request->filled('q'), fn ($query) => $query->where('description', 'like', '%'.$request->input('q').'%'))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('account_id'), fn ($query) => $query->where('account_id', $request->input('account_id')))
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->input('category_id')))
            ->when($request->filled('from'), fn ($query) => $query->where('transaction_date', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($query) => $query->where('transaction_date', '<=', $request->input('to')))
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $accounts = Auth::user()->accounts()->orderBy('name')->get();
        $categories = Auth::user()->categories()->orderBy('name')->get();

        return view('transactions.index', [
            'transactions' => $transactions,
            'accounts' => $accounts,
            'categories' => $categories,
            'running' => $this->runningBalances(),
        ]);
    }

    public function create(): View
    {
        return view('transactions.create', [
            'accounts' => $this->activeAccounts(),
            'categories' => $this->allCategories(),
        ]);
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        Auth::user()->transactions()->create($request->validated());

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil ditambahkan.');
    }

    public function show(Transaction $transaction): View
    {
        $this->authorizeOwnership($transaction);

        return view('transactions.show', compact('transaction'));
    }

    public function edit(Transaction $transaction): View
    {
        $this->authorizeOwnership($transaction);

        return view('transactions.edit', [
            'transaction' => $transaction,
            'accounts' => $this->allAccounts(),
            'categories' => $this->allCategories(),
        ]);
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorizeOwnership($transaction);

        $transaction->update($request->validated());

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorizeOwnership($transaction);

        $transaction->delete();

        return redirect()->route('transactions.index')->with('status', 'Transaksi berhasil dihapus.');
    }

    private function authorizeOwnership(Transaction $transaction): void
    {
        abort_unless($transaction->user_id === Auth::id(), 404);
    }

    private function activeAccounts(): Collection
    {
        return Auth::user()->accounts()->where('is_active', true)->orderBy('name')->get();
    }

    private function allAccounts(): Collection
    {
        return Auth::user()->accounts()->orderBy('name')->get();
    }

    private function allCategories(): Collection
    {
        return Auth::user()->categories()->orderBy('name')->get();
    }

    /**
     * Build a map of running balances per account, keyed by "account_id|date|id".
     * The balance shown for a transaction is the account balance after that row.
     */
    private function runningBalances(): array
    {
        $initial = Auth::user()->accounts()->pluck('initial_balance', 'id');
        $totals = [];
        $balances = [];

        Auth::user()->transactions()
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->each(function (Transaction $transaction) use (&$totals, &$balances, $initial) {
                $accountId = $transaction->account_id;
                $totals[$accountId] = ($totals[$accountId] ?? (float) ($initial[$accountId] ?? 0))
                    + ((float) $transaction->amount * ($transaction->type === 'income' ? 1 : -1));

                $key = $accountId.'|'.$transaction->transaction_date->toDateString().'|'.$transaction->id;
                $balances[$key] = $totals[$accountId];
            });

        return $balances;
    }
}
