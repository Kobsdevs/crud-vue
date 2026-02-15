<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('categories/Index', [
            'categories' => Category::withCount('campaigns')
                ->orderBy('name')
                ->paginate(10),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('categories/Create');
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        Category::create($request->validated());

        return to_route('categories.index')
            ->with('success', 'Catégorie créée avec succès.');
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('categories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        return to_route('categories.index')
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();

        return to_route('categories.index')
            ->with('success', 'Catégorie supprimée avec succès.');
    }
}
