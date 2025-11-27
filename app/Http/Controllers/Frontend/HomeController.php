<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Whistlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function landingPage()
    {
        $category = Category::where('status', 1)->orderBy('created_at', 'DESC')->get();
        $banners = Banner::where('status', 1)->orderBy('created_at', 'DESC')->get();
        $products = Product::with('productSkus')
            ->whereHas('productSkus', function ($query) {
                $query->where('stock_status', 'in_stock');
            })->where('status', 1)->orderBy("created_at", "desc")->paginate(3);

        $whislist = Whistlist::where('user_id', Auth::guard('customer')->id())
            ->pluck('product_id')
            ->toArray();
        return view('frontend.home.index', compact('category', 'products', 'banners', 'whislist'));
    }
}
