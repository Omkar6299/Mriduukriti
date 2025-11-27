<?php

namespace App\Http\Controllers;

use App\Models\CustomerSupport;
use App\Models\SocialProfiles;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $customerSupport = CustomerSupport::where('user_id', auth()->user()->id)->first();
        $socialProfiles = SocialProfiles::where('user_id', auth()->user()->id)->first();
        return view('admin_panel.setting.support_and_social.index', compact('customerSupport','socialProfiles'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'email'  => 'nullable|email|unique:customer_supports,email,' . auth()->id() . ',user_id',
            'contact' => 'nullable|digits:10',
        ]);

        // 🔹 Customer Support - update or create
        CustomerSupport::updateOrCreate(
            ['user_id' => auth()->id()], // check if record exists for this user
            [
                'email'  => $request->email,
                'contact' => $request->contact,
            ]
        );

        // 🔹 Social Profiles - update or create
        SocialProfiles::updateOrCreate(
            ['user_id' => auth()->id()], // check if record exists for this user
            [
                'facebook_link'  => $request->facebook,
                'twitter_link'   => $request->twitter,
                'instagram_link' => $request->instagram,
                'linkedin_link'  => $request->linkedin,
            ]
        );

        return back()->with('success', 'Customer support & social profiles saved successfully!');
    }
}
