<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Auth::user()->categories()
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        Auth::user()->categories()->create($request->validated());

        return redirect()->route('categories.index')->with('status', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category): View
    {
        $this->authorizeOwnership($category);

        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->authorizeOwnership($category);

        $category->update($request->validated() + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('categories.index')->with('status', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->authorizeOwnership($category);

        $category->update(['is_active' => false]);

        return redirect()->route('categories.index')->with('status', 'Kategori berhasil dinonaktifkan.');
    }

    private function authorizeOwnership(Category $category): void
    {
        abort_unless($category->user_id === Auth::id(), 404);
    }
}
