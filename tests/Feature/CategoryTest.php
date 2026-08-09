<?php

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\QueryException;

test('category belongs to user and user has many categories', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    expect($category->user->is($user))->toBeTrue()
        ->and($user->categories()->count())->toBe(1)
        ->and($user->categories->first()->is($category))->toBeTrue();
});

test('category name is unique per user and type', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Category::factory()->create(['user_id' => $user->id, 'name' => 'Honor', 'type' => 'income']);
    Category::factory()->create(['user_id' => $user->id, 'name' => 'Honor', 'type' => 'expense']);
    Category::factory()->create(['user_id' => $otherUser->id, 'name' => 'Honor', 'type' => 'income']);

    expect(Category::where('name', 'Honor')->count())->toBe(3);

    expect(fn () => Category::factory()->create(['user_id' => $user->id, 'name' => 'Honor', 'type' => 'income']))
        ->toThrow(QueryException::class);
});

test('deleting a user cascades to categories', function () {
    $user = User::factory()->create();
    Category::factory()->create(['user_id' => $user->id]);

    $user->delete();

    expect(Category::where('user_id', $user->id)->count())->toBe(0);
});

test('is_active default is true', function () {
    $category = Category::factory()->create();

    expect($category->is_active)->toBeTrue();
});
