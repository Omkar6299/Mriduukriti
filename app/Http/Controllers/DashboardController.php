<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $todaySales = Order::where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->sum('grand_total');

        $todaySaleAmount = formatIndianCurrency($todaySales);

        
        $totalSaleAmounts = Order::where('payment_status', 'paid')->sum('grand_total');
        $totalSaleAmount = formatIndianCurrency($totalSaleAmounts);

        $totalUsers = User::whereDate('created_at', today())->count();

        

        return view('admin_panel.home.index', compact('totalSaleAmount', 'todaySaleAmount','totalUsers'));
    }
}
