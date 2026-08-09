<?php

use App\Models\Account;
use App\Models\User;

test('guest is redirected to login for kas pages', function () {
    $this->get(route('accounts.index'))->assertRedirect(route('login'));
    $this->get(route('accounts.create'))->assertRedirect(route('login'));
});

test('user can view their own accounts', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'name' => 'Kas Rumah']);
    $other = Account::factory()->create(['name' => 'Kas Orang Lain']);

    $this->actingAs($user)->get(route('accounts.index'))
        ->assertOk()
        ->assertSee('Kas Rumah')
        ->assertDontSee('Kas Orang Lain');
});

test('user can create a kas', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('accounts.store'), [
        'name' => 'Kas Rumah',
        'type' => 'cash',
        'initial_balance' => 250000,
        'description' => 'Kas utama',
    ])->assertRedirect(route('accounts.index'));

    $this->assertDatabaseHas('accounts', [
        'user_id' => $user->id,
        'name' => 'Kas Rumah',
        'initial_balance' => 250000,
    ]);
});

test('kas creation requires name, valid type and non-negative balance', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('accounts.store'), [
        'name' => '',
        'type' => 'invalid',
        'initial_balance' => -5,
    ])->assertSessionHasErrors(['name', 'type', 'initial_balance']);
});

test('kas name must be unique per user', function () {
    $user = User::factory()->create();
    Account::factory()->create(['user_id' => $user->id, 'name' => 'Kas Rumah']);

    $this->actingAs($user)->post(route('accounts.store'), [
        'name' => 'Kas Rumah',
        'type' => 'cash',
    ])->assertSessionHasErrors('name');
});

test('same kas name for different users is allowed', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();

    $this->actingAs($firstUser)->post(route('accounts.store'), [
        'name' => 'Kas Rumah', 'type' => 'cash',
    ])->assertRedirect(route('accounts.index'));

    $this->actingAs($secondUser)->post(route('accounts.store'), [
        'name' => 'Kas Rumah', 'type' => 'cash',
    ])->assertRedirect(route('accounts.index'));

    expect(Account::where('name', 'Kas Rumah')->count())->toBe(2);
});

test('user can update their own account', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'name' => 'Kas Lama']);

    $this->actingAs($user)->put(route('accounts.update', $account), [
        'name' => 'Kas Baru',
        'type' => 'bank',
        'initial_balance' => 100,
        'is_active' => 1,
    ])->assertRedirect(route('accounts.index'));

    $this->assertDatabaseHas('accounts', [
        'id' => $account->id,
        'user_id' => $user->id,
        'name' => 'Kas Baru',
        'type' => 'bank',
        'is_active' => 1,
    ]);
});

test('editing an account without the active checkbox archives it', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'is_active' => true]);

    $this->actingAs($user)->put(route('accounts.update', $account), [
        'name' => $account->name,
        'type' => $account->type,
    ])->assertRedirect(route('accounts.index'));

    expect($account->fresh()->is_active)->toBeFalse();
});

test('archive keeps the account but marks it inactive', function () {
    $user = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $user->id, 'is_active' => true]);

    $this->actingAs($user)->delete(route('accounts.destroy', $account))
        ->assertRedirect(route('accounts.index'));

    expect(Account::find($account->id))->not->toBeNull()
        ->and($account->fresh()->is_active)->toBeFalse();
});

test('user cannot view, edit or archive another user account', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $account = Account::factory()->create(['user_id' => $owner->id]);

    $this->actingAs($intruder)
        ->get(route('accounts.edit', $account))->assertNotFound();
    $this->actingAs($intruder)
        ->put(route('accounts.update', $account), [
            'name' => 'Disusupi',
            'type' => 'cash',
        ])->assertNotFound();
    $this->actingAs($intruder)
        ->delete(route('accounts.destroy', $account))->assertNotFound();

    expect(Account::find($account->id)->name)->toBe($account->name);
});
