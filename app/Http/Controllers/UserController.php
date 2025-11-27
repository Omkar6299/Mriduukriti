<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $user = User::where('role', 'Customer')->get();
        return view('admin_panel.users.index', compact('user'));
    }
}
