<?php

namespace App\Http\Controllers\Api\user;

use App\Http\Controllers\Controller;
use App\Models\AuthBanners;
use App\Models\SplashBanner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;


class BannerController extends Controller
{
    public function __construct(
        private AuthBanners $authbanner,
        private SplashBanner $splashBanner,
    ){}
    
    /**
     * 
     * @return JsonResponse
     * 
     */
    public function Auth($ui,$section) : JsonResponse
    {
        
        if($ui == 'user')
        {
            if($section == 'login' || $section == 'signup' || $section == 'verify')
            {
                $data = $this->authbanner->status()->where('ui_type', 'user')->where('section_type',$section)->first();
                return response()->json([
                    'status' => true,
                    'data' => $data
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Section Parameter must be only login or signup or verify'
                ]);
            }

        }elseif ($ui == 'vender') {
            if($section == 'login' || $section == 'signup' || $section == 'verify')
            {
                $data = $this->authbanner->status()->where('ui_type', 'vender')->where('section_type',$section)->first();
                return response()->json([
                    'status' => true,
                    'data' => $data
                ],200);
            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Section Parameter must be only login or signup or verify'
                ]);
            }

        }else{
            return response()->json([
                'status' => false,
                'message' => 'Ui Parameter must be only user or vender'
            ]);
        }
    }

    /**
     * 
     * @return JsonResponse
     * 
     */
    public function Splash($ui) : JsonResponse
    {
        
        if($ui == 'user')
        {
            $data = $this->splashBanner->status()->where('ui_type', 'user')->first();
            return response()->json([
                'status' => true,
                'data' => $data
            ],200);

        }elseif ($ui == 'vender') {
            $data = $this->splashBanner->status()->where('ui_type', 'vender')->first();
            return response()->json([
                'status' => true,
                'data' => $data
            ],200);

        }else{
            return response()->json([
                'status' => false,
                'message' => 'Parameter must be only user or vender'
            ]);
        }
    }
}
