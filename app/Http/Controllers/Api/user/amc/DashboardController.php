<?php

namespace App\Http\Controllers\Api\user\amc;

use App\Http\Controllers\Controller;
use App\Models\AMCPlan;
use App\Models\AMCPlanServices;
use App\Models\HomeBanner;
use App\Models\HomeSliderBanner;
use App\Models\Order;
use App\Models\Order_details;
use App\Models\Service;
use App\Models\ServiceTimeSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function Index(Request $request) : JsonResponse
    {

        try {
            $maindata = HomeBanner::where(['status' => 0,'ui_type' => 'user_service'])->first();
        } catch (\Throwable $th) {
            $maindata->background_color = null;
            $maindata->font_color = null;
            $maindata->attechment_type = null;
            $maindata->attechment = null;
        }

        try {
            $homeslider = HomeSliderBanner::where(['status' => 0,'ui_type' => 'amc'])->orderBy('priority', 'asc')->limit(6)->get();
        } catch (\Throwable $th) {
            $homeslider = [];
        }

        try {
            $timeslot = ServiceTimeSlot::select('id','time')->where('status' , 1)->orderby('priority' , 'asc')->get();
        } catch (\Throwable $th) {
            $timeslot = [];
        }

        if(Auth::guard('sanctum')->check())
        {
            if(Order::where(['order_type' => 'amc','user_id' => Auth::guard('sanctum')->user()->id,'plan_activate' => 1])->exists())
            {
                $order = Order::where(['order_type' => 'amc','user_id' => Auth::guard('sanctum')->user()->id,'plan_activate' => 1])->orderBy('id', 'desc')->first();
            }
            
            if(isset($order))
            {
                $plan = AMCPlan::where('id', $order->plan_id)->first();

                if(Carbon::parse($order->created_at)->add(str_replace("_",",",$plan->duration)) > Carbon::now())
                {
                    $services = AMCPlanServices::where('plan_id', $order->plan_id)->get();

                    $count = 0;
                    foreach ($services as $key => $value) 
                    {
                        if($value->service_activate == 1)
                        {
                            $count += 1;
                        }
                    }

                    if(($key + 1) == $count)
                    {
                        $order->plan_activate = 0;
                        $order->save();
                    }else{

                        $order->order_details = Order_details::where('order_id', $order->id)->get();

                        foreach ($order->order_details as $key => $value) {
                            $value->product_details = json_decode($value->product_details);
                            $value->product_details->service_details = Service_data_formatting(json_decode($value->product_details->service_details));
                        }

                        $plan->child = AMCPlanServices::where('plan_id', $plan->id)->get();

                        foreach ($plan->child as $key => $value) {
                            $value->service_details = Service_data_formatting(json_decode($value->service_details));
                        }

                        return response()->json([
                            'status' => true,
                            'plan' => true,
                            'data' => [
                                'colorcode' => $maindata->background_color ?? '#079AC2',
                                'fontcode' => $maindata->font_color ?? '#ffffff',
                                'bannerType' => $maindata->attechment_type ?? 'not found',
                                'banner' => $maindata->attechment ?? 'not found',
                                'array' => [
                                    'Slider' => $homeslider,
                                    'order' => $order,
                                    'plan' => $plan,
                                    'timeslot' => $timeslot
                                ]
                            ]
                        ],200);
                    }
                }else{
                    $order->plan_activate = 0;
                    $order->save();
                }
            }
        }
        
        

        if(isset($request->platform)){

            if($request->platform == 0)
            {
                $limit = 8;
            }
            else{
                $limit = 10;
            }

            try {
                $data = AMCPlan::where('status',1)->orderBy('id','ASC')->with('PlanChild')->get();
            } catch (\Throwable $th) {
                $data = [];
            }

            foreach ($data as $key => $value) 
            {
                foreach ($value->PlanChild as $key => $value1) 
                {
                    $value1->service_details = Service_data_formatting(json_decode($value1->service_details));
                }
            }
    
            return response()->json([
                'status' => true,
                'plan' => false,
                'data' => [
                    'colorcode' => $maindata->background_color ?? '#079AC2',
                    'fontcode' => $maindata->font_color ?? '#ffffff',
                    'bannerType' => $maindata->attechment_type ?? 'not found',
                    'banner' => $maindata->attechment ?? 'not found',
                    'id' => $maindata->item_id ?? 'not found',
                    'array' => [
                        'Slider' => $homeslider,
                        'plan' => $data,
                        'timeslot' => $timeslot
                    ]
                ]
            ],200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'Platform Key is required',
                'data' => [
                    '0 is for' => 'Mobile View',
                    '1 is for' => 'Web View'
                ]
            ],408);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function Search(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $keys = explode(' ', $request->key);
        $matches = array();
        $category_id = array();


        // Finding Tags
        $plans = AMCPlan::where('status',1)->where(function ($q) use ($keys) 
        {
            foreach ($keys as $value)
            {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->with('PlanChild')->get();

        foreach ($plans as $key => $value) 
        {
            foreach ($value->PlanChild as $key => $value1) 
            {
                $value1->service_details = Service_data_formatting(json_decode($value1->service_details));
            }
        }

        $services = Service::where(function ($q) use ($keys) 
        {
            foreach ($keys as $value)
            {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->pluck('id')->toArray();
        
        $amcservices = AMCPlanServices::whereIn('service_id', $services)->pluck('plan_id');

        $amcplans = AMCPlan::whereIn('id', $amcservices)->with('PlanChild')->get();

        foreach ($amcplans as $key => $value) 
        {
            foreach ($value->PlanChild as $key => $value1) 
            {
                $value1->service_details = Service_data_formatting(json_decode($value1->service_details));
            }
        }

        // Finding Products
        
        // $products1 = $this->service->status()->whereIn('id',$matches)->orderBy('total_sale','DESC')->get();

        $products = Arr::collapse([$plans,$amcplans]);

        // $products = array_values(array_unique($products, SORT_REGULAR));
        
        if(!empty($products))
        {
            return response()->json([
                'status' => true,
                'message' => 'Search Details',
                'data' => $products,
            ], 200);
        }else{
            return response()->json([
                'status' => true,
                'message' => 'Data not found',
                'data' => [],
            ], 200);
        }
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function PlanDetails($id) : JsonResponse
    {
        if(AMCPlan::where('id', $id)->exists())
        {
            $data = AMCPlan::where('id', $id)->first();
            $data->services = AMCPlanServices::where('plan_id', $id)->get();

            foreach ($data->services as $key => $value) 
            {
                $value->service_details = Service_data_formatting(json_decode($value->service_details),false,false);
            }

            return response()->json([
                'status' => true,
                'data' => $data
            ],200);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Plan not found'
            ],408);
        }
    }
}
