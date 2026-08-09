<?php

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\QueryException;

test('account belongs to user and user has many accounts', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id]);

    expect($account->user->is($user))->toBeTrue();
    expect($user->accounts)->toHaveCount(1);
    expect($user->accounts->first()->is($account))->toBeTrue();
});

test('account name is unique per user but shared across users is allowed', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();

    Account::factory()->create(['user_id' => $firstUser->id, 'name' => 'Kas Rumah']);
    Account::factory()->create(['user_id' => $secondUser->id, 'name' => 'Kas Rumah']);

    expect(Account::where('name', 'Kas Rumah')->count())->toBe(2);

    expect(fn () => Account::factory()->create(['user_id' => $firstUser->id, 'name' => 'Kas Rumah']))
        ->toThrow(QueryException::class);
});

test('deleting a user cascades to accounts', function () {
    $user = User::factory()->create();
    Account::factory()->create(['user_id' => $user->id]);

    $user->delete();

    expect(Account::where('user_id', $user->id)->count())->toBe(0);
});

test('account defaults and casts are applied', function () {
    $account = Account::factory()->create([
        'initial_balance' => 250000,
        'is_active' => true,
    ]);

    expect($account->initial_balance)->toBe('250000.00')
        ->and($account->is_active)->toBeTrue();
});
