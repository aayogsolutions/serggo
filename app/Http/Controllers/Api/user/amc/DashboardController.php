<?php

namespace App\Http\Controllers\Api\user\amc;

use App\Http\Controllers\Controller;
use App\Models\AMCPlan;
use App\Models\AMCPlanServices;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function Index(Request $request) : JsonResponse
    {
        if(isset($request->platform)){

            if($request->platform == 0)
            {
                $limit = 8;
            }
            else{
                $limit = 10;
            }

            try {
                $data = AMCPlan::where('status',1)->orderBy('id','ASC')->get();
            } catch (\Throwable $th) {
                $data = [];
            }
    
            return response()->json([
                'status' => true,
                'plan' => false,
                'data' => $data
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
        })->get();

        $services = Service::where(function ($q) use ($keys) 
        {
            foreach ($keys as $value)
            {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->pluck('id')->toArray();
        
        $amcservices = AMCPlanServices::whereIn('service_id', $services)->pluck('plan_id');

        $amcplans = AMCPlan::whereIn('id', $amcservices)->get();

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
