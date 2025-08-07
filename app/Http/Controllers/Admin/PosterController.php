<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
// use App\Model\Banner;
use App\Models\Poster;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Models\Category;


class PosterController extends Controller
{
    function list(Request $request)
    {
       
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $banners = Poster::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('banner_type', 'like', "%{$value}%");
                }
            })->orderBy('id', 'desc');
            $query_param = ['search' => $request['search']];
        } else {
            $banners = Poster::orderBy('id', 'desc');
        }
        $banners = $banners->paginate(Helpers::pagination_limit())->appends($query_param);
        $categories=Category::all();
        return view('admin-views.poster.view', compact('banners', 'search','categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'image' => 'required',
        ], [
            'title.required' => 'title is required!',
            'image.required' => 'Image is required!',

        ]);

        $banner = new Poster;
        $banner->blog_category = $request->blog_category;
        $banner->title = $request->title;
        $banner->details = $request->details;
        $banner->added_by = 'Admin';
        $banner->image = ImageManager::upload('poster/', 'png', $request->file('image'));
        $banner->save();
        Toastr::success(translate('banner_added_successfully'));
        return back();
    }

    public function status(Request $request)
    {
        if ($request->ajax()) {
            $banner = Poster::find($request->id);
            $banner->published = $request->status ?? 0;
            $banner->save();
            $data = $request->status ?? 0;
            return response()->json($data);
        }
    }

    public function edit($id)
    {
        $banner = Poster::where('id', $id)->first();
        $categories=Category::all();
        return view('admin-views.poster.edit', compact('banner','categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
        ], [
            'title.required' => 'title is required!',
        ]);

        $banner = Poster::find($id);
        $banner->blog_category = $request->blog_category;
        $banner->title = $request->title;
        $banner->details = $request->details;
        if ($request->file('image')) {
            $banner->image = ImageManager::update('poster/', $banner['photo'], 'png', $request->file('image'));
        }
        $banner->save();

        Toastr::success(translate('banner_updated_successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $br = Poster::find($request->id);
        ImageManager::delete('/poster/' . $br['photo']);
        Poster::where('id', $request->id)->delete();
        return response()->json();
    }
}
