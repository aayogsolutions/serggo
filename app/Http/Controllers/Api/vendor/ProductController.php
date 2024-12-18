<?php

namespace App\Http\Controllers\Api\vendor;

use App\Http\Controllers\Controller;
use App\Models\{
    Attributes,
    Brands,
    Category,
    Products,
    Tag
};
use Illuminate\Http\{
    Request,
    JsonResponse
};
use Illuminate\Support\Facades\{
    Auth,
    Validator
};

class ProductController extends Controller
{
    public function __construct(
       private Category $category,
       private Tag $tags,
       private Brands $brand,
       private Attributes $attribute,
       private Products $product,
    ){}

    /**
     * 
     * @return JsonResponse
     */
    public function CreateProduct() : JsonResponse
    {
        try {
            $category = $this->category->where('status',0)->orderBy('priority' , 'ASC')->WithCount('childes')->having('childes_count', '>', 0)->get();
        } catch (\Throwable $th) {
            $category = [];
        }

        try {
            $brand = $this->brand->where('status',0)->get();
        } catch (\Throwable $th) {
            $brand = [];
        }

        try {
            $tags = $this->tags->orderBy('name')->get();
        } catch (\Throwable $th) {
            $tags = [];
        }

        try {
            $attribute = $this->attribute->orderBy('name')->get();
        } catch (\Throwable $th) {
            $attribute = [];
        }

        try {
            return response()->json([
                'status' => true,
                'message' => 'Product Create Data',
                'data' => [
                    'category' => $category,
                    'brand' => $brand,
                    'tags' => $tags,
                    'attribute' => $attribute
                ]
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error '.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function SubCategoryDetail($id) : JsonResponse
    {
        try {
            $category = $this->category->where('status',0)->where('parent_id', $id)->orderBy('priority' , 'ASC')->get();

            return response()->json([
                'status' => true,
                'message' => 'Category Data',
                'data' => $category
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error '.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function StoreProduct(Request $request) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'brand' => 'required',
            'unit' => 'required',
            'images' => 'required',
            'tags' => 'required',
            'discount' => 'required',
            'tax' => 'required|numeric|min:0',
            'attribute_id' => 'required',
            'choice' => 'required',
            'variations' => 'required',
            'total_stock' => 'required|numeric|min:0',
            'is_installable' => 'required',
            'installable_name' => 'required_if:is_installable,0',
            'installable_description' => 'nullable',
            'installable_price' => 'required_if:is_installable,0|numeric|min:0',
        ]);

        if ($validator->fails()) 
        {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            
            // if ($request['discount_type'] == 'percent') {
            //     $discount = ($request['price'] / 100) * $request['discount'];
            // } else {
            //     $discount = $request['discount'];
            // }
    
            // if ($request['price'] <= $discount) {
            //     $validator->getMessageBag()->add('unit_price', 'Discount can not be more or equal to the price!');
            // }
            
            $imageNames = [];
            if (!empty($request->images))
            {
                foreach (json_decode($request->images) as $img)
                {
                    $imageData = Helpers_upload('Images/productImages/', $img->extension() , $img);
                    $imageNames[] = $imageData;
                }
                $imageData = json_encode($imageNames);
            } else {
                $imageData = json_encode([]);
            }
    
            $choiceOptions = [];
            if ($request->has('choice')) {
                foreach (json_decode($request->attribute_id) as $key => $no)
                {
                    $str = 'choice_options_' . $no;
                    
                    if ($request[$str] == null) {
                        $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
                        return response()->json(['errors' => Helpers_error_processor($validator)]);
                    }
                    $item['name'] = 'choice_' . $no;
                    $item['title'] = json_decode($request->choice)[$key];
                    $item['options'] = explode(',', implode(',', preg_replace('/\s+/', ' ', json_decode($request[$str]))));
                    $choiceOptions[] = $item;
                }
            }

            // $variations = [];
            // $options = [];
            // if ($request->has('choice_no')) {
            //     foreach ($request->attribute_id as $key => $no) {
            //         $name = 'choice_options_' . $no;
            //         $my_str = implode('|', $request[$name]);
            //         $options[] = explode(',', $my_str);
            //     }
            // }
            // $combinations = Helpers_combinations($options);
    
            // $stockCount = 0;
            // if (count($combinations[0]) > 0) {
            //     foreach ($combinations as $key => $combination) {
            //         $str = '';
            //         foreach ($combination as $k => $item) {
            //             if ($k > 0) {
            //                 $str .= '-' . str_replace(' ', '', $item);
            //             } else {
            //                 $str .= str_replace(' ', '', $item);
            //             }
            //         }
            //         $item = [];
            //         $item['type'] = $str;
            //         $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
            //         $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
    
            //         if ($request['discount_type'] == 'amount' && $item['price'] <= $request['discount'] ){
            //             $validator->getMessageBag()->add('discount_mismatch', 'Discount can not be more or equal to the price. Please change variant '. $item['type'] .' price or change discount amount!');
            //         }
    
            //         $variations[] = $item;
            //         $stockCount += $item['stock'];
            //     }
            // } else {
            //     $stockCount = (integer)$request['total_stock'];
            // }

            $stockCount = 0;
            if(count(json_decode($request->variations)) > 0) 
            {
                foreach(json_decode($request->variations) as $key => $variation)
                {
                    $stockCount += $variation->stock;
                }
            }
    
            if ((integer)$request['total_stock'] != $stockCount) 
            {
                $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
                return response()->json([
                    'status' => false,
                    'errors' => 'Stock calculation mismatch!'
                ],408);
            }
    
            if ($validator->getMessageBag()->count() > 0) 
            {
                return response()->json([
                    'status' => false,
                    'errors' => Helpers_error_processor($validator)
                ],408);
            }
            
            $product = new Products();
            $product->name = $request->name;
            $product->brand_name = json_encode($this->brand->find($request->brand));
            $product->brand_id = $request->brand;
            $product->vender_id = Auth::user()->id;
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id ;
            $product->description = $request->description;
            $product->choice_options = json_encode($choiceOptions);
            $product->variations = $request->variations;
            $product->price = json_decode($request->variations)[0]->price;
            $product->unit = $request->unit;
            $product->image = $imageData;
            $product->tax = $request->tax;
            $product->tax_type = 'precent';
            $product->discount = $request->discount;
            $product->discount_type = 'precent';
            if($request->is_installable == 0)
            {
                $product->installation_name = $request->installation_name;
                $product->installation_description = $request->installation_description;
                $product->installation_charges = $request->installable_price;
            }
            $product->total_stock = $request->total_stock;
            $product->attributes = $request->attribute_id;
            $product->status = 2;
            $product->tags = $request->tags;
            $product->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Category Data',
                'data' => Products::find($product->id)
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error '.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /** 
     * @return JsonResponse
     */
    public function ProductList() : JsonResponse
    {
        try {
            $product = $this->product->where('vender_id' , Auth::user()->id)->get();
            return response()->json([
                'status' => true,
                'message' => 'Product Data',
                'data' => $product
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error '.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @param $id
     * @return JsonResponse
     */
    public function EditProduct($id) : JsonResponse
    {
        try {
            $product = $this->product->find($id);
            
            if($product->vender_id != Auth::user()->id)
            {
                return response()->json([
                    'status' => false,
                    'message' => 'This Product is not belongs to you',
                    'data' => []
                ],408);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Product Data',
                'data' => $product
            ],200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error '.$th->getMessage(),
                'data' => []
            ],408);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function UpdateProduct(Request $request, $id) : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'brand' => 'required',
            'unit' => 'required',
            'images' => 'required',
            'tags' => 'required',
            'discount' => 'required',
            'tax' => 'required|numeric|min:0',
            'attribute_id' => 'required',
            'choice' => 'required',
            'variations' => 'required',
            'total_stock' => 'required|numeric|min:0',
            'is_installable' => 'required',
            'installable_name' => 'required_if:is_installable,0',
            'installable_description' => 'nullable',
            'installable_price' => 'required_if:is_installable,0|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => Helpers_error_processor($validator)
            ], 406);
        }

        try {
            
            // if ($request['discount_type'] == 'percent') {
            //     $discount = ($request['price'] / 100) * $request['discount'];
            // } else {
            //     $discount = $request['discount'];
            // }
    
            // if ($request['price'] <= $discount) {
            //     $validator->getMessageBag()->add('unit_price', 'Discount can not be more or equal to the price!');
            // }
    
            $imageNames = [];
            if (!empty($request->file('images'))) {
                foreach ($request->images as $img) {
                    $imageData = Helpers_upload('Images/productImages/', $img->getClientOriginalExtension() , $img);
                    $imageNames[] = $imageData;
                }
                $imageData = json_encode($imageNames);
            } else {
                $imageData = json_encode([]);
            }
    
            $choiceOptions = [];
            if ($request->has('choice')) {
                foreach (json_decode($request->attribute_id) as $key => $no) {
                    $str = 'choice_options_' . $no;
                    
                    if ($request[$str] == null) {
                        $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
                        return response()->json(['errors' => Helpers_error_processor($validator)]);
                    }
                    $item['name'] = 'choice_' . $no;
                    $item['title'] = json_decode($request->choice)[$key];
                    $item['options'] = explode(',', implode(',', preg_replace('/\s+/', ' ', json_decode($request[$str]))));
                    $choiceOptions[] = $item;
                }
            }

            // $variations = [];
            // $options = [];
            // if ($request->has('choice_no')) {
            //     foreach ($request->attribute_id as $key => $no) {
            //         $name = 'choice_options_' . $no;
            //         $my_str = implode('|', $request[$name]);
            //         $options[] = explode(',', $my_str);
            //     }
            // }
            // $combinations = Helpers_combinations($options);
    
            // $stockCount = 0;
            // if (count($combinations[0]) > 0) {
            //     foreach ($combinations as $key => $combination) {
            //         $str = '';
            //         foreach ($combination as $k => $item) {
            //             if ($k > 0) {
            //                 $str .= '-' . str_replace(' ', '', $item);
            //             } else {
            //                 $str .= str_replace(' ', '', $item);
            //             }
            //         }
            //         $item = [];
            //         $item['type'] = $str;
            //         $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
            //         $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);
    
            //         if ($request['discount_type'] == 'amount' && $item['price'] <= $request['discount'] ){
            //             $validator->getMessageBag()->add('discount_mismatch', 'Discount can not be more or equal to the price. Please change variant '. $item['type'] .' price or change discount amount!');
            //         }
    
            //         $variations[] = $item;
            //         $stockCount += $item['stock'];
            //     }
            // } else {
            //     $stockCount = (integer)$request['total_stock'];
            // }

            $stockCount = 0;
            if(count(json_decode($request->variations)) > 0) 
            {
                foreach(json_decode($request->variations) as $key => $variation)
                {
                    $stockCount += $variation->stock;
                }
            }
    
            if ((integer)$request['total_stock'] != $stockCount) {
                $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
                return response()->json([
                    'status' => false,
                    'errors' => 'Stock calculation mismatch!'
                ],408);
            }
    
            if ($validator->getMessageBag()->count() > 0) {
                return response()->json([
                    'status' => false,
                    'errors' => Helpers_error_processor($validator)
                ],408);
            }
            
            $product = $this->product->find($id);
            $product->name = $request->name;
            $product->brand_name = json_encode($this->brand->find($request->brand));
            $product->brand_id = $request->brand;
            $product->vender_id = Auth::user()->id;
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id ;
            $product->description = $request->description;
            $product->choice_options = json_encode($choiceOptions);
            $product->variations = $request->variations;
            $product->price = json_decode($request->variations)[0]->price;
            $product->unit = $request->unit;
            $product->image = $imageData;
            $product->tax = $request->tax;
            $product->tax_type = 'precent';
            $product->discount = $request->discount;
            $product->discount_type = 'precent';
            if($request->is_installable == 0)
            {
                $product->installation_name = $request->installation_name;
                $product->installation_description = $request->installation_description;
                $product->installation_charges = $request->installation_charges;
            }
            $product->total_stock = $request->total_stock;
            $product->attributes = $request->attribute_id;
            $product->status = 2;
            $product->tags = $request->tags;
            $product->save();
            
            return response()->json([
                'status' => true,
                'message' => 'Category Data',
                'data' => Products::find($product->id)
            ],201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'unexpected error '.$th->getMessage(),
                'data' => []
            ],408);
        }
    }
}
