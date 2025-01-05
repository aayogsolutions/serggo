<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};

class AmcController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return View|Factory|Application
     */
    public function List(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = Products::whereNotIn('status',[2])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->latest();
            $queryParam = ['search' => $request['search']];
        }else{
            $query = Products::whereNotIn('status',[2])->latest();
        }
        $products = $query->with('order_details.OrderDetails')->paginate(Helpers_getPagination())->appends($queryParam);
        
        return view('Admin.views.amc.plan.list',compact('products','search'));
    }
}
