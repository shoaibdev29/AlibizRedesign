<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BranchCategory;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BranchCategoryController extends Controller
{
    public function __construct(
        private BranchCategory $category,
        private Translation $translation
    ) {
    }

    /**
     * List main branch categories
     */
    public function index(Request $request): Factory|View|Application
    {
        $queryParam = [];
        $search = $request['search'] ?? null;

        $categories = $this->category->where(['position' => 0]);

        if ($search) {
            $keys = explode(' ', $search);
            $categories = $categories->where(function ($q) use ($keys) {
                foreach ($keys as $key) {
                    $q->orWhere('name', 'like', "%{$key}%");
                }
            });
            $queryParam = ['search' => $search];
        }

        $categories = $categories->latest()->paginate(Helpers::getPagination())->appends($queryParam);

        return view('branch-views.category.index', compact('categories', 'search'));
    }

    /**
     * List subcategories
     */
    public function subIndex(Request $request): Factory|View|Application
    {
        $queryParam = [];
        $search = $request['search'] ?? null;

        $categories = $this->category->with('parent')->where(['position' => 1]);

        if ($search) {
            $keys = explode(' ', $search);
            $categories = $categories->where(function ($q) use ($keys) {
                foreach ($keys as $key) {
                    $q->orWhere('name', 'like', "%{$key}%");
                }
            });
            $queryParam = ['search' => $search];
        }

        $categories = $categories->latest()->paginate(Helpers::getPagination())->appends($queryParam);

        return view('branch-views.category.sub-index', compact('categories', 'search'));
    }

    /**
     * List sub-subcategories view
     */
    public function subSubIndex(): Factory|View|Application
    {
        return view('branch-views.category.sub-sub-index');
    }

    /**
     * Store new branch category or subcategory
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);

        foreach ($request->name as $name) {
            if (strlen($name) > 255) {
                Toastr::error(translate('Name is too long!'));
                return back();
            }
        }

        $existing = $this->category
            ->where('name', $request->name[array_search('en', $request->lang)])
            ->where('parent_id', $request->parent_id ?? 0)
            ->first();

        if ($existing) {
            Toastr::error(translate(($request->parent_id == null ? 'Category' : 'Sub-category') . ' already exists!'));
            return back();
        }

        $image_name = $request->file('image') ? Helpers::upload('branch-category/', 'png', $request->file('image')) : 'def.png';
        $banner_image_name = $request->file('banner_image') ? Helpers::upload('branch-category/banner/', 'png', $request->file('banner_image')) : 'def.png';

        $category = new BranchCategory();
        $category->name = $request->name[array_search('en', $request->lang)];
        $category->image = $image_name;
        $category->banner_image = $banner_image_name;
        $category->parent_id = $request->parent_id ?? 0;
        $category->position = $request->position;
        $category->save();

        // Save translations
        $data = [];
        foreach ($request->lang as $index => $key) {
            if ($key != 'en' && !empty($request->name[$index])) {
                $data[] = [
                    'translationable_type' => BranchCategory::class,
                    'translationable_id' => $category->id,
                    'locale' => $key,
                    'key' => 'name',
                    'value' => $request->name[$index],
                ];
            }
        }
        if ($data) {
            $this->translation->insert($data);
        }

        Toastr::success($request->parent_id == 0 ? translate('Category Added Successfully!') : translate('Sub Category Added Successfully!'));
        return back();
    }

    /**
     * Edit branch category
     */
    public function edit($id): Factory|View|Application
    {
        $category = $this->category->withoutGlobalScopes()->with('translations')->findOrFail($id);
        return view('branch-views.category.edit', compact('category'));
    }

    /**
     * Update category status
     */
    public function status(Request $request): RedirectResponse
    {
        $category = $this->category->findOrFail($request->id);
        $category->status = $request->status;
        $category->save();
        Toastr::success($category->parent_id == 0 ? translate('Category status updated!') : translate('Sub Category status updated!'));
        return back();
    }

    /**
     * Update branch category
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
        ]);

        foreach ($request->name as $name) {
            if (strlen($name) > 255) {
                Toastr::error(translate('Name is too long!'));
                return back();
            }
        }

        $category = $this->category->findOrFail($id);
        $category->name = $request->name[array_search('en', $request->lang)];
        $category->parent_id = $request->parent_id ?? 0;
        $category->image = $request->hasFile('image') ? Helpers::update('branch-category/', $category->image, 'png', $request->file('image')) : $category->image;
        $category->banner_image = $request->hasFile('banner_image') ? Helpers::update('branch-category/banner/', $category->banner_image, 'png', $request->file('banner_image')) : $category->banner_image;
        $category->save();

        // Update translations
        foreach ($request->lang as $index => $key) {
            if ($key != 'en' && !empty($request->name[$index])) {
                $this->translation->updateOrInsert(
                    [
                        'translationable_type' => BranchCategory::class,
                        'translationable_id' => $category->id,
                        'locale' => $key,
                        'key' => 'name'
                    ],
                    ['value' => $request->name[$index]]
                );
            }
        }

        Toastr::success($category->parent_id == 0 ? translate('Category updated successfully!') : translate('Sub Category updated successfully!'));
        return back();
    }

    /**
     * Delete branch category
     */
    public function delete($id): RedirectResponse
    {
        $category = $this->category->findOrFail($id);

        if ($category->childes->count() === 0) {
            if (Storage::disk('public')->exists('branch-category/' . $category->image)) {
                Storage::disk('public')->delete('branch-category/' . $category->image);
            }
            $category->delete();
            Toastr::success($category->parent_id == 0 ? translate('Category removed!') : translate('Sub Category removed!'));
        } else {
            Toastr::warning($category->parent_id == 0 ? translate('Remove subcategories first!') : translate('Remove subcategories first!'));
        }

        return back();
    }


    /**
     * Update featured status
     */
    public function featured(Request $request): RedirectResponse
    {
        $category = $this->category->findOrFail($request->id);
        $category->is_featured = $request->featured;
        $category->save();
        Toastr::success(translate('Featured status updated!'));
        return back();
    }
}
