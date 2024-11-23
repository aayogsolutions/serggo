<?php

namespace App\Http\Controllers\Api\user\service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\{
    DisplaySection,
    HomeBanner,
    ServiceTag,
    DisplayCategory,
    HomeSliderBanner,
    Service,
    ServiceCategory,
    ServiceCategoryBanner
};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function __construct(
        private HomeBanner $homebanner,
        private ServiceTag $servicetag,
        private ServiceCategoryBanner $servicecategorybanner,
        private HomeSliderBanner $homesliderbanner,
        private DisplaySection $displaysection,
        private DisplayCategory $displaycategory,
        private ServiceCategory $servicecategory,
        private Service $service,
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
        $all = $this->service->status()->select('id','tags')->get();
        foreach ($all as $a)
        {
            $a->tags = json_decode($a->tags);
            foreach ($a->tags as $key => $tag) 
            {
                foreach ($keys as $key1 => $value) 
                {
                    // dump($a->id, $value,stripos($tag, $value));
                    if (stripos($tag, $value) !== false)
                    {
                        //if $c starts with $input, add to matches list
                        $matches[] = $a->id;
                        break;
                    }
                }
            }
        }
        $matches = array_unique($matches);
        $matches = array_values($matches);

        // Finding Categorys

        $categories = $this->servicecategory->status()->where(function ($q) use ($keys) 
        {
            foreach ($keys as $value) 
            {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->get();

        $services = $this->service->status()->where(function ($q) use ($keys) 
        {
            foreach ($keys as $value)
            {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->get();
        

        // Finding Products
        
        $products1 = $this->service->status()->whereIn('id',$matches)->orderBy('total_sale','DESC')->get();

        $products = Arr::collapse([$categories,$products1,$services]);

        $products = array_values(array_unique($products, SORT_REGULAR));
        
        if(!empty($products))
        {
            return response()->json([
                'status' => true,
                'message' => 'Search Details',
                'data' => product_data_formatting($products,true),
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
     * @param $id
     * @return JsonResponse
     */
    public function CategoryDetails($id) : JsonResponse
    {
        try {
            $categorys = $this->servicecategory->where('id',$id)->with('childes')->first();
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
            try {
                $data['homesliderbanner'] = homesliderbanner_data_formatting($this->homesliderbanner->status()->where('ui_type','user_service')->orderBy('priority', 'asc')->limit(6)->get(), true);
            } catch (\Throwable $th) {
                $data['homesliderbanner'] = [];
            }

            try {
                $mainbanner = $this->servicecategorybanner->where('sub_category_id',$category_id)->first();
                $mainbanner->sub_category_detail = json_decode($mainbanner->sub_category_detail);
            } catch (\Throwable $th) {
                $mainbanner = [];
            }

            $categorys = $this->servicecategory->status()->where(['id' => $category_id , 'position' => 1])->with(['childes','childes.Services'])->get();

            return response()->json([
                'status' => true,
                'message' => 'Category Data',
                'data' => [
                    'mainbanner' => $mainbanner,
                    'category' => $categorys,
                    'sliderbanner' => $data['homesliderbanner']
                ]
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
