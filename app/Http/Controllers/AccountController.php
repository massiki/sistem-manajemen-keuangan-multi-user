<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        $accounts = Auth::user()->accounts()
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        return view('accounts.index', compact('accounts'));
    }

    public function create(): View
    {
        return view('accounts.create');
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        Auth::user()->accounts()->create($request->validated());

        return redirect()->route('accounts.index')->with('status', 'Kas berhasil ditambahkan.');
    }

    public function edit(Account $account): View
    {
        $this->authorizeOwnership($account);

        return view('accounts.edit', compact('account'));
    }

    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $this->authorizeOwnership($account);

        $account->update($request->validated() + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('accounts.index')->with('status', 'Kas berhasil diperbarui.');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $this->authorizeOwnership($account);

        $account->update(['is_active' => false]);

        return redirect()->route('accounts.index')->with('status', 'Kas berhasil dinonaktifkan.');
    }

    private function authorizeOwnership(Account $account): void
    {
        abort_unless($account->user_id === Auth::id(), 404);
    }
}
