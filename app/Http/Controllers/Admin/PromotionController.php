<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Promotion;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function listing()
    {
        $promotions = Promotion::all();
        return view('admin.promotions.listing', compact('promotions'));
    }

    public function index()
    {
        $promotions = Promotion::all();
        return view('promotions', compact('promotions'));
    }

    public function create()
    {
        return view('admin.promotions.create');
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|image|mimes:png,jpg,jpeg',
            'display_on_machine' => 'boolean'
        ]);
        if ($request->hasFile('image')) {
            $profileImage = $request->file('image');
            $uploadFolder = 'promotions';
            $filename = $profileImage->getClientOriginalName();
            $profileImage->storeAs($uploadFolder, $filename, 'public');
            $validated['image'] = $uploadFolder . '/' . $filename;
        }
        Promotion::create($validated);
        return redirect("/admin/promotions")
            ->withSuccess('Promotion created successfully');
    }

    public function edit(Request $request, $id)
    {
        $promotion = Promotion::find($id);
        return view('admin.promotions.edit', compact('promotion'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:png,jpg,jpeg',
            'display_on_machine' => 'boolean'
        ]);
        if ($request->hasFile('image')) {
            $profileImage = $request->file('image');
            $uploadFolder = 'promotions';
            $filename = $profileImage->getClientOriginalName();
            $profileImage->storeAs($uploadFolder, $filename, 'public');
            $image = $uploadFolder . '/' . $filename;

            Promotion::where('id', $id)->update([
                "title" => $validated['title'],
                "description" => $validated['description'],
                "image" => $image,
                "display_on_machine" => $validated['display_on_machine'],
            ]);
        }
        Promotion::where('id', $id)->update([
            "title" => $validated['title'],
            "description" => $validated['description'],
            "display_on_machine" => $validated['display_on_machine'] ?? 0,
        ]);

        return redirect("/admin/promotions")
            ->withSuccess('Promotion updated successfully');
    }

    public function delete(Request $request, $id)
    {
        $promotion = Promotion::find($id);
        $promotion->delete();
        return redirect("/admin/promotions")
        ->withSuccess('Promotion deleted successfully');
    }
}
