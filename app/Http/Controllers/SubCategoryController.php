<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class SubCategoryController extends Controller
{
    public function index()
    {
        $subcategories = SubCategory::with('parentCategory')->orderBy('created_at', 'DESC')->get();
        // dd($subcategories->toArray());
        return view('admin_panel.sub_category.index', compact('subcategories'));
    }

    public function create()
    {
        $category = Category::where('status', 1)->orderBy('name', 'ASC')->get();
        return view('admin_panel.sub_category.create', compact('category'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|max:50',
            'description' => 'nullable|string|max:150',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:500',
        ]);
        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/subcategories');
            $image->move($destinationPath, $imageName);
            $imagePath = 'uploads/subcategories/' . $imageName;
        }
        SubCategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status ?? 0,
            'image' => $imagePath,
        ]);
        return redirect()->route('sub_category.index')->with('success', 'Sub Category created successfully.');
    }
    public function edit($id)
    {
        $subcategory = SubCategory::findOrFail($id);
        $category = Category::all();
        return view('admin_panel.sub_category.edit', compact('subcategory', 'category'));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required',
            'name' => 'required|max:50',
            'description' => 'nullable|string|max:150',
            'image' => 'nullable|mimes:jpg,jpeg,png,webp|max:500',
        ]);

        $subcategory = SubCategory::findOrFail($id);

        // Handle image upload
        $imagePath = $subcategory->image;
        if ($request->hasFile('image')) {
            // Delete old image
            if ($subcategory->image && file_exists(public_path($subcategory->image))) {
                unlink(public_path($subcategory->image));
            }

            $image = $request->file('image');
            $imageName = time() . '-' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/subcategories');
            $image->move($destinationPath, $imageName);
            $imagePath = 'uploads/subcategories/' . $imageName;
        }

        // Update the subcategory
        $subcategory->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'status' => $request->status ?? 0,
            'image' => $imagePath,
        ]);

        return redirect()->route('sub_category.index')->with('success', 'Sub Category updated successfully.');
    }
    public function destroy($id)
    {
        $subcategory = SubCategory::findOrFail($id);

        if ($subcategory->image && file_exists(public_path($subcategory->image))) {
            unlink(public_path($subcategory->image));
        }

        $subcategory->delete();

        return redirect()->route('sub_category.index')->with('success', 'Sub Category deleted successfully.');
    }
}
