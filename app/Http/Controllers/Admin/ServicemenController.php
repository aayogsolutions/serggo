<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};
use App\Models\{
    Vendor,
    Order
};

class ServicemenController extends Controller
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
            $service_mens = $this->vendor->where('role','1')->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                    $q->orWhere('id', 'like', "%{$value}%");
                }
            })->orderBy('id', 'desc');
            $queryParam = ['search' => $request['search']];
        }else{
            $service_mens = $this->vendor->where('role','1')->orderBy('id', 'desc');
        }
        $service_mens = $service_mens->paginate(Helpers_getPagination())->appends($queryParam);
        
        return view('Admin.views.service_men.list', compact('service_mens','search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $service_mens = Vendor::find($request->id);
        $service_mens->is_block = $request->status;
        $service_mens->save();
        flash()->success(translate('Service_men status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function view(Request $request, $id): Factory|View|Application|RedirectResponse
    {
        $service_mens = $this->vendor->where('id',$id)->with('vendororders')->first();
        if (isset($service_mens)) {
            $queryParam = [];
            $search = $request['search'];
            if($request->has('search'))
            {
                $key = explode(' ', $request['search']);
                $orders = $this->order->where(['service_man_id' => $id])
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
            
            return view('Admin.views.service_men.view', compact('service_mens', 'orders', 'search'));
        }
        flash()->error(translate('Service_men not found!'));
        return back();
    }
}
