<?php
namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class MenuItemController extends Controller
{
    // List all menu items
    public function index(Request $request)
{
    $query = MenuItem::query();

    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
              ->orWhere('description', 'LIKE', "%{$search}%")
              ->orWhere('category', 'LIKE', "%{$search}%");
        });
    }

    // Category filter
    if ($request->filled('category')) {
        $query->where('category', $request->category);
    }

    $menuItems = $query->paginate(5);
    $categories = $this->getCategories(); // Get ENUM values

    return view('menus.index', compact('menuItems', 'categories'));
}

    // Get ENUM categories from the menu_items table
    private function getCategories()
    {
        $type = \DB::select("SHOW COLUMNS FROM menu_items WHERE Field = 'category'")[0]->Type;
        preg_match('/enum\((.*)\)$/', $type, $matches);
        $enumValues = str_getcsv($matches[1], ',', "'");

        return $enumValues;
    }

    // Show a specific menu item
    public function show($id)
    {
        $menuItem = MenuItem::find($id);

        if (!$menuItem) {
            return redirect()->route('menus.index')->with('error', 'Menu item not found!');
        }

        return view('menus.show', compact('menuItem'));
    }

    // Show create menu item form
    public function create()
    {
        return view('menus.create');
    }

    // Add a new menu item with image upload
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/|min:0',
            'category' => 'required|in:Fried,Soup,Steam,Dessert,Drink,Smoothies',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048' // Validate image
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu_images', 'public');
            $data['image'] = $imagePath;
        }

        // Create the menu item
        MenuItem::create($data);

        return redirect()->route('menus.index')->with('success', 'Menu item created successfully!');
    }

    // Show edit form for a menu item
    public function edit($id)
    {
        $menuItem = MenuItem::findOrFail($id);

        return view('menus.edit', compact('menuItem'));
    }

    // Update an existing menu item with image upload
    public function update(Request $request, $id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|regex:/^\d+(\.\d{1,2})?$/|min:0',
            'category' => 'required|in:Fried,Soup,Steam,Dessert,Drink,Smoothies',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048' // Validate image
        ]);

        $data = $request->all();

        // Handle image update
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($menuItem->image) {
                Storage::disk('public')->delete($menuItem->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('menu_images', 'public');
            $data['image'] = $imagePath;
        }

        // Update menu item
        $menuItem->update($data);

        return redirect()->route('menus.index')->with('success', 'Menu item updated successfully!');
    }

    // Delete a menu item and its image
    public function destroy($id)
    {
        $menuItem = MenuItem::find($id);

        if (!$menuItem) {
            return redirect()->route('menus.index')->with('error', 'Menu item not found!');
        }

        // Delete image if exists
        if ($menuItem->image) {
            Storage::disk('public')->delete($menuItem->image);
        }

        // Delete the menu item
        $menuItem->delete();

        return redirect()->route('menus.index')->with('success', 'Menu item deleted successfully!');
    }
}
