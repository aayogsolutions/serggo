<?php

namespace App\Http\Controllers\Api\user\product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;
use App\Models\DisplaySection;


class ProductController extends Controller
{
    public function __construct(

        private Products $product,
        private DisplaySection $display_section,

        
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

            return response()->json([
                'status' => true,
                'message' => 'Detail Provided',
                'data' => [
                    'product_details' => $data,
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

            // $data = display_data_formatting($section);

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
}
