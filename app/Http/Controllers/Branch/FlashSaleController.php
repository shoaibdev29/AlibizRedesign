<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;

use App\Models\BranchFlashSale;
use App\Models\BranchFlashSaleProduct;
use App\Models\Product;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\Foundation\Application;

class FlashSaleController extends Controller
{
    public function __construct(
        private BranchFlashSale $flashSale,
        private Product $product,
        private BranchFlashSaleProduct $flashSaleProduct,
    ) {}

    /**
     * Show flash sales list for branch
     */
    public function index(Request $request)
    {
        $search = $request->input('search', '');
        $queryParam = [];

        $flashSaleQuery = BranchFlashSale::query();

        if (!empty($search)) {
            $keywords = explode(' ', $search);
            $flashSaleQuery->where(function ($q) use ($keywords) {
                foreach ($keywords as $value) {
                    $q->where('title', 'like', "%{$value}%");
                }
            });
            $queryParam['search'] = $search;
        }

        $flashSales = $flashSaleQuery
            ->withCount('products')
            ->latest()
            ->paginate(Helpers::getPagination())
            ->appends($queryParam);

        return view('branch-views.flash-sale.index', [
            'flashSales' => $flashSales,
            'search'     => $search
        ]);
    }


    /**
     * Store a new flash sale in branch table
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ], [
            'title.required' => translate('Title is required'),
        ]);

        $image_name = !empty($request->file('image'))
            ? Helpers::upload('branch-flash-sale/', 'png', $request->file('image'))
            : 'def.png';

        $flashSale = new BranchFlashSale();
        $flashSale->title = $request->title;
        $flashSale->start_date = $request->start_date;
        $flashSale->end_date = $request->end_date;
        $flashSale->status = 0;
        $flashSale->image = $image_name;
        $flashSale->save();

        Toastr::success(translate('Added successfully!'));
        return back();
    }

    /**
     * Update flash sale status (only one active)
     */
    public function status(Request $request): RedirectResponse
    {
        BranchFlashSale::where(['status' => 1])->update(['status' => 0]);

        $flashSale = BranchFlashSale::findOrFail($request->id);
        $flashSale->status = $request->status;
        $flashSale->save();

        Toastr::success(translate('Status updated!'));
        return back();
    }

    /**
     * Edit flash sale
     */
    public function edit($id): View|Factory|Application
    {
        $flashSale = BranchFlashSale::findOrFail($id);
        return view('branch-views.flash-sale.edit', compact('flashSale'));
    }

    /**
     * Update flash sale
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ], [
            'title.required' => translate('Title is required'),
        ]);

        $flashSale = BranchFlashSale::findOrFail($id);
        $flashSale->title = $request->title;
        $flashSale->start_date = $request->start_date;
        $flashSale->end_date = $request->end_date;
        $flashSale->image = $request->hasFile('image')
            ? Helpers::update('branch-flash-sale/', $flashSale->image, 'png', $request->file('image'))
            : $flashSale->image;
        $flashSale->save();

        Toastr::success(translate('Updated successfully!'));
        return redirect()->route('branch.flash-sale.index');
    }

    /**
     * Delete flash sale
     */
    public function delete(Request $request): RedirectResponse
    {
        $flashSale = BranchFlashSale::findOrFail($request->id);

        if (Storage::disk('public')->exists('branch-flash-sale/' . $flashSale->image)) {
            Storage::disk('public')->delete('branch-flash-sale/' . $flashSale->image);
        }

        $productIds = $this->flashSaleProduct
            ->where(['flash_sale_id' => $flashSale->id])
            ->pluck('flash_sale_id');

        $flashSale->delete();
        $this->flashSaleProduct->whereIn('flash_sale_id', $productIds)->delete();

        Toastr::success(translate('Flash sale deleted!'));
        return back();
    }

    /**
     * Add products to a flash sale
     */
    public function addProduct(Request $request, $flash_sale_id): View|Factory|Application
    {
        $queryParam = [];
        $search = $request->get('search', '');

        $flashSale = BranchFlashSale::findOrFail($flash_sale_id);

        $flashSaleProductIds = $this->flashSaleProduct
            ->where('flash_sale_id', $flash_sale_id)
            ->pluck('product_id');

        $flashSaleProductsQuery = $this->product->whereIn('id', $flashSaleProductIds);

        if (!empty($search)) {
            $key = explode(' ', $search);
            $flashSaleProductsQuery->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->where('name', 'like', "%{$value}%");
                }
            });
            $queryParam = ['search' => $search];
        }

        $flashSaleProducts = $flashSaleProductsQuery
            ->paginate(Helpers::getPagination())
            ->appends($queryParam);

        $products = $this->product
            ->active()
            ->whereNotIn('id', $flashSaleProductIds)
            ->orderBy('id', 'DESC')
            ->get();

        return view('branch-views.flash-sale.add-product', compact(
            'products',
            'flashSaleProducts',
            'flash_sale_id',
            'search'
        ));
    }

    /**
     * Add a product to the flash sale session
     */
    public function addProductToSession(Request $request, $flash_sale_id, $product_id): RedirectResponse
    {
        $product = $this->product->findOrFail($product_id);

        $existing = $this->flashSaleProduct
            ->where(['product_id' => $product_id, 'flash_sale_id' => $flash_sale_id])
            ->first();

        if ($existing) {
            Toastr::info($product->name . ' is already exist in this flash sale!');
            return back();
        }

        $selectedProduct = [
            'flash_sale_id' => $flash_sale_id,
            'product_id' => $product->id,
            'name' => $product->name,
            'image' => $product['image_fullpath'][0],
            'price' => $product->price,
            'total_stock' => $product->total_stock,
        ];

        $selectedProducts = $request->session()->get('selected_products', []);
        foreach ($selectedProducts as $existingProduct) {
            if (
                $existingProduct['product_id'] == $selectedProduct['product_id'] &&
                $existingProduct['flash_sale_id'] == $selectedProduct['flash_sale_id']
            ) {
                Toastr::info($existingProduct['name'] . ' is already selected!');
                return back();
            }
        }

        $selectedProducts[] = $selectedProduct;
        $request->session()->put('selected_products', $selectedProducts);

        Toastr::success(translate('Product added successfully!'));
        return back();
    }

    /**
     * Delete one product from session
     */
    public function deleteProductFromSession(Request $request, $flash_sale_id, $product_id): RedirectResponse
    {
        $selectedProducts = $request->session()->get('selected_products', []);

        $selectedProducts = array_values(array_filter($selectedProducts, function ($product) use ($flash_sale_id, $product_id) {
            return !($product['flash_sale_id'] == $flash_sale_id && $product['product_id'] == $product_id);
        }));

        $request->session()->put('selected_products', $selectedProducts);

        Toastr::success(translate('Product deleted successfully!'));
        return back();
    }

    /**
     * Delete all products from a flash sale session
     */
    public function deleteAllProductsFromSession(Request $request, $flash_sale_id): RedirectResponse
    {
        $selectedProducts = $request->session()->get('selected_products', []);

        $selectedProducts = array_values(array_filter($selectedProducts, function ($product) use ($flash_sale_id) {
            return $product['flash_sale_id'] != $flash_sale_id;
        }));

        $request->session()->put('selected_products', $selectedProducts);

        Toastr::success(translate('Reset successfully!'));
        return back();
    }

    /**
     * Save session products into DB
     */
    public function flashSaleProductStore(Request $request, $flash_sale_id): RedirectResponse
    {
        $selectedProducts = $request->session()->get('selected_products', []);

        foreach ($selectedProducts as $key => $selectedProduct) {
            if ($selectedProduct['flash_sale_id'] == $flash_sale_id) {
                $exists = $this->flashSaleProduct
                    ->where([
                        'product_id' => $selectedProduct['product_id'],
                        'flash_sale_id' => $flash_sale_id
                    ])
                    ->first();

                if (!$exists) {
                    BranchFlashSaleProduct::create([
                        'product_id' => $selectedProduct['product_id'],
                        'flash_sale_id' => $flash_sale_id,
                    ]);
                }

                unset($selectedProducts[$key]);
            }
        }

        $request->session()->put('selected_products', array_values($selectedProducts));

        Toastr::success(translate('Product added successfully!'));
        return back();
    }

    /**
     * Delete a product from flash sale in DB
     */
    public function deleteFlashProduct(Request $request, $flash_sale_id, $product_id): RedirectResponse
    {
        $this->flashSaleProduct
            ->where(['product_id' => $product_id, 'flash_sale_id' => $flash_sale_id])
            ->delete();

        Toastr::success(translate('Product deleted successfully!'));
        return back();
    }
}
