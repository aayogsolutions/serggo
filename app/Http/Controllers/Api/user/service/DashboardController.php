<?php

namespace App\Http\Controllers\Api\user\service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\{
    Brands,
    DisplaySection,
    HomeBanner,
    ServiceTag,
    DisplayCategory,
    ServiceCategory
};
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function __construct(
        private HomeBanner $homebanner,
        private ServiceTag $servicetag,
        // private Brands $brand,
        // private HomeSliderBanner $homesliderbanner,
        private DisplaySection $displaysection,
        private DisplayCategory $displaycategory,
        private ServiceCategory $servicecategory,
    ){}

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
                $maindata = $this->homebanner->status()->where('ui_type','user_service')->first();
            } catch (\Throwable $th) {
                $maindata->background_color = null;
                $maindata->font_color = null;
                $maindata->attechment_type = null;
                $maindata->attechment = null;
            }

            try {
                $data['tags'] = $this->servicetag->orderBy('name','DESC')->get('name');
            } catch (\Throwable $th) {
                $data['tags'] = [];
            }

            try {
                $data['slider'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_service')->where('section_type','slider')->orderBy('priority', 'asc')->with('childes',function($q) use ($limit){
                    $q->take($limit);
                })->get(), true);
            } catch (\Throwable $th) {
                $data['slider'] = [];
            }

            try {
                $data['box_section'] = display_data_formatting($this->displaysection->status()->where('ui_type','user_service')->where('section_type','box_section')->orderBy('priority', 'asc')->with('childes',function($q) use ($limit){
                    $q->take(6);
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
                    
                    'arraydata' => [
                        'tags' => $data['tags'],
                        'smallbanners' => $data['slider'],
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
            ],406);
        }
    }

    /**
     * @return JsonResponse
     */
    public function CategoryDetails() : JsonResponse
    {
        try {

            $categorys = $this->servicecategory->status()->where('position', 0)->get();
            return response()->json([
                'status' => true,
                'message' => 'Category Data',
                'data' => $categorys
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error',
                'data' => []
            ], 406);
        }
        
    }

    /**
     * @param $category_id   
     * @return JsonResponse
     */
    public function SubCategoryDetails($category_id) : JsonResponse
    {
        try {

            $categorys = $this->servicecategory->status()->where('parent_id', $category_id)->with('childes.Services')->get();

            return response()->json([
                'status' => true,
                'message' => 'Category Data',
                'data' => $categorys
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error',
                'data' => []
            ], 406);
        }
        
    }
}
