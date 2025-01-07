<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AMCPlan;
use App\Models\AMCPlanServices;
use App\Models\Products;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

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
            $query = AMCPlan::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->latest();
            $queryParam = ['search' => $request['search']];
        }else{
            $query = AMCPlan::latest();
        }
        $products = $query->paginate(Helpers_getPagination())->appends($queryParam);
        
        return view('Admin.views.amc.plan.list',compact('products','search'));
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @return View|Factory|Application
     */
    public function index(): View|Factory|Application
    {
        $services = Service::where('status',0)->get();

        return view('Admin.views.amc.plan.index',compact('services'));
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'service' => 'required',
            'quantity' => 'required',
            'price' => 'required|numeric|min:0',
            'duration' => 'required',
        ], [
            'name.required' => translate('Product name is required!'),
            'service.required' => translate('Service is required!'),
            'quantity.required' => translate('Quantity is required!'),
            'price.required' => translate('Price is required!'),
            'duration.required' => translate('Duration is required!'),
        ]);

        $imageNames = [];
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $imageData = Helpers_upload('Images/amcImages/', $img->getClientOriginalExtension() , $img);
                $imageNames[] = $imageData;
            }
            $imageData = json_encode($imageNames);
        } else {
            $imageData = json_encode([]);
        }

        $product = new AMCPlan();
        $product->name = $request->name;
        $product->description = $request->description;
        $product->image = $imageData;
        $product->duration = $request->duration;
        $product->price = $request->price;
        $product->tax = $request->tax;
        $product->discount = $request->discount;
        $product->save();

        foreach ($request->service as $key => $value) {
            $amcdata = new AMCPlanServices();
            $amcdata->plan_id = $product->id;
            $amcdata->service_id = $value;
            $amcdata->service_details = json_encode(Service::find($value));
            $amcdata->quantity = $request->quantity[$key];
            $amcdata->save();
        }
        return response()->json([], 200);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $product = AMCPlan::find($request->id);
        $product->status = $request->status;
        $product->save();
        flash()->success(translate('Plan status updated!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $plan = AMCPlan::find($id);
        $planservices = AMCPlanServices::where('plan_id', $plan->id)->get();
        $planservicesids = AMCPlanServices::where('plan_id', $plan->id)->pluck('service_id')->toArray();
        $services = Service::where('status',0)->get();
        return view('Admin.views.amc.plan.edit', compact('plan','planservices','planservicesids','services'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'service' => 'required',
            'quantity' => 'required',
            'price' => 'required|numeric|min:0',
            'duration' => 'required',
        ], [
            'name.required' => translate('Product name is required!'),
            'service.required' => translate('Service is required!'),
            'quantity.required' => translate('Quantity is required!'),
            'price.required' => translate('Price is required!'),
            'duration.required' => translate('Duration is required!'),
        ]);

        AMCPlanServices::where('plan_id', $id)->delete();

        $product = AMCPlan::find($id);

        $images = json_decode($product->image);
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $imageData = Helpers_upload('Images/amcImages/', $img->getClientOriginalExtension() , $img);
                $images[] = $imageData;
            }

        }

        if (!count($images)) {
            $validator->getMessageBag()->add('images', 'Image can not be empty!');
        }
        
        $product->name = $request->name;
        $product->description = $request->description;
        $product->image = json_encode($images);
        $product->duration = $request->duration;
        $product->price = $request->price;
        $product->tax = $request->tax;
        $product->discount = $request->discount;
        $product->save();

        foreach ($request->service as $key => $value) 
        {
            $amcdata = new AMCPlanServices();
            $amcdata->plan_id = $product->id;
            $amcdata->service_id = $value;
            $amcdata->service_details = json_encode(Service::find($value));
            $amcdata->quantity = $request->quantity[$key];
            $amcdata->save();
        }
        
        return response()->json([], 200);
    }

    /**
     * @param $id
     * @param $images
     * @param $products
     * @param $name
     * @return RedirectResponse
     */
    public function removeImage($id, $images, $products, $name): \Illuminate\Http\RedirectResponse
    {
        $fullpath = "Images/amcImages/".$name;
        if (File::exists("Images/amcImages/".$name))
        {
            File::delete("Images/amcImages/".$name);
        }

        $product = AMCPlan::find($id);
        $imageArray = [];

        foreach (json_decode($product['image'], true) as $img) 
        {
            if (strcmp($img, $fullpath) != 0) 
            {
                $imageArray[] = $img;
            }
        }

        AMCPlan::where(['id' => $id])->update([
            'image' => json_encode($imageArray),
        ]);
        flash()->success(translate('Image removed successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $product_reviews = AMCPlanServices::where('plan_id', $request->id)->get();
        foreach ($product_reviews as $review) 
        {
            $review->delete();
        }
        AMCPlan::where(['id' => $request->id])->delete();
        flash()->success(translate('Product removed!'));
        return back();
    }
}
