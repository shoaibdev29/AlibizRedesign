<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BranchBanner;
use App\Models\Category;
use App\Models\BranchProduct;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class BranchBannerController extends Controller
{
    /**
     * Display all banners
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = BranchBanner::query();

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $banners = $query->latest()
            ->paginate(Helpers::getPagination())
            ->appends(['search' => $search]);

        $products = BranchProduct::orderBy('name')->get();
        $categories = Category::where('parent_id', 0)->orderBy('name')->get();

        return view('branch-views.banner.index', compact('banners', 'products', 'categories', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
            'banner_type' => 'required|in:primary,secondary',
            'item_type' => 'required|in:product,category',
            'primary_image' => 'required_if:banner_type,primary|image|max:2048',
            'secondary_image' => 'required_if:banner_type,secondary|image|max:2048',
        ]);

        $banner = new BranchBanner();
        $banner->title = $request->title;
        $banner->banner_type = $request->banner_type;
        $banner->product_id = $request->item_type === 'product' ? $request->product_id : null;
        $banner->category_id = $request->item_type === 'category' ? $request->category_id : null;

        if ($request->banner_type === 'primary' && $request->hasFile('primary_image')) {
            $banner->image = Helpers::upload('branch/banner/', 'png', $request->file('primary_image'));
        } elseif ($request->banner_type === 'secondary' && $request->hasFile('secondary_image')) {
            $banner->image = Helpers::upload('branch/banner/', 'png', $request->file('secondary_image'));
        }

        $banner->save();
        Toastr::success(translate('Banner added successfully!'));
        return back();
    }

    public function edit($id)
    {
        $banner = BranchBanner::findOrFail($id);
        $products = BranchProduct::orderBy('name')->get();
        $categories = Category::where('parent_id', 0)->orderBy('name')->get();

        return view('branch-views.banner.edit', compact('banner', 'products', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|max:255',
            'banner_type' => 'required|in:primary,secondary',
            'item_type' => 'required|in:product,category',
        ]);

        $banner = BranchBanner::findOrFail($id);
        $banner->title = $request->title;
        $banner->banner_type = $request->banner_type;
        $banner->product_id = $request->item_type === 'product' ? $request->product_id : null;
        $banner->category_id = $request->item_type === 'category' ? $request->category_id : null;

        if ($request->banner_type === 'primary' && $request->hasFile('primary_image')) {
            $banner->image = Helpers::update('branch/banner/', $banner->image, 'png', $request->file('primary_image'));
        } elseif ($request->banner_type === 'secondary' && $request->hasFile('secondary_image')) {
            $banner->image = Helpers::update('branch/banner/', $banner->image, 'png', $request->file('secondary_image'));
        }

        $banner->save();
        Toastr::success(translate('Banner updated successfully!'));
        return back();
    }

    public function delete($id)
    {
        $banner = BranchBanner::findOrFail($id);
        if ($banner->image) {
            Helpers::delete('branch/banner/' . $banner->image);
        }
        $banner->delete();

        Toastr::success(translate('Banner removed!'));
        return back();
    }

    public function status($id, $status)
    {
        $banner = BranchBanner::findOrFail($id);
        $banner->status = $status;
        $banner->save();

        Toastr::success(translate('Banner status updated!'));
        return back();
    }
}
