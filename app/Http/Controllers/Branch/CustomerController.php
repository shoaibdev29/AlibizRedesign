<?php

namespace App\Http\Controllers\Branch;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\BranchUser;
use App\Models\BranchNewsletter;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Rap2hpoutre\FastExcel\FastExcel;

class CustomerController extends Controller
{
    public function __construct(
        private BranchNewsletter $newsletter,
        private Order $order,
        private BranchUser $user
    ){ }

    public function customerList(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        $customers = $this->user;

        if ($request->has('search')) {
            $key = explode(' ', $search);
            $customers = $customers->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                      ->orWhere('l_name', 'like', "%{$value}%")
                      ->orWhere('email', 'like', "%{$value}%")
                      ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $queryParam = ['search' => $search];
        }

        $customers = $customers->with(['orders'])->latest()->paginate(Helpers::pagination_limit())->appends($queryParam);
        return view('branch-views.customer.list', compact('customers', 'search'));
    }

    public function view($id, Request $request): View|Factory|RedirectResponse|Application
    {
        $search = $request->search;
        $customer = $this->user->find($id);

        if ($customer) {
            $orders = $this->order->latest()
                ->where('user_id', $id)
                ->when($search, function ($query) use ($search) {
                    $key = explode(' ', $search);
                    foreach ($key as $value) {
                        $query->where('id', 'like', "%$value%");
                    }
                })
                ->paginate(Helpers::getPagination())
                ->appends(['search' => $search]);

            return view('branch-views.customer.customer-view', compact('customer', 'orders', 'search'));
        }

        Toastr::error(translate('Customer not found!'));
        return back();
    }

    public function subscribedEmails(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        $newsletters = $this->newsletter;

        if ($request->has('search')) {
            $key = explode(' ', $search);
            $newsletters = $newsletters->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('email', 'like', "%{$value}%");
                }
            });
            $queryParam = ['search' => $search];
        }

        $newsletters = $newsletters->latest()->paginate(Helpers::getPagination())->appends($queryParam);
        return view('branch-views.customer.subscribed-list', compact('newsletters', 'search'));
    }

    public function exportSubscribedEmails(Request $request)
    {
        $newsletters = $this->newsletter;

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $newsletters = $newsletters->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('email', 'like', "%{$value}%");
                }
            });
        }

        $newsletters = $newsletters->latest()->get();

        $data = [];
        foreach ($newsletters as $key => $newsletter) {
            $data[] = [
                'SL' => ++$key,
                'Email' => $newsletter->email,
                'Subscribe At' => date('d M Y h:i A', strtotime($newsletter['created_at'])),
            ];
        }

        return (new FastExcel($data))->download('branch-subscribe-email.xlsx');
    }
}
