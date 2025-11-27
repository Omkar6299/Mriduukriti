<?php

namespace App\Http\Controllers;

use App\Models\ProductAttribute;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = ProductAttribute::orderBy('name', 'ASC')->get();
        return view('admin_panel.attribute.index', compact('attributes'));
    }
    public function create()
    {
        return view('admin_panel.attribute.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:50',
            'type' => 'required|in:text,select,multiselect',
            'is_variant' => 'nullable',
        ]);
        // dd($validated);
        ProductAttribute::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'type' => $validated['type'],
            'is_variant' => $request->has('is_variant'),
        ]);
        return redirect()->route('attribute.index')->with('success', 'Attribute created successfully.');
    }

    public function edit($id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        return view('admin_panel.attribute.edit', compact('attribute'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|max:50',
            'type' => 'required|in:text,select,multiselect',
            'is_variant' => 'nullable',
        ]);

        $attribute = ProductAttribute::findOrFail($id);

        $attribute->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'type' => $validated['type'],
            'is_variant' => $request->has('is_variant'),
        ]);

        return redirect()->route('attribute.index')->with('success', 'Attribute updated successfully.');
    }

    public function destroy($id)
    {
        $attribute = ProductAttribute::findOrFail($id);
        $attribute->delete();
        return redirect()->back()->with('success', 'Attribute deleted successfully.');
    }
}
