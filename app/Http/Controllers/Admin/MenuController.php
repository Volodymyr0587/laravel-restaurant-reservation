<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreMenuRequest;
use App\Http\Requests\UpdateMenuRequest;
use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::all();
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.menus.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMenuRequest $request)
    {
        $menuData = $request->validated();

        $menuData['image'] = $this->handleImageUpload($request);

        $menu = Menu::create($menuData);

        $menu->categories()->attach($request->categories);

        return to_route('admin.menus.index')->with('success', 'Menu created successfully');

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
    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMenuRequest $request, Menu $menu)
    {
        $menuData = $request->validated();

        $menuData['image'] = $this->handleImageUpload($request);

        // Update the menu data
        $menu->update($menuData);

        // Sync the categories (this will update the categories attached to the menu)
        $menu->categories()->sync($request->categories);

        return to_route('admin.menus.index')->with('success', 'Menu updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return to_route('admin.menus.index')->with('success', 'Menu deleted successfully');
    }

    protected function handleImageUpload($request)
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('menus', 'public');
        }

        return null;
    }
}
