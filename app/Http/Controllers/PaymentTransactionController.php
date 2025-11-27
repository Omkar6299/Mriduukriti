<?php

namespace App\Http\Controllers;

use App\Models\OrderPayment;

class PaymentTransactionController extends Controller
{
    /**
     * Display a listing of the payment transactions.
     */
    public function index()
    {
        $transactions = OrderPayment::with(['order.user'])
            ->latest()
            ->get();

        return view('admin_panel.payment_transactions.index', compact('transactions'));
    }
}

