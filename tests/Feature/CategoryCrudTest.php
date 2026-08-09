<?php

use App\Models\Category;
use App\Models\User;

test('guest is redirected to login for category pages', function () {
    $this->get(route('categories.index'))->assertRedirect(route('login'));
    $this->get(route('categories.create'))->assertRedirect(route('login'));
});

test('user can view their own categories', function () {
    $user = User::factory()->create();
    Category::factory()->create(['user_id' => $user->id, 'name' => 'Gaji', 'type' => 'income']);
    Category::factory()->create(['name' => 'Kategori Orang Lain', 'type' => 'income']);

    $this->actingAs($user)->get(route('categories.index'))
        ->assertOk()
        ->assertSee('Gaji')
        ->assertDontSee('Kategori Orang Lain');
});

test('user can create a category', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('categories.store'), [
        'name' => 'Makanan / Dapur',
        'type' => 'expense',
    ])->assertRedirect(route('categories.index'));

    $this->assertDatabaseHas('categories', [
        'user_id' => $user->id,
        'name' => 'Makanan / Dapur',
        'type' => 'expense',
    ]);
});

test('category creation requires name and valid type', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('categories.store'), [
        'name' => '',
        'type' => 'invalid',
    ])->assertSessionHasErrors(['name', 'type']);
});

test('category name is unique per user and type, but shared names across types are allowed', function () {
    $user = User::factory()->create();
    Category::factory()->create(['user_id' => $user->id, 'name' => 'Honor', 'type' => 'income']);

    $this->actingAs($user)->post(route('categories.store'), [
        'name' => 'Honor', 'type' => 'income',
    ])->assertSessionHasErrors('name');

    $this->actingAs($user)->post(route('categories.store'), [
        'name' => 'Honor', 'type' => 'expense',
    ])->assertRedirect(route('categories.index'));
});

test('same category name for different users is allowed', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create();

    $this->actingAs($firstUser)->post(route('categories.store'), [
        'name' => 'Transportasi', 'type' => 'expense',
    ])->assertRedirect(route('categories.index'));

    $this->actingAs($secondUser)->post(route('categories.store'), [
        'name' => 'Transportasi', 'type' => 'expense',
    ])->assertRedirect(route('categories.index'));

    expect(Category::where('name', 'Transportasi')->count())->toBe(2);
});

test('user can update their own category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'name' => 'Konsumsi', 'type' => 'expense']);

    $this->actingAs($user)->put(route('categories.update', $category), [
        'name' => 'Konsumsi Kantor',
        'type' => 'expense',
        'is_active' => 1,
    ])->assertRedirect(route('categories.index'));

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Konsumsi Kantor',
        'is_active' => 1,
    ]);
});

test('updating a category keeps its name acceptable when unchanged', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'name' => 'Gaji', 'type' => 'income']);

    $this->actingAs($user)->put(route('categories.update', $category), [
        'name' => 'Gaji',
        'type' => 'income',
        'is_active' => 1,
    ])->assertRedirect(route('categories.index'));
});

test('archive keeps the category but marks it inactive', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id, 'is_active' => true]);

    $this->actingAs($user)->delete(route('categories.destroy', $category))
        ->assertRedirect(route('categories.index'));

    expect(Category::find($category->id))->not->toBeNull()
        ->and($category->fresh()->is_active)->toBeFalse();
});

test('user cannot view, edit or archive another user category', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $owner->id, 'type' => 'expense']);

    $this->actingAs($intruder)
        ->get(route('categories.edit', $category))->assertNotFound();
    $this->actingAs($intruder)
        ->put(route('categories.update', $category), [
            'name' => 'Disusupi',
            'type' => 'expense',
        ])->assertNotFound();
    $this->actingAs($intruder)
        ->delete(route('categories.destroy', $category))->assertNotFound();

    expect(Category::find($category->id)->name)->toBe($category->name);
});
