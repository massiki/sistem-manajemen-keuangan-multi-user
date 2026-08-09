<?php

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\QueryException;

test('transaction belongs to user, account and category', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'income']);
    $transaction = Transaction::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
    ]);

    expect($transaction->user->is($user))->toBeTrue()
        ->and($transaction->account->is($account))->toBeTrue()
        ->and($transaction->category->is($category))->toBeTrue()
        ->and($user->transactions)->toHaveCount(1)
        ->and($account->transactions)->toHaveCount(1)
        ->and($category->transactions)->toHaveCount(1);
});

test('deleting a user cascades to transactions', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    Transaction::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
    ]);

    $userId = $user->id;
    $user->delete();

    expect(Transaction::where('user_id', $userId)->count())->toBe(0);
});

test('an account with transactions cannot be deleted', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    Transaction::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
    ]);

    expect(fn () => $account->delete())->toThrow(QueryException::class);
});

test('a category with transactions cannot be deleted', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);
    $category = Category::factory()->create(['user_id' => $user->id, 'type' => 'expense']);
    Transaction::factory()->create([
        'user_id' => $user->id,
        'account_id' => $account->id,
        'category_id' => $category->id,
    ]);

    expect(fn () => $category->delete())->toThrow(QueryException::class);
});

test('transaction casts are applied', function () {
    $transaction = Transaction::factory()->create([
        'transaction_date' => '2026-08-09',
        'amount' => 150000,
    ]);

    expect($transaction->transaction_date->format('Y-m-d'))->toBe('2026-08-09')
        ->and($transaction->amount)->toBe('150000.00');
});
