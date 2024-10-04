<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\{
    Products,
    DisplaySection,
    DisplaySectionContent,
    HomeSliderBanner
};

class ProductController extends Controller
{
    public function __construct(

        private Products $product,
        private DisplaySection $display_section,
        private DisplaySectionContent $displaysectioncontent,
        private HomeSliderBanner $homesliderbanner,
    ){}

     /**
     * 
     * @return JsonResponse
     * 
     */
    public function Index(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }
        
        $product = $this->product->status()->where('id',$request->product_id)->first();

        if(!is_null($product)) 
        {
            $data = product_data_formatting($product, false, true);

            // More Related Product....!

            $relatedproduct = [];
            try {
                $sectionid = $this->display_section->status()->where([
                    ['ui_type','=','user_product'],
                    ['section_type','=','cart'],
                ])->get('id');
    
                if(!is_null($sectionid))
                {
                    foreach ($sectionid as $key => $value) {
                        $section_id[] = $value->id;
                    };
        
                    $item_ids = $this->displaysectioncontent->whereIn('section_id',$section_id)->whereNotIn('item_id', [$request->product_id])->groupBy('item_id')->get('item_id');
                    $products = $this->product->status()->where('category_id',$data->category_id)->whereNotIn('id', [$request->product_id])->orderBy('total_sale','DESC')->get();
                    
                    
                    if(!is_null($item_ids) && !is_null($products))
                    {
                        $filtered_data = $this->search_product($item_ids ,$products);
                        $relatedproducts = $filtered_data['data'];
                        foreach ($filtered_data['array'] as $key => $value) {
                            $relatedproducts[] = $value;
                        }

                        if(count($relatedproducts) < 4)
                        {
                            $allproducts = $this->product->status()->whereNotIn('id', [$request->product_id])->orderBy('total_sale','DESC')->get();

                            foreach ($allproducts as $key => $value) {
                                if(count($relatedproducts) <= 3)
                                {
                                    $relatedproducts[] = $value;
                                }else{
                                    break;
                                }
                            }

                            $relatedproduct = $relatedproducts;
                        }else{
                            foreach ($relatedproducts as $key => $value) {
                                if($key <= 3)
                                {
                                    $relatedproduct[] = $value;
                                }else{
                                    break;
                                }
                            }
                        }
                    }else{
                        $allproducts = $this->product->status()->whereNotIn('id', [$request->product_id])->orderBy('total_sale','DESC')->get();

                        foreach ($allproducts as $key => $value) {
                            if($key <= 3)
                            {
                                $relatedproduct[] = $value;
                            }else{
                                break;
                            }
                        }
                    }
                }else{
                    $allproducts = $this->product->status()->whereNotIn('id', [$request->product_id])->orderBy('total_sale','DESC')->get();

                    foreach ($allproducts as $key => $value) {
                        if($key <= 3)
                        {
                            $relatedproduct[] = $value;
                        }else{
                            break;
                        }
                    }
                }
                $relatedproduct = product_data_formatting($relatedproduct,true);
            } catch (\Throwable $th) {
            }
            //  More Related Product End....! 

            //  More Related Slider Display....! 

            $section_items_product = [];

            try {
                $sectionid = $this->display_section->status()->where([
                    ['ui_type','=','user_product'],
                    ['section_type','=','slider'],
                ])->get('id');
    
                if(!is_null($sectionid))
                {
                    foreach ($sectionid as $key => $value) {
                        $section_ids[] = $value->id;
                    };
                    
                    $section_items_product1 = $this->displaysectioncontent->whereIn('section_id',$section_ids)->where('item_type' , 'product')->where('item_id' , $data->id)->get();
                    $section_items_category_id = $this->product->status()->where('category_id', $data->category_id)->whereNotIn('id', [$request->product_id])->get('id');
        
                    if(!is_null($section_items_category_id))
                    {
                        foreach ($section_items_category_id as $key => $value) {
                            $section_items_category_ids[] = $value->id;
                        };
                        $section_items_product2 = $this->displaysectioncontent->whereIn('section_id',$section_ids)->whereIn('item_id',$section_items_category_ids)->get();
            
                        if(!is_null($section_items_product2))
                        {
                            foreach ($section_items_product2 as $key => $value) {
                                $section_items_product1[] = $value;
                            }
                        }
        
                        $section_items_product3 = $this->displaysectioncontent->whereIn('section_id',$section_ids)->whereNotIn('item_id',$section_items_category_ids)->whereNotIn('item_id',[$request->product_id])->get();
        
                        if(!is_null($section_items_product3))
                        {
                            foreach ($section_items_product3 as $key => $value) {
                                $section_items_product1[] = $value;
                            }
                        }
                    }
                    
                    if(count($section_items_product1) >= 10)
                    {
                        foreach ($section_items_product1 as $key => $value) {
                            if($key <= 10){
                                $section_items_product_final[] = $value;
                            }
                        }
                    }else{
                        $section_items_product_final = $section_items_product1;
                    }
    
                    if(!is_null($section_items_product_final))
                    {
                        $section_items_product = homesliderbanner_data_formatting($section_items_product_final, true);
                    }
                }
            } catch (\Throwable $th) {
            }
            //  More Related Slider Display End....!

            //  More Related Slider Banners....!

            $sliderbanner = [];

            try {
                if($this->homesliderbanner->status()->exists())
                {
                    $homesliderbanner_data1 = $this->homesliderbanner->status()->where('item_type' , 'product')->where('item_id' , $data->id)->get();
                    $homesliderbanner_data2 = $this->homesliderbanner->status()->where('item_type' , 'category')->where('item_id' , $data->category_id)->get();
                    $homesliderbanner_data3 = $this->homesliderbanner->status()->where('item_type' , 'product')->whereNotIn('item_id' , [$data->id])->get();
                    $homesliderbanner_data4 = $this->homesliderbanner->status()->where('item_type' , 'category')->whereNotIn('item_id' , [$data->category_id])->get();

                    if(!is_null($homesliderbanner_data1))
                    {
                        foreach ($homesliderbanner_data1 as $key => $value) {
                            if($key <= 8){
                                $sliderbanner[] = $value;
                            }
                        }
                    }
                    if(!is_null($homesliderbanner_data2))
                    {
                        foreach ($homesliderbanner_data2 as $key => $value) {
                            if($key <= 8){
                                $sliderbanner[] = $value;
                            }
                        }
                    }
                    if(!is_null($homesliderbanner_data4))
                    {
                        foreach ($homesliderbanner_data4 as $key => $value) {
                            if($key <= 8){
                                $sliderbanner[] = $value;
                            }
                        }
                    }
                    if(!is_null($homesliderbanner_data3))
                    {
                        foreach ($homesliderbanner_data3 as $key => $value) {
                            if($key <= 8){
                                $sliderbanner[] = $value;
                            }
                        }
                    }

                    if(!is_null($sliderbanner))
                    {
                        $sliderbanner = homesliderbanner_data_formatting($sliderbanner, true);
                    }
                }
            } catch (\Throwable $th) {
            }
            //  More Related Slider Banners End....!
            
            return response()->json([
                'status' => true,
                'message' => 'Detail Provided',
                'data' => [
                    'product_details' => $data,
                    'more_option' => [
                        'related' => $relatedproduct,
                        'silderbannersection' => $sliderbanner,
                        'sildersection' => $section_items_product,
                    ],
                ]
            ],200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Product Not exists'
            ],404);
        }
    }
      
      /**
     * 
     * @return JsonResponse
     * 
     */
    public function Display(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'section_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $section = $this->display_section->find($request->section_id);
        if(!is_null($section)) {

            try {
                $data = display_data_formatting($section);

                return response()->json([
                    'status' => true,
                    'message' => 'Display details',
                    'data' =>  $data
                ]);
            } catch (\Throwable $th) {
                return response()->json([
                    'status' => false,
                    'message' => 'data not available',
                    'data' =>  []
                ]);
            }
            
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Section data not available'
            ]);
        }
    }

      /**
     * 
     * @return JsonResponse
     * 
     */
    public function BrandSelected(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }
        $brandProduct = [];
        $brandedProduct = $this->product->status()->orderby('total_sale','DESC')->get();

        if(!is_null($brandedProduct))
        {

            $brands_product1 = $this->search_brand($request->brand_id,$brandedProduct);
            $brandProduct = $brands_product1['data'];
            foreach ($brands_product1['array'] as $key => $value) {
                $brandProduct[] = $value;
            }
            
            $brandProduct = product_data_formatting($brandProduct,true);
            return response()->json([
                'status' => true,
                'message' => 'Brands Details',
                'data' => [
                    'products' => $brandProduct
                ]
            ], 200);
        }else{
            return response()->json([
                'status' => true,
                'message' => 'No Details Available',
                'data' => []
            ], 200);
        }

        
    }
  
    protected function search_brand($ids,$array)
    {
        $data = [];
        $mainarray = $array;
        $mainreturnarray = [];
        foreach ($array as $key => $value) {
            $value->brand_name = json_decode($value->brand_name, true);

            if($ids == $value->brand_name['id'])
            {
                array_push($data,$value);
                unset($mainarray[$key]);
            }
        }
        foreach ($mainarray as $key => $value) {
            $mainreturnarray[] = $value;
        }
        $return = array('data' => $data, 'array' => $mainreturnarray);
        return $return;
    }

    protected function search_product($ids,$array)
    {
        $data = [];
        $mainarray = $array;
        $mainreturnarray = [];
        foreach ($ids as $key => $id)
        {
            foreach ($array as $key1 => $value)
            {
                if($id->item_id == $value->id)
                {
                    array_push($data,$value);
                    unset($mainarray[$key1]);
                }
            }
        }
        foreach ($mainarray as $key => $value) {
            $mainreturnarray[] = $value;
        }
        $return = array('data' => $data, 'array' => $mainreturnarray);
        return $return;
    }
}