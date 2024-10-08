<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};
use App\Models\{
    Vendor,
    Order,
};

class VendorController extends Controller
{
    public function __construct(
        private Vendor $vendor,
        private Order $order,
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): Factory|View|Application
    {
        
        $queryParam = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $vendors = $this->vendor->where('role','0')->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                    $q->orWhere('id', 'like', "%{$value}%");
                }
            })->orderBy('id', 'DESC');
            $queryParam = ['search' => $request['search']];
        }else{
            $vendors = $this->vendor->where('role','0')->orderBy('id','DESC');
            
        }
        $vendors = $vendors->paginate(Helpers_getPagination())->appends($queryParam);
        
        return view('Admin.views.vendor.list', compact('vendors','search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $vendor = Vendor::find($request->id);
        $vendor->is_block = $request->status;
        $vendor->save();
        flash()->success(translate('Vendor status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function view(Request $request, $id): Factory|View|Application|RedirectResponse
    {
        $vendor = $this->vendor->where('id',$id)->with(['vendororders','Products'])->first();
        
        if (isset($vendor)) {
            $queryParam = [];
            $search = $request['search'];
            if($request->has('search'))
            {
                $key = explode(' ', $request['search']);
                $orders = $this->order->where(['vender_id' => $id])
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_amount', 'like', "%{$value}%");
                        }
                });
                $queryParam = ['search' => $request['search']];
            }else{
                $orders = $this->order->where(['user_id' => $id]);
            }
            $orders = $orders->latest()->paginate(Helpers_getPagination())->appends($queryParam);
            
            return view('Admin.views.vendor.view', compact('vendor', 'orders', 'search'));
        }
        flash()->error(translate('Vendor not found!'));
        return back();
    }
}
