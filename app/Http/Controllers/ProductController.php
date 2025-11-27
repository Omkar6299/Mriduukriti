<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\ProductSku;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('productSkus')->orderBy('id', 'desc')->get();
        // dd($products->toArray());
        return view('admin_panel.products.index', compact('products'));
    }

    public function create()
    {
        $category = Category::where('status', 1)->orderBy('name', 'ASC')->get();
        $attributes = ProductAttribute::get();
        return view('admin_panel.products.create', compact('category', 'attributes'));
    }

    public function getSucategoryByCategory(Request $request)
    {
        $subcategory = SubCategory::where('category_id', $request->category_id)->get();
        return response()->json($subcategory);
    }


    public function store(Request $request)
    {
        $validateReq =  $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'quantity' => 'required|integer',
            'stock_status' => 'required|in:in_stock,out_of_stock,backorder',
            'short_description' => 'required',
            'description' => 'required',
            'weight' => 'required|numeric',
            'length' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'attributes' => 'required|array',
            'attributes.*.id' => 'required|exists:product_attributes,id',
            'attributes.*.value' => 'required|string|max:255',
            'image' => 'required|image',
        ]);

        DB::beginTransaction();

        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '-' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('uploads/products');
                $image->move($destinationPath, $imageName);
                $imagePath = 'uploads/products/' . $imageName;
            }

            // Create product
            $product = Product::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'short_description' => $request->short_description,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->subcategory_id,
                'status' => $request->status ?? 0,
            ]);

            // Generate SKU code
            $skuPrefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $request->name), 0, 3));
            $skuCode = $skuPrefix . '-' . rand(11111, 99999);

            // Create SKU record
            $productSku = ProductSku::create([
                'product_id' => $product->id,
                'sku_code' => $skuCode,
                'price' => $request->price,
                'sale_price' => $request->sale_price,
                'stock_quantity' => $request->quantity,
                'stock_status' => $request->stock_status,
                'weight' => $request->weight,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
                'image' => $imagePath,
            ]);

            foreach ($validateReq['attributes'] as $attr) {
                ProductAttributeValue::create([
                    'product_id' => $product->id,
                    'product_sku_id' => $productSku->id,
                    'attribute_id' => $attr['id'],
                    'value' => $attr['value'],
                ]);
            }




            DB::commit();

            return redirect()->route('products.index')->with('success', 'Product created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $product = Product::with('productSkus')->find($id);
        $category = Category::where('status', 1)->orderBy('name', 'ASC')->get();
        $attributes = ProductAttribute::get();
        $attributesValue = ProductAttributeValue::where('product_id', $id)->get();
        return view('admin_panel.products.edit', compact('product', 'category', 'attributes', 'attributesValue'));
    }

    public function update(Request $request, $id)
    {
        $validateReq = $request->validate([
            'name' => 'required',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'price' => 'required|numeric',
            'sale_price' => 'required|numeric',
            'quantity' => 'required|integer',
            'stock_status' => 'required|in:in_stock,out_of_stock,backorder',
            'short_description' => 'required',
            'description' => 'required',
            'weight' => 'required|numeric',
            'length' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric',
            'attributes' => 'required|array',
            'attributes.*.id' => 'required|exists:product_attributes,id',
            'attributes.*.value' => 'required|string|max:255',
            'image' => 'nullable|image', // image optional
        ]);
        // dd($request->all(), $id);
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);
            $productSku = $product->productSkus()->first();

            // Handle image upload (optional)
            $imagePath = $productSku->image ?? null;
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($imagePath && file_exists(public_path($imagePath))) {
                    unlink(public_path($imagePath));
                }
                $image = $request->file('image');
                $imageName = time() . '-' . Str::slug($request->name) . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('uploads/products');
                $image->move($destinationPath, $imageName);
                $imagePath = 'uploads/products/' . $imageName;
            }

            // Update product
            $product->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'short_description' => $request->short_description,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'sub_category_id' => $request->subcategory_id,
                'status' => $request->status ?? 0,
            ]);

            // Update SKU
            $productSku->update([
                'price' => $request->price,
                'sale_price' => $request->sale_price,
                'stock_quantity' => $request->quantity,
                'stock_status' => $request->stock_status,
                'weight' => $request->weight,
                'length' => $request->length,
                'width' => $request->width,
                'height' => $request->height,
                'image' => $imagePath,
            ]);

            // Update attributes (delete old, insert new)
            $deleted = ProductAttributeValue::where('product_id', $id)->delete();

            // Insert new ones
            foreach ($validateReq['attributes'] as $attr) {
                ProductAttributeValue::create([
                    'product_id'      => $product->id,
                    'product_sku_id'  => $productSku->id,
                    'attribute_id'    => $attr['id'],
                    'value'           => $attr['value'],
                ]);
            }

            DB::commit();

            return redirect()->route('products.index')->with('success', 'Product updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $skus = ProductSku::where('product_id', $id)->get();

            foreach ($skus as $sku) {
                if ($sku->image && file_exists(public_path($sku->image))) {
                    unlink(public_path($sku->image));
                }
            }

            ProductAttributeValue::where('product_id', $id)->delete();

            ProductSku::where('product_id', $id)->delete();

            Product::where('id', $id)->delete();

            DB::commit();

            return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
