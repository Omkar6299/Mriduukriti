<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('created_at', 'DESC')->get();

        return view('admin_panel.category.index', compact('categories'));
    }
    public function create()
    {
        return view('admin_panel.category.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:50',
            'description' => 'nullable|string|max:150',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:500',
        ]);
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/categories');
            $image->move($destinationPath, $imageName);
            $imagePath = 'uploads/categories/' . $imageName;
        }
        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status ?? 0,
            'image' => $imagePath,
        ]);
        return redirect()->route('category.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin_panel.category.edit', compact('category'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:50',
            'description' => 'nullable|string|max:150',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:500',
        ]);
        $category = Category::findOrFail($id);

        if ($request->remove_image == '1' && $category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
            $category->image = null;
        }

        if ($request->hasFile('image')) {
            if ($category->image && file_exists(public_path($category->image))) {
                unlink(public_path($category->image));
            }

            $file = $request->file('image');
            $filename = time() . '-' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/categories'), $filename);
            $category->image = 'uploads/categories/' . $filename;
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);
        $category->description = $request->description;
        $category->status = $request->status ?? 0;
        $category->save();

        return redirect()->route('category.index')->with('success', 'Category updated successfully.');
    }


    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->image && file_exists(public_path($category->image))) {
            unlink(public_path($category->image));
        }

        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }
}
