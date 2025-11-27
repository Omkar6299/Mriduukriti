<?php

namespace App\Http\Controllers;

use App\Models\NewsLetter;
use Illuminate\Http\Request;

class NewsLetterController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'subscriber_email' => 'required|email|unique:news_letters,subscriber_email',
        ]);

        NewsLetter::create([
            'subscriber_email' => $request->subscriber_email,
            'status' => 1,
        ]);

        return back()->with('success_subcriber', 'You have subscribed successfully!');
    }
}
