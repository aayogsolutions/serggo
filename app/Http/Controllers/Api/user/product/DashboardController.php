<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use App\Models\{
    Brands,
    DisplaySection,
    HomeBanner,
    HomeSliderBanner,
    Tag,
    DisplayCategory
};
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
        private DisplayCategory $displaycategory,
    ){}
    
    /**
     * 
     * @return JsonResponse
     * 
     */
    public function Index(Request $request) : JsonResponse
    {
        if(isset($request->platform)){

            if($request->platform == 0)
            {
                $limit = 7;
            }
            else{
                $limit = 10;
            }

            try {
                $maindata = $this->homebanner->status()->where('ui_type','user_product')->first();
            } catch (\Throwable $th) {
                $maindata->background_color = null;
                $maindata->font_color = null;
                $maindata->attechment_type = null;
                $maindata->attechment = null;
            }

            try {
                $data['tags'] = $this->tag->orderBy('name','DESC')->get('name');
            } catch (\Throwable $th) {
                $data['tags'] = [];
            }

            try {
                $data['brands'] = $this->brand->status()->select('id','name','Image')->orderBy('priority','ASC')->withCount('childes')->having('childes_count', '>', 0)->get();
            } catch (\Throwable $th) {
                $data['brands'] = [];
            }

            try {
                $data['homesliderbanner'] = homesliderbanner_data_formatting($this->homesliderbanner->status()->where('ui_type','user_product')->orderBy('priority', 'asc')->limit(6)->get(), true);
            } catch (\Throwable $th) {
                $data['homesliderbanner'] = [];
            }

            try {
                $data['slider'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_product')->where('section_type','slider')->orderBy('priority', 'asc')->with('childes')->get(), true);
            } catch (\Throwable $th) {
                $data['slider'] = [];
            }

            try {
                $data['cart'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_product')->where('section_type','cart')->orderBy('priority', 'asc')->with('childes',function($q) use ($limit){
                    $q->take($limit);
                })->get(), true);
            } catch (\Throwable $th) {
                $data['cart'] = [];
            }

            try {
                $data['box_section'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_product')->where('section_type','box_section')->orderBy('priority', 'asc')->with('childes',function($q) use ($limit){
                    $q->take($limit);
                })->get(), true);
            } catch (\Throwable $th) {
                $data['box_section'] = [];
            }
    
            return response()->json([
                'status' => true,
                'data' => [
                    'colorcode' => $maindata->background_color ?? '#fe2e2e',
                    'fontcode' => $maindata->font_color ?? '#ffffff',
                    'bannerType' => $maindata->attechment_type ?? 'not found',
                    'banner' => $maindata->attechment ?? 'not found',
                    'id' => $maindata->item_id ?? 'not found',
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
     * 
     * @return JsonResponse
     * 
     */
    public function CategoryDisplay($ui) : JsonResponse
    {
        try {

            if($ui == 'user')
            {
                $data = category_data_formatting($this->displaycategory->status()->where('ui_type','user_product')->get(),true);

                
                if(!empty($data))
                {
                    return response()->json([
                        'status' => true,
                        'message' => 'Display Data',
                        'data' => $data
                    ],200);
                }
                return response()->json([
                    'status' => false,
                    'message' => 'Data not found',
                    'data' => []
                ],408);

            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Parameter must be only user'
                ],408);
            }
            
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'error' => $th->getMessage(),
                'data' => []
            ],408);
        }
    }
}
