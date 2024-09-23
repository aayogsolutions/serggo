<?php

namespace App\Http\Controllers\Api\product;

use App\Http\Controllers\Controller;
use App\Models\DisplaySection;
use App\Models\HomeBanner;
use App\Models\HomeSliderBanner;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private HomeBanner $homebanner,
        private HomeSliderBanner $homesliderbanner,
        private DisplaySection $displaysection,
    ){}
    
    /**
     * 
     * @return JsonResponse
     * 
     */
    public function Index() : JsonResponse
    {
        try {
            // $data['tags'] = 1;

            $data['homebanner'] = $this->homebanner->status()->where('ui_type','user_product')->get();
            $data['homesliderbanner'] = homesliderbanner_data_formatting($this->homesliderbanner->status()->where('ui_type','user_product')->get(), true);
            $data['slider'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_product')->where('section_type','slider')->with('childes')->get(), true);
            $data['cart'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_product')->where('section_type','cart')->with('childes')->limit(6)->get(), true);
            $data['box_section'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_product')->where('section_type','box_section')->with('childes')->get(), true);

            return response()->json([
                'status' => true,
                'data' => $data
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => $th->getMessage()
            ]);
        }
    }
}
