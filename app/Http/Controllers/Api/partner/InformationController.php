<?php

namespace App\Http\Controllers\Api\partner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InformationController extends Controller
{
    /**
     * 
     * @return JsonResponse
     * 
     */
    public function AboutUs() : JsonResponse
    {   
        try {
            $about_us  = Helpers_get_business_settings('about_us_partner');

            if(!empty($about_us))
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Get About Us',
                    'data' => $about_us
                ],200); 
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found',
                    'data' => []
                ],404); 
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Data not found',
                'data' => []
            ],408);
        }
       
    }

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function TermConditions() : JsonResponse
    {   
        try {
            $termscondition  = Helpers_get_business_settings('terms_and_conditions_partner');

            if(!empty($termscondition))
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Get Terms & Conditions',
                    'data' => $termscondition
                ],200);  
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found',
                    'data' => []
                ],404); 
            }                       
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Data not found',
                'data' => []
            ],408);
        }
       
    }

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function PrivacyPolicy() : JsonResponse
    {   
        try {

            $privacy_policy  = Helpers_get_business_settings('privacy_policy_partner');

            if(!empty($privacy_policy))
            {
                return response()->json([
                    'status' => true,
                    'message' => 'Get Privacy Policy',
                    'data' => $privacy_policy
                ],200);   
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found',
                    'data' => []
                ],404); 
            }

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => 'Data not found',
                'data' => []
            ],408);
        }
       
    }
}
