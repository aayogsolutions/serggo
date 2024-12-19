<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\{
    Admin,
    Category,
    Products,
    DisplaySection,
    DisplaySectionContent,
    HomeSliderBanner
};
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function __construct(

        private Products $product,
        private Category $category,
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
        
        $childes_ids = [];
        $childes = [];
        $product = $this->product->status()->where('id',$request->product_id)->first();

        if(!empty($product)) 
        {
            $data = product_data_formatting($product, false, true, true);
            
            // More Related Product....!

            $relatedproduct = [];
            try {
    
                $rproducts = $this->product->status()->where('sub_category_id',$data->sub_category_id)->whereNotIn('id', [$data->id])->orderBy('total_sale','DESC')->limit(4)->get();

                if(!empty($rproducts))
                {
                    if(count($rproducts) < 4)
                    {
                        $mrproducts = $this->product->status()->where('category_id',$data->category_id)->whereNotIn('id', [$data->id])->orderBy('total_sale','DESC')->limit(4)->get();

                        foreach ($mrproducts as $key => $value) {
                            if(count($rproducts) < 4)
                            {
                                $rproducts[] = $value;
                            }else{
                                break;
                            }
                        }

                        if(count($rproducts) < 4)
                        {
                            $mrproducts = $this->product->status()->whereNotIn('category_id', [$data->category_id])->orderBy('total_sale','DESC')->limit(4)->get();

                            foreach ($mrproducts as $key => $value) {
                                if(count($rproducts) < 4)
                                {
                                    $rproducts[] = $value;
                                }else{
                                    break;
                                }
                            }
                        }
                    }
                }else{
                    $rproducts = $this->product->status()->where('category_id',$data->category_id)->whereNotIn('id', [$data->id])->orderBy('total_sale','DESC')->limit(4)->get();

                    if(count($rproducts) < 4)
                    {
                        $mrproducts = $this->product->status()->whereNotIn('category_id', [$data->category_id])->orderBy('total_sale','DESC')->limit(4)->get();

                        foreach ($mrproducts as $key => $value) {
                            if(count($rproducts) < 4)
                            {
                                $rproducts[] = $value;
                            }else{
                                break;
                            }
                        }
                    }
                    
                }
                $relatedproduct = product_data_formatting($rproducts,true,false,true);
            } catch (\Throwable $th) {
                $relatedproduct = [];
            }
            //  More Related Product End....! 

            //  More Related Slider Display....! 
            $Cartmainsection = [];

            try {
                
                $Cartmainsection = $this->display_section->status()->where([
                    ['ui_type','=','user_product'],
                    ['section_type','=','cart'],
                ])->orderBy('priority', 'ASC')->first();
                
                if(!empty($Cartmainsection))
                {
                    $cartsectionid = $this->display_section->status()->where([
                        ['ui_type','=','user_product'],
                        ['section_type','=','cart'],
                    ])->get('id');

                    if(!empty($cartsectionid))
                    {
                        foreach ($cartsectionid as $key => $value) {
                            $cartsection_ids[] = $value->id;
                        }

                        $cart_section_product = $this->displaysectioncontent->whereIn('section_id',$cartsection_ids)->whereNotIn('item_id' , [$data->id])->get();

                        foreach ($cart_section_product as $key => $v)
                        {
                            if(count($childes_ids) < 4)
                            {
                                if(!in_array($v->item_id,$childes_ids))
                                {
                                    $childes[] = $v;
                                    $childes_ids[] = $v->item_id;
                                }
                            }
                        }
                        
                        $Cartmainsection->childes = $childes;

                        $Cartmainsection = display_data_formatting($Cartmainsection);
                    }else{

                    }
                }
                
            } catch (\Throwable $th) {
                $Cartmainsection = [];
            }
            //  More Related Slider Display End....!

            //  More Related Slider Display....! 

            $section_items_product = [];
            try {
                $sectionid = $this->display_section->status()->where([
                    ['ui_type','=','user_product'],
                    ['section_type','=','slider'],
                ])->get('id');
    
                if(!empty($sectionid))
                {
                    foreach ($sectionid as $key => $value) {
                        $section_ids[] = $value['id'];
                    };
                    
                    $section_items_product1 = $this->displaysectioncontent->whereIn('section_id',$section_ids)->where('item_type' , 'product')->where('item_id' , $data->id)->get();
                    $section_items_category_id = $this->product->status()->where('category_id', $data->category_id)->whereNotIn('id', [$request->product_id])->get('id')->toArray();
        
                    if(!is_null($section_items_category_id) && $section_items_category_id != [])
                    {
                        foreach ($section_items_category_id as $key => $value) {
                            $section_items_category_ids[] = $value->id;
                        };
                        
                        $section_items_product2 = $this->displaysectioncontent->whereIn('section_id',$section_ids)->whereIn('item_id',$section_items_category_ids)->get();
            
                        if(!empty($section_items_product2))
                        {
                            foreach ($section_items_product2 as $key => $value) {
                                $section_items_product1[] = $value;
                            }
                        }
        
                        $section_items_product3 = $this->displaysectioncontent->whereIn('section_id',$section_ids)->whereNotIn('item_id',$section_items_category_ids)->whereNotIn('item_id',[$request->product_id])->get();
        
                        if(!empty($section_items_product3))
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
    
                    if(!empty($section_items_product_final))
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

                    if(!empty($homesliderbanner_data1))
                    {
                        foreach ($homesliderbanner_data1 as $key => $value) {
                            if($key <= 8){
                                $sliderbanner[] = $value;
                            }
                        }
                    }
                    if(!empty($homesliderbanner_data2))
                    {
                        foreach ($homesliderbanner_data2 as $key => $value) {
                            if($key <= 8){
                                $sliderbanner[] = $value;
                            }
                        }
                    }
                    if(!empty($homesliderbanner_data4))
                    {
                        foreach ($homesliderbanner_data4 as $key => $value) {
                            if($key <= 8){
                                $sliderbanner[] = $value;
                            }
                        }
                    }
                    if(!empty($homesliderbanner_data3))
                    {
                        foreach ($homesliderbanner_data3 as $key => $value) {
                            if($key <= 8){
                                $sliderbanner[] = $value;
                            }
                        }
                    }

                    if(!empty($sliderbanner))
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
                        'cart' => $Cartmainsection,
                        'silderbannersection' => $sliderbanner,
                        'sildersection' => $section_items_product,
                    ],
                ]
            ],200);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Product Not exists',
                'data' => []
            ],408);
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
        if(!empty($section)) {

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
                'message' => 'Section data not available',
                'data' => []
            ],408);
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
        $brandedProduct_brand = $this->product->status()->where('brand_id',$request->brand_id)->orderby('total_sale','DESC')->get();
        $brandedProduct_nonbrand = $this->product->status()->whereNotIn('brand_id',[$request->brand_id])->orderby('total_sale','DESC')->get();

        if(!empty($brandedProduct_brand) && $brandedProduct_brand != [])
        {
            $brandProduct = $brandedProduct_brand;
            foreach ($brandedProduct_nonbrand as $key => $value) {
                $brandProduct[] = $value;
            }
            
            $brandProduct = product_data_formatting($brandProduct,true);
            return response()->json([
                'status' => true,
                'message' => 'Brands Details',
                'data' => [
                    'products' => $brandProduct,
                ]
            ], 200);
        }else{
            return response()->json([
                'status' => false,
                'message' => 'No Details Available',
                'data' => []
            ], 408);
        }
    }

    /**
     * 
     * @return JsonResponse
     * 
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

        $all = $this->product->status()->select('id','tags')->get();
        foreach ($all as $a)
        {
            if($a->tags != null)
            {
                $a->tags = json_decode($a->tags);
                foreach ($a->tags as $key => $tag) 
                {
                    foreach ($keys as $key => $value) 
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
        }
        $matches = array_unique($matches);
        $matches = array_values($matches);

        // Finding Categorys

        $category_ids = $this->category->status()->where(['position' => 0])->where(function ($q) use ($keys) 
        {
            foreach ($keys as $value) 
            {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->get('id');
        
        foreach ($category_ids as $key => $value) 
        {
            $category_id[] = $value->id;
        }

        $product2 = $this->product->status()->whereIn('category_id',$category_id)->get();
        

        // Finding Products
        
        $products1 = $this->product->status()->whereIn('id',$matches)->orWhere(function ($q) use ($keys) 
        {
            foreach ($keys as $value) 
            {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->orderBy('total_sale','DESC')->get();

        $products = Arr::collapse([$products1,$product2]);

        $products = array_values(array_unique($products, SORT_REGULAR));
        
        if(!empty($products))
        {
            return response()->json([
                'status' => true,
                'message' => 'Product Details',
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
     * 
     * @return JsonResponse
     * 
     */
    public function DeliveryTimeLine(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
            'products' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $products = json_decode($request->products);

        foreach ($products as $key => $value) {
            if(Products::find($value)->vender_id == null)
            {
                $distance = $this->point2point_distance(Admin::select('latitude','longitude')->where('id',1)->first()->latitude, Admin::select('latitude','longitude')->where('id',1)->first()->longitude, $request->latitude, $request->longitude,'K');
            }else{
                $vendor = Products::select('id','vender_id')->where('id', $value)->with('vendorproducts')->first();

                $distance = $this->point2point_distance($vendor->vendorproducts->latitude, $vendor->vendorproducts->longitude, $request->latitude, $request->longitude,'K');
            }

            $time = $this->calculateDilevaryTime($distance);
            $data[$key] = [
                'product_id' => $value,
                'time' => $time
            ];
        }
        
        array_multisort(array_column($data, 'time'), SORT_ASC, $data);

        return response()->json([
            'status' => true,
            'message' => 'Data Provided',
            'timelimit' => '24 hour',
            'data' => $data[0]
        ], 200);
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

    private function point2point_distance($lat1, $lon1, $lat2, $lon2, $unit='K') 
    { 
        $theta = $lon1 - $lon2; 
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)); 
        $dist = acos($dist); 
        $dist = rad2deg($dist); 
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") 
        {
            return ($miles * 1.609344); 
        } 
        else if ($unit == "N") 
        {
            return ($miles * 0.8684);
        } 
        else 
        {
            return $miles;
        }
    } 

    private function calculateDilevaryTime($distance) 
    { 
        if($distance < 10)
        {
            $time = '1 Hour';
        }
        else if($distance < 20)
        {
            $time = '2 Hour';
        }
        else if($distance < 30)
        {
            $time = '4 Hour';
        }
        else if($distance < 40)
        {
            $time = '5 Hour';
        }
        else if($distance < 50)
        {
            $time = '8 Hour';
        }
        else if($distance < 60)
        {
            $time = '12 Hour';
        }
        else if($distance < 70)
        {
            $time = '16 Hour';
        }
        else if($distance < 80)
        {
            $time = '20 Hour';
        }
        else if($distance < 100)
        {
            $time = '24 Hour';
        }
        else
        {
            $time = '25 Hour';
        }
        return $time;
    } 
}