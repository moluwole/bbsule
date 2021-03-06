<?php

namespace App\Http\Controllers;

use App\Category;
use App\Gallery;
use Illuminate\Http\Request;
use Session;

class PictureController extends Controller
{
    /**
     * @param Category $category
     */
    public function index(Category $category)
    {
        $categories = $category->all();
        return view('dashboard.product-edit', compact('categories'));
    }

    public function home(Gallery $gallery)
    {
        $galleries = $gallery->get()->first();
        return view('dashboard.home', compact('galleries'));
    }

    /**
     * @param Request $request
     * @param Gallery $gallery
     */
    public function store(Request $request, Gallery $gallery)
    {
        // $this->validate($request, [
        //     'upload' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        // ]);

        $images = $request->file('upload');
        $store = [];
        $combine = '';
        foreach ($images as $image) {
            $imagename = time() . '.' . str_replace(' ', '-', $image->getClientOriginalName()) . $image->getClientOriginalExtension();
            $destinationPath = public_path('/gallery');
            $image->move($destinationPath, $imagename);
            // array_push($store, $imagename);
            $combine .= $imagename . ',';
            echo "<div class=\"col-md-3\"><img src=\"/gallery/{$imagename}\" class=\"image\" style=\"border:1px solid #ccc;padding:2px;\" /></div>";
        }
        $last_id = $gallery->create(['user_id' => 'admin', 'image_path' => $combine]);
        session(['last_id' => $last_id->id]);
        // return response()->json(["images" => $store]);
    }

    /**
     * @param Request $request
     * @param Gallery $gallery
     */
    public function create(Request $request, Gallery $gallery)
    {
        $data = $request->all();
        $id = session('last_id');
        $findorfail = $gallery->find($id);
        $findorfail->fill($data)->save();
        return back()->with('error', 'Image Added Successfully')->withInput();
    }

    public function gallery(Gallery $gallery)
    {
        $galleries = $gallery->all();
        return view('dashboard.gallery', compact('galleries'));
    }
}
