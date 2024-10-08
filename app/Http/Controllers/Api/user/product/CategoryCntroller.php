<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use App\Models\{
    Category,
    Products,
};
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class CategoryCntroller extends Controller
{
    public function __construct(

        private Category $category,
        private Products $product,
    ){}

     /**
     * 
     * @return JsonResponse
     * 
     */
    public function CategoryDetails(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|numeric',
            'screen' => 'required|in:new,toprated,newitem,trending,instant,lowprice,topbrand,discounted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $tags = ['Top Rated','New item','Trending','Instant deliver','Low price','Top Brands','Discounted'];

        try {
            $category = $this->category->where('id', $request->category_id)->with('banner')->first();
            
            if(is_null($category))
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Data not exists',
                    'data' => []
                ],404);
            }
            foreach ($category->banner as $key => $value)
            {
                $value->sub_category_detail = json_decode($value->sub_category_detail,true);
            }
        } catch (\Throwable $th) {
            $category = [];
        }

        if($request->screen == 'new' || $request->screen == 'toprated')
        {
            try {
                $toprated = $this->product->status()->where('category_id', $request->category_id)->get();
                $toprated = product_data_formatting($toprated,true);
                $products = array_values(Arr::sortDesc($toprated,function($value){
                    return $value['reviewsAverage'];
                }));

                $product['toprated'] = array_slice($products, 0,29);
            } catch (\Throwable $th) {
                $product['toprated'] = [];
            }
        }
        if($request->screen == 'new' || $request->screen == 'newitem')
        {
            try {
                $newitem = $this->product->status()->where('category_id', $request->category_id)->orderBy('created_at','DESC')->get();
                $newitem = product_data_formatting($newitem,true);
    
                $product['newitem'] = array_slice($newitem, 0,29);
            } catch (\Throwable $th) {
                $product['newitem'] = [];
            }
        }

        if($request->screen == 'trending')
        {
            try {
                $trending = $this->product->status()->where('category_id', $request->category_id)->orderBy('total_sale','DESC')->get();
                $trending = product_data_formatting($trending,true);
                
                $product['trending'] = array_slice($trending, 0,29);
            } catch (\Throwable $th) {
                $product['trending'] = [];
            }
        }

        if($request->screen == 'instant')
        {
            try {
                $instant = $this->product->status()->where('category_id', $request->category_id)->orderBy('total_sale','DESC')->get();
                $instant = product_data_formatting($instant,true);
    
                $product['instant'] = array_slice($instant, 0,29);
            } catch (\Throwable $th) {
                $product['instant'] = [];
            }
        }

        if($request->screen == 'lowprice')
        {
            try {
                $lowprice = $this->product->status()->where('category_id', $request->category_id)->orderBy('price','ASC')->get();
                $lowprice = product_data_formatting($lowprice,true);
    
                $product['lowprice'] = array_slice($lowprice, 0,29);
            } catch (\Throwable $th) {
                $product['lowprice'] = [];
            }
        }

        if($request->screen == 'topbrand')
        {
            try {
                $topbrand_category = $this->product->status()->where('category_id', $request->category_id)->orderBy('created_at','DESC')->get();
                $topbrand = product_data_formatting($topbrand_category,true,false,true);

                $topbrand_category = array_values(Arr::sort($topbrand,function($value){
                    return $value['brand_name']->priority;
                }));
                $product['topbrand'] = array_slice($topbrand_category, 0,29);
            } catch (\Throwable $th) {
                $product['topbrand'] = [];
            }
        }

        if($request->screen == 'discounted')
        {
            try {
                $discounted = $this->product->status()->where('category_id', $request->category_id)->get();
                $discounted = product_data_formatting($discounted,true);
    
                $discounted_product = array_values(Arr::sortDesc($discounted,function($value){
                    return $value['productdiscount'];
                }));

                $product['discounted'] = array_slice($discounted_product, 0,29);
            } catch (\Throwable $th) {
                $product['discounted'] = [];
            }
        }
        
        return response()->json([
            'status' => true,
            'message' => 'category Details',
            'data' => [
                'tags' => $tags,
                'category' => $category,
                'products' => $product
            ]
        ],200);
    }

     /**
     * 
     * @return JsonResponse
     * 
     */
    public function SubCategoryDetails(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subcategory_id' => 'required|numeric',
            'screen' => 'required|in:new,toprated,newitem,trending,instant,lowprice,topbrand,discounted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        $tags = ['Top Rated','New item','Trending','Instant deliver','Low price','Top Brands','Discounted'];

        try {
            $subcategory = $this->category->where([
                ['id','=', $request->subcategory_id],
                ['position','=',1]    
            ])->first();

            if(is_null($subcategory))
            {
                return response()->json([
                    'status' => false,
                    'message' => 'Data not exists',
                    'data' => []
                ],404);
            }
        } catch (\Throwable $th) {
            $subcategory = [];
        }

        if($request->screen == 'new' || $request->screen == 'toprated')
        {
            try {
                $toprated = $this->product->status()->where('sub_category_id', $request->subcategory_id)->get();
                $toprated = product_data_formatting($toprated,true);
                $products = array_values(Arr::sortDesc($toprated,function($value){
                    return $value['reviewsAverage'];
                }));

                $product['toprated'] = array_slice($products, 0,29);
            } catch (\Throwable $th) {
                $product['toprated'] = [];
            }
        }
        if($request->screen == 'new' || $request->screen == 'newitem')
        {
            try {
                $newitem = $this->product->status()->where('sub_category_id', $request->subcategory_id)->orderBy('created_at','DESC')->get();
                $newitem = product_data_formatting($newitem,true);
    
                $product['newitem'] = array_slice($newitem, 0,29);
            } catch (\Throwable $th) {
                $product['newitem'] = [];
            }
        }

        if($request->screen == 'trending')
        {
            try {
                $trending = $this->product->status()->where('sub_category_id', $request->subcategory_id)->orderBy('total_sale','DESC')->get();
                $trending = product_data_formatting($trending,true);
                
                $product['trending'] = array_slice($trending, 0,29);
            } catch (\Throwable $th) {
                $product['trending'] = [];
            }
        }

        if($request->screen == 'instant')
        {
            try {
                $instant = $this->product->status()->where('sub_category_id', $request->subcategory_id)->orderBy('total_sale','DESC')->get();
                $instant = product_data_formatting($instant,true);
    
                $product['instant'] = array_slice($instant, 0,29);
            } catch (\Throwable $th) {
                $product['instant'] = [];
            }
        }

        if($request->screen == 'lowprice')
        {
            try {
                $lowprice = $this->product->status()->where('sub_category_id', $request->subcategory_id)->orderBy('price','ASC')->get();
                $lowprice = product_data_formatting($lowprice,true);
    
                $product['lowprice'] = array_slice($lowprice, 0,29);
            } catch (\Throwable $th) {
                $product['lowprice'] = [];
            }
        }

        if($request->screen == 'topbrand')
        {
            try {
                $topbrand_category = $this->product->status()->where('sub_category_id', $request->subcategory_id)->orderBy('created_at','DESC')->get();
                $topbrand = product_data_formatting($topbrand_category,true,false,true);

                $topbrand_category = array_values(Arr::sort($topbrand,function($value){
                    return $value['brand_name']->priority;
                }));
                $product['topbrand'] = array_slice($topbrand_category, 0,29);
            } catch (\Throwable $th) {
                $product['topbrand'] = [];
            }
        }

        if($request->screen == 'discounted')
        {
            try {
                $discounted = $this->product->status()->where('sub_category_id', $request->subcategory_id)->get();
                $discounted = product_data_formatting($discounted,true);
    
                $discounted_product = array_values(Arr::sortDesc($discounted,function($value){
                    return $value['productdiscount'];
                }));

                $product['discounted'] = array_slice($discounted_product, 0,29);
            } catch (\Throwable $th) {
                $product['discounted'] = [];
            }
        }
        
        return response()->json([
            'status' => true,
            'message' => 'Sub Category Details',
            'data' => [
                'tags' => $tags,
                'subcategory' => $subcategory,
                'products' => $product
            ]
        ],200);
    }
}
