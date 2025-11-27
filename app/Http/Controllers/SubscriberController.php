<?php

namespace App\Http\Controllers;

use App\Models\NewsLetter;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(){
        $subscribers = NewsLetter::orderBy('created_at', 'DESC')->get();
        return view('admin_panel.subscriber.index', compact('subscribers'));
    }
}
