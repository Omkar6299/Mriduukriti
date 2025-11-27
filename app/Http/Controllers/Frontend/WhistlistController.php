<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Whistlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhistlistController extends Controller
{
  

    public function toggleWishlist($productId)
    {
        $userId = Auth::guard('customer')->id();

        if (!$userId) {
            return response()->json(['auth' => false]);
        }

        $Whistlist = Whistlist::where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($Whistlist) {
            $Whistlist->delete();
            $WhistlistCount = Whistlist::where('user_id', $userId)->count();

            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Removed from Whistlist',
                'Whistlist_count' => $WhistlistCount
            ]);
        } else {
            Whistlist::create(['user_id' => $userId, 'product_id' => $productId]);
            $WhistlistCount = Whistlist::where('user_id', $userId)->count();

            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Added to Whistlist',
                'Whistlist_count' => $WhistlistCount
            ]);
        }
    }
}
