<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\{
    Products,
    DisplaySection,
    DisplaySectionContent
};

use function Laravel\Prompts\search;

class ProductController extends Controller
{
    public function __construct(

        private Products $product,
        private DisplaySection $display_section,
        private DisplaySectionContent $displaysectioncontent,
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

            $ids = [];
            $relatedproduct = [];
            $limit = 4;

            $sectionid = $this->display_section->status()->where([
                ['ui_type','=','user_product'],
                ['section_type','=','cart'],
            ])->get('id');

            foreach ($sectionid as $key => $value) {
                $section_id[] = $value->id;
            };

            $section_items = $this->displaysectioncontent->whereIn('section_id',$section_id)->whereNotIn('item_id', [$request->product_id])->groupBy('item_id')->get('item_id');
            
            $products = $this->product->status()->where('category_id', $data->category_id)->orderBy('total_stock','DESC')->get();

            
            foreach ($section_items as $key => $value) 
            {
                if($this->search_array($value->item_id ,$products))
                {
                    array_push($ids,$value->item_id);
                }
            }
            $relatedproduct = $this->product->status()->whereIn('id', $ids)->orderBy('total_stock','DESC')->get();
            array_push($ids,$request->product_id);
            $limit = $limit - count($relatedproduct);

            if($limit >= 0)
            {
                $morerelated = $this->product->status()->where('category_id' , $data->category_id)->whereNotIn('id', $ids)->orderBy('total_stock','DESC')->limit($limit)->get();

                if(!is_null($morerelated)){
                    foreach ($morerelated as $key => $value) {
                        $relatedproduct[] = $value;
                    }
                }
            }else{
            }
            
            //  More Related Product End....! 

            //  More Related Slider Banners....! 

            // $sectionid = $this->display_section->status()->where([
            //     ['ui_type','=','user_product'],
            //     ['section_type','=','slider'],
            // ])->get('id');

            // foreach ($sectionid as $key => $value) {
            //     $section_id[] = $value->id;
            // };
            
            // $section_items = $this->displaysectioncontent->whereIn('section_id',$section_id)->get();
            
            $relatedproduct = product_data_formatting($relatedproduct, true);
            // $data = product_data_formatting($product, true);
            return response()->json([
                'status' => true,
                'message' => 'Detail Provided',
                'data' => [
                    'product_details' => $data,
                    'more_option' => [
                        'related' => $relatedproduct,
                        // 'sildersection' => $relatedproduct,
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
    public function display(Request $request) : JsonResponse
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

            $data = display_data_formatting($section);

            return response()->json([
                'status' => true,
                'data' =>  $section
            ]);
        }else {
            return response()->json([
                'status' => false,
                'message' => 'Section data not available'
            ]);
        }
    }

    protected function search_array($id,$array)
    {
        foreach ($array as $key => $value) {
            if($id == $value->id)
            {
                return true;
            }
        }
        return false;
    }
}
