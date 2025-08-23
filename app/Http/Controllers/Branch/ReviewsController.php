<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\BranchProduct;
use App\Models\BranchReview;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
    public function __construct(
        private BranchProduct $product,
        private BranchReview $review
    ) {
    }

    /**
     * Display a listing of the reviews for branch products.
     *
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): View|Factory|Application
    {
        $branchId = Auth::guard('branch')->id();



        $queryParam = [];
        $search = $request['search'];

        // Get only branch-owned products
        $branchProducts = $this->product->where('id', $branchId);

        if ($request->has('search')) {
            $key = explode(' ', $search);
            $branchProducts = $branchProducts->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $queryParam = ['search' => $search];
        }

        $products = $branchProducts->pluck('id')->toArray();

        $reviews = $this->review->where('branch_id', $branchId)
            ->with(['product', 'customer'])
            ->latest()
            ->paginate(Helpers::pagination_limit());

        return view('branch-views.reviews.list', compact('reviews', 'search'));
    }

}
