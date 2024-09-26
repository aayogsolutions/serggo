<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use App\Models\Brands;
use App\Models\DisplaySection;
use App\Models\HomeBanner;
use App\Models\HomeSliderBanner;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        private HomeBanner $homebanner,
        private Tag $tag,
        private Brands $brand,
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
            $maindata = $this->homebanner->status()->where('ui_type','user_product')->first();

            $data['tags'] = $this->tag->select('name')->orderBy('name','DESC')->get();
            $data['brands'] = $this->brand->status()->orderBy('priority','ASC')->select('name','Image')->get();
            $data['homesliderbanner'] = homesliderbanner_data_formatting($this->homesliderbanner->status()->where('ui_type','user_product')->orderBy('priority', 'asc')->get(), true);
            $data['slider'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_product')->where('section_type','slider')->orderBy('priority', 'asc')->with('childes')->get(), true);
            $data['cart'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_product')->where('section_type','cart')->orderBy('priority', 'asc')->with('childes')->limit(6)->get(), true);
            $data['box_section'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_product')->where('section_type','box_section')->orderBy('priority', 'asc')->with('childes')->get(), true);

            return response()->json([
                'status' => true,
                'data' => [
                    'colorcode' => $maindata->background_color,
                    'fontcode' => $maindata->font_color,
                    'bannerType' => $maindata->attechment_type,
                    'banner' => $maindata->attechment,
                    'brands' => $data['brands'],
                    'arraydata' => [
                        'tags' => $data['tags'],
                        'productslider' => $data['slider'],
                        'cartsection' => $data['cart'],
                        'bannerslider' => $data['homesliderbanner'],
                        'boxsection' => $data['box_section'],
                    ]
                ]
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => $th->getMessage()
            ]);
        }
    }
}
