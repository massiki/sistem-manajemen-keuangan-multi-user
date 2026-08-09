<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;

test('guest is redirected to login for transaction pages', function () {
    $this->get(route('transactions.index'))->assertRedirect(route('login'));
    $this->get(route('transactions.create'))->assertRedirect(route('login'));
});

test('user can view their own transactions', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'name' => 'Kas Rumah']);
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income', 'name' => 'Gaji']);
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'description' => 'Honor bulanan',
    ]);
    $other = Transaction::factory()->create(['description' => 'Transaksi orang lain']);

    $this->actingAs($user)->get(route('transactions.index'))
        ->assertOk()
        ->assertSee('Honor bulanan')
        ->assertDontSee('Transaksi orang lain');

    $this->actingAs($user)->get(route('transactions.show', $transaction))
        ->assertOk()
        ->assertSee('Honor bulanan');
});

test('user can create an income transaction', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);

    $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'transaction_date' => '2026-08-09',
        'type' => 'income',
        'amount' => 150000,
        'description' => 'Honor proyek',
        'notes' => 'Transfer BCA',
    ])->assertRedirect(route('transactions.index'));

    $this->assertDatabaseHas('transactions', [
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'type' => 'income',
        'amount' => 150000,
        'description' => 'Honor proyek',
    ]);
});

test('transaction creation requires valid fields', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => 99999,
        'category_id' => 99999,
        'transaction_date' => '',
        'type' => 'invalid',
        'amount' => -5,
    ])->assertSessionHasErrors(['account_id', 'category_id', 'transaction_date', 'type', 'amount']);
});

test('transaction creation rejects a category that belongs to another user', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $foreignCategory = Category::factory()->create(['type' => 'income']);

    $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $foreignCategory->id,
        'transaction_date' => '2026-08-09',
        'type' => 'income',
        'amount' => 1000,
    ])->assertSessionHasErrors('category_id');

    expect(Transaction::count())->toBe(0);
});

test("transaction creation rejects another user's account", function () {
    $user = User::factory()->create();
    $foreignAccount = Account::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);

    $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => $foreignAccount->id,
        'category_id' => $category->id,
        'transaction_date' => '2026-08-09',
        'type' => 'income',
        'amount' => 1000,
    ])->assertSessionHasErrors('account_id');
});

test('transaction category type must match transaction type', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $incomeCategory = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);

    $this->actingAs($user)->post(route('transactions.store'), [
        'account_id' => $account->id,
        'category_id' => $incomeCategory->id,
        'transaction_date' => '2026-08-09',
        'type' => 'expense',
        'amount' => 1000,
    ])->assertSessionHasErrors('category_id');
});

test('user can update their own transaction', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
        'amount' => 1000,
    ]);

    $this->actingAs($user)->put(route('transactions.update', $transaction), [
        'account_id' => $account->id,
        'category_id' => $category->id,
        'transaction_date' => '2026-08-10',
        'type' => 'expense',
        'amount' => 250000,
        'description' => 'Diperbarui',
    ])->assertRedirect(route('transactions.index'));

    expect($transaction->fresh())
        ->amount->toBe('250000.00')
        ->description->toBe('Diperbarui');
});

test('user cannot view, edit or delete another user transaction', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $owner->id]);
    $category = Category::factory()->create(['user_id' => $owner->id, 'type' => 'expense']);
    $transaction = Transaction::factory()->create([
        'user_id' => $owner->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
    ]);
    $intruderAccount = Account::factory()->create(['user_id' => $intruder->id]);
    $intruderCategory = Category::factory()->create(['user_id' => $intruder->id, 'type' => 'expense']);

    $this->actingAs($intruder)
        ->get(route('transactions.show', $transaction))->assertNotFound();
    $this->actingAs($intruder)
        ->get(route('transactions.edit', $transaction))->assertNotFound();
    $this->actingAs($intruder)
        ->put(route('transactions.update', $transaction), [
            'account_id' => $intruderAccount->id,
            'category_id' => $intruderCategory->id,
            'transaction_date' => '2026-08-09',
            'type' => 'expense',
            'amount' => 1000,
        ])->assertNotFound();
    $this->actingAs($intruder)
        ->delete(route('transactions.destroy', $transaction))->assertNotFound();

    expect(Transaction::find($transaction->id))->not->toBeNull();
});

test('user can delete their own transaction', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
    ]);

    $this->actingAs($user)->delete(route('transactions.destroy', $transaction))
        ->assertRedirect(route('transactions.index'));

    expect(Transaction::find($transaction->id))->toBeNull();
});

test('transaction history shows running balance', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 100000]);
    $income = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);
    $expense = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);

    Transaction::factory()->create([
        'user_id' => $user->id, 'account_id' => $account->id, 'category_id' => $income->id,
        'type' => 'income', 'amount' => 50000, 'transaction_date' => '2026-08-01',
    ]);
    Transaction::factory()->create([
        'user_id' => $user->id, 'account_id' => $account->id, 'category_id' => $expense->id,
        'type' => 'expense', 'amount' => 20000, 'transaction_date' => '2026-08-02',
    ]);

    $this->actingAs($user)->get(route('transactions.index'))
        ->assertOk()
        ->assertSee('Rp 130.000')
        ->assertSee('Rp 150.000');
});

test('dashboard shows summary for the logged in user only', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 500000]);
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);
    Transaction::factory()->create([
        'user_id' => $user->id, 'account_id' => $account->id, 'category_id' => $category->id,
        'type' => 'income', 'amount' => 100000,
    ]);

    $other = User::factory()->create();
    $otherAccount = Account::factory()->create(['user_id' => $other->id, 'initial_balance' => 99999999]);
    $otherCategory = Category::factory()->create(['user_id' => $other->id, 'type' => 'income']);
    Transaction::factory()->create([
        'user_id' => $other->id, 'account_id' => $otherAccount->id, 'category_id' => $otherCategory->id,
        'type' => 'income', 'amount' => 99999999,
    ]);

    $this->actingAs($user)->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Rp 600.000')
        ->assertSee('Halo')
        ->assertDontSee('Rp 99.999.999');
});
