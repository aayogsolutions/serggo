<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\CustomerLogic;
use App\CentralLogics\Helpers;
use App\Models\Products;
use App\Models\ProductReview;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use SebastianBergmann\Type\NullType;

class ReviewsController extends Controller
{
    public function __construct(
        private Products $product,
        private ProductReview $review
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $products = $this->product->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('id', 'like', "%{$value}%");
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                })->pluck('id')->toArray();

            $reviews = $this->review->whereIn('product_id',$products);
            $queryParam = ['search' => $request['search']];
        }else{
            $reviews = $this->review->with(['product','customer']);
        }
         $reviews = $reviews->latest()->paginate(Helpers_getPagination())->appends($queryParam);
        return view('Admin.views.reviews.list',compact('reviews','search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $reviews = $this->review->find($request->id);
        $reviews->is_active = $request->status;
        $reviews->save();
        flash()->success(translate('Review status updated!'));
        return back();
    }
}
