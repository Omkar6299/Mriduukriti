<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Whistlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('productSkus');


        if ($request->filled('category')) {
            $categories = (array) $request->category;
            $products->whereIn('category_id', $categories);
        }

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $minPrice = $request->min_price;
            $maxPrice = $request->max_price;

            $products->whereHas('productSkus', function ($query) use ($minPrice, $maxPrice) {
                $query->whereBetween('sale_price', [$minPrice, $maxPrice]);
            });
        }
     


        $products = $products->where('status', 1)->orderBy("created_at", "desc")->paginate(6);
        $category = Category::where('status', 1)->orderBy('created_at', 'DESC')->get();
        if ($request->ajax()) {
            return view('frontend.shop.partials.products', compact('products'))->render();
        }

          $whislist = Whistlist::where('user_id', Auth::guard('customer')->id())
        ->pluck('product_id')
        ->toArray();

        // $whislist = Whistlist::where('user_id', Auth::guard('customer')->id())->get();

        return view('frontend.shop.list', compact('products', 'category','whislist'));
    }

    public function productPage($product_slug)
    {
        $product = Product::with('productSkus')->where('slug', $product_slug)->first();
        $category = Category::where('id', $product->category_id)->first();
        return view('frontend.shop.product_page', compact('product', 'category'));
    }
}
