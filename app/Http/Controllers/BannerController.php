<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('created_at', 'DESC')->get();
        return view('admin_panel.banner.index', compact('banners'));
    }
    public function create()
    {
        return view('admin_panel.banner.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|max:30',
            'link'        => 'nullable|url',
            'description' => 'nullable|max:255',
            'image'       => 'required|mimes:jpg,jpeg,png,webp|max:600',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '-' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/banners');
            $image->move($destinationPath, $imageName);
            $imagePath = 'uploads/banners/' . $imageName;
        }
        Banner::create([
            'title' => $request->title,
            'paragraph' => $request->description,
            'link' => $request->link,
            'bannner' => $imagePath,
            'status' => $request->status ?? 0,
        ]);
        return redirect()->route('banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit($id)
    {
        $banner = Banner::find($id);
        return view('admin_panel.banner.edit', compact('banner'));
    }
    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $request->validate([
            'title'       => 'required|max:30',
            'link'        => 'nullable|url',
            'description' => 'nullable|max:255',
            'image'       => 'nullable|mimes:jpg,jpeg,png,webp|max:600',
        ]);

        $imagePath = $banner->bannner;

        if ($request->hasFile('image')) {
            if ($banner->bannner && file_exists(public_path($banner->bannner))) {
                unlink(public_path($banner->bannner));
            }

            $image = $request->file('image');
            $imageName = time() . '-' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('uploads/banners');
            $image->move($destinationPath, $imageName);
            $imagePath = 'uploads/banners/' . $imageName;
        }

        $banner->update([
            'title'     => $request->title,
            'paragraph' => $request->description,
            'link'      => $request->link,
            'bannner'   => $imagePath,
            'status'    => $request->status ?? 0,
        ]);

        return redirect()->route('banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);

        if ($banner->bannner && file_exists(public_path($banner->bannner))) {
            unlink(public_path($banner->bannner));
        }

        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Banner deleted successfully.');
    }
}
