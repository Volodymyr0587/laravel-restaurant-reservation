<?php

namespace App\Http\Controllers\Admin;

use App\Models\Menu;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $menus = Menu::all();
        return view('admin.categories.index', compact('categories', 'menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request)
    {
        $categoryData = $request->validated();

        $categoryData['image'] = $this->handleImageUpload($request);

        Category::create($categoryData);

        return to_route('admin.categories.index')->with('success', 'Category created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $categoryData = $request->validated();

        $categoryData['image'] = $this->handleImageUpload($request);

        $category->update($categoryData);

        return to_route('admin.categories.index')->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return to_route('admin.categories.index')->with('danger', 'Category deleted successfully');
    }

    protected function handleImageUpload($request)
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('categories', 'public');
        }

        return null;
    }
}
