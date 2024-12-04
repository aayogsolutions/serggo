<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    ServiceCategory, 
    Service,
    ServiceCategoryBanner,
    ServiceReview,
};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Rap2hpoutre\FastExcel\FastExcel;

class ServiceController extends Controller
{
    public function __construct(
        private ServiceCategory $service_category,
        private Service $service,
        private ServiceCategoryBanner $servicecategorybanner,
        private ServiceReview $review,
    ){}

    /**
     * @return Factory|View|Application
     */
    public function index(): View|Factory|Application
    {
        $categories = $this->service_category->status()->where('position' , 0)->get();
        // $brand = $this->brand->status()->get();
        // $Installations = $this->Installation->status()->get();
        return view('Admin.views.services.index', compact('categories'));
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function list(Request $request): View|Factory|Application
    {  
      
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $this->service->whereNotIn('status',[2])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->latest();
            $queryParam = ['search' => $request['search']];
        }else{
            $query = $this->service->whereNotIn('status',[2])->latest();
        }
        $service = $query->paginate(Helpers_getPagination())->appends($queryParam); 
        return view('Admin.views.services.service-list', compact('service','search'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCategories(Request $request): JsonResponse
    {
        $categories = $this->service_category->where(['parent_id' => $request->parent_id])->get();
        $result = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($categories as $row) {
            if ($row->id == $request->sub_category) {
                $result .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $result .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'options' => $result,
            'option' => $request->parent_id,
        ]);
    }
    
     /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getChildCategories(Request $request): JsonResponse
    {
        $categories = $this->service_category->where(['parent_id' => $request->parent_id])->get();
        $result = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($categories as $row) {
            if ($row->id == $request->sub_category) {
                $result .= '<option value="' . $row->id . '" selected >' . $row->name . '</option>';
            } else {
                $result .= '<option value="' . $row->id . '">' . $row->name . '</option>';
            }
        }
        return response()->json([
            'options' => $result,
            'option' => $request->parent_id,
        ]);
    }

     /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'images' => 'required',
            'price' => 'required|numeric|min:0',
            'time_duration' => 'required',
           
        ], [
            'name.required' => translate('Service name is required!'),
            'category_id.required' => translate('category  is required!'),
        ]);

        if ($request['discount_type'] == 'percent') {
            $discount = ($request['price'] / 100) * $request['discount'];
        } else {
            $discount = $request['discount'];
        }

        if ($request['price'] <= $discount) {
            $validator->getMessageBag()->add('unit_price', 'Discount can not be more or equal to the price!');
        }

        $imageNames = [];
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $imageData = Helpers_upload('Images/ServiceImages/', $img->getClientOriginalExtension() , $img);
                $imageNames[] = $imageData;
            }
            $imageData = json_encode($imageNames);
        } else {
            $imageData = json_encode([]);
        }

        // $choiceOptions = [];
        // if ($request->has('choice')) {
        //     foreach ($request->choice_no as $key => $no) {
        //         $str = 'choice_options_' . $no;
        //         if ($request[$str][0] == null) {
        //             $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
        //             return response()->json(['errors' => Helpers_error_processor($validator)]);
        //         }
        //         $item['name'] = 'choice_' . $no;
        //         $item['title'] = $request->choice[$key];
        //         $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
        //         $choiceOptions[] = $item;
        //     }
        // }

        // $variations = [];
        // $options = [];
        // if ($request->has('choice_no')) {
        //     foreach ($request->choice_no as $key => $no) {
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

        // if ((integer)$request['total_stock'] != $stockCount) {
        //     $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
        // }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers_error_processor($validator)]);
        }
      
        // $installations = $this->Installation->find($request->installation);
     
        $service = $this->service;
        $service->name = $request->name;
        // $product->brand_name = json_encode($this->brand->find($request->brand));
        // $product->brand_id = $request->brand;
        // if(isset($request->otherbrand) && !is_null($request->otherbrand))
        // {
        //     $product->brandname_if_other = $request->otherbrand;
        // }
        $service->category_id = $request->category_id;
        $service->sub_category_id = $request->sub_category_id;
        $service->child_category_id = $request->child_category_id;
        $service->description = $request->description;
        $service->time_duration = $request->time_duration;
        // $product->choice_options = json_encode($choiceOptions);
        // $product->variations = json_encode($variations);
        $service->price = $request->price;
        // $product->unit = $request->unit;
        $service->image = $imageData;
        $service->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $service->tax_type = $request->tax_type;
        $service->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $service->discount_type = $request->discount_type;
        // $product->installation_name = $installations->installation_name;
        // $product->installation_description = $installations->installation_description;
        // $product->installation_charges = $installations->installation_charges;
        // $product->total_stock = $request->total_stock;
        // $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $service->status = 0;
        $service->tags = json_encode($request->tag_name);
        
        $service->save();

        return response()->json([], 200);
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function view($id): View|Factory|RedirectResponse|Application
    {
        $product = $this->service->where(['id' => $id])->first();
        if (!$product){
            flash()->error(translate('product not found'));
            return back();
        }
        $reviews = $this->review->where(['service_man_id' => $id])->latest()->paginate(20);
        return view('Admin.views.services.view', compact('product', 'reviews'));
    }


     /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $service = $this->service->find($request->id);
        $service->status = $request->status;
        $service->save();
        flash()->success(translate('Service status updated!'));
        return back();
    }

     /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $service = $this->service->find($request->id);
        foreach (json_decode($service['image'], true) as $img) {
            if (File::exists($img)) {
                File::delete($img);
            }
        }

        $service->delete();
        flash()->success(translate('Service removed!'));
        return back();
    }


    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
       
        $service = $this->service->find($id);

        $categories = $this->service_category->where(['parent_id' => 0])->get();
        $subcategories = $this->service_category->status()->where('parent_id' , $service->category_id)->get();
        $childcategories = $this->service_category->status()->where('parent_id' , $service->sub_category_id)->get();
     
        return view('Admin.views.services.edit', compact('service', 'categories','subcategories','childcategories'));
    }    

    /**
     * @param $id
     * @param $images
     * @param $products
     * @param $name
     * @return RedirectResponse
     */
    public function removeImage($id, $images, $service, $name): \Illuminate\Http\RedirectResponse
    {
        if (File::exists("Images/ServiceImages/".$name))
        {
            File::delete("Images/ServiceImages/".$name);
            $name = "Images/ServiceImages/".$name;
        }

        $service = $this->service->find($id);
        $imageArray = [];

        foreach (json_decode($service['image'], true) as $img) {
            if (strcmp($img, $name) != 0) {
                $imageArray[] = $img;
            }
        }

        $this->service->where(['id' => $id])->update([
            'image' => json_encode($imageArray),
        ]);
        flash()->success(translate('Image removed successfully!'));
        return back();
    }

     /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'category_id' => 'required',
            'time_duration' => 'required',           
            'price' => 'required|numeric|min:0',
        ], [
            'name.required' => 'Product name is required!',
            'category_id.required' => 'category  is required!',
        ]);

        if ($request['discount_type'] == 'percent') {
            $discount = ($request['price'] / 100) * $request['discount'];
        } else {
            $discount = $request['discount'];
        }

        if ($request['price'] <= $discount) {
            $validator->getMessageBag()->add('unit_price', 'Discount can not be more or equal to the price!');
        }

        $tags = [];
        if ($request->tags != null) {
            $tag = explode(",", str_replace(" ", "",$request->tags));
        }
        if(isset($tag)){
            foreach ($tag as $key => $value) {
                if($value != ""){
                    $tags[] = $value;
                }
            }
        }

        $service = $this->service->find($id);

        $images = json_decode($service->image);
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $imageData = Helpers_upload('Images/ServiceImages/', $img->getClientOriginalExtension() , $img);
                $images[] = $imageData;
            }

        }

        if (!count($images)) {
            $validator->getMessageBag()->add('images', 'Image can not be empty!');
        }

        // $category = [];
        // if ($request->category_id != null) {
        //     $category[] = [
        //         'id' => $request->category_id,
        //         'position' => 1,
        //     ];
        // }
        // if ($request->sub_category_id != null) {
        //     $category[] = [
        //         'id' => $request->sub_category_id,
        //         'position' => 2,
        //     ];
        // }
        // if ($request->sub_sub_category_id != null) {
        //     $category[] = [
        //         'id' => $request->sub_sub_category_id,
        //         'position' => 3,
        //     ];
        // }
        
        // $choiceOptions = [];
        // if ($request->has('choice')) {
        //     foreach ($request->choice_no as $key => $no) {
        //         $str = 'choice_options_' . $no;
        //         if ($request[$str][0] == null) {
        //             $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
        //             return response()->json(['errors' => Helpers_error_processor($validator)]);
        //         }
        //         $item['name'] = 'choice_' . $no;
        //         $item['title'] = $request->choice[$key];
        //         $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
        //         $choiceOptions[] = $item;
        //     }
        // }

        // $variations = [];
        // $options = [];
        // if ($request->has('choice_no')) {
        //     foreach ($request->choice_no as $key => $no) {
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

        // if ((integer)$request['total_stock'] != $stockCount) {
        //     $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
        // }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers_error_processor($validator)]);
        }

        $service->name = $request->name;
        // $product->brand_name = json_encode($this->brand->find($request->brand));
        // $product->brand_id = $request->brand;
        // if(isset($request->otherbrand) && !is_null($request->otherbrand))
        // {
        //     $product->brandname_if_other = $request->otherbrand;
        // }
        $service->category_id = $request->category_id;
        $service->sub_category_id = $request->sub_category_id;
        $service->child_category_id = $request->child_category_id;
        $service->description = $request->description;
        $service->time_duration = $request->time_duration;
        // $product->choice_options = json_encode($choiceOptions);
        // $product->variations = json_encode($variations);
        $service->price = $request->price;
        // $service->unit = $request->unit;
        $service->image = json_encode($images);
        $service->tags = json_encode($tags);
        $service->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $service->tax_type = $request->tax_type;
        $service->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $service->discount_type = $request->discount_type;
        // $product->total_stock = $request->total_stock;
        // $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $service->status = $request->status? $request->status:0;
        // if(isset($request->installation) && !is_null($request->installation))
        // {
        //     $Installations = $this->Installation->find($request->installation);
        //     $product->installation_name = $Installations->installation_name; 
        //     $product->installation_charges = $Installations->installation_charges; 
        //     $product->installation_description = $Installations->installation_description; 
        // }
        $service->save();
        
        return response()->json([], 200);
    }

    // Sub-Categories Banner

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function SubcategoryIndex(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $category = $this->service_category->status()->where('position',1)->withCount('childes')->having('childes_count', '>', 0)->with('banner')->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $queryParam = ['search' => $request['search']];
        } else {
            $category = $this->service_category->status()->where('position',1)->withCount('childes')->having('childes_count', '>', 0)->with('banner');
        }
        $categories = $category->paginate(Helpers_getPagination())->appends($queryParam);
      
        return view('Admin.views.services.Subcategories.index', compact('categories', 'search'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function SubcategoryAddContent(Request $request, $id): RedirectResponse
    {
        
        $request->validate([
            'image' => 'required',
        ],[
            'image.required' => translate('image is required'),
        ]);

        $category = $this->service_category->find($id);
        $file_size = getimagesize($request->file('image'));
        // Width Check                Height Check
        if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
            
            if(!$this->servicecategorybanner->where('sub_category_id' , $category->id)->exists())
            {
                $banner = $this->servicecategorybanner;
                $banner->category_id = $category->parent_id;
                $banner->sub_category_id = $category->id;
                $banner->sub_category_detail = json_encode($category);
                $banner->attechment = Helpers_upload('Images/banners/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->save();
                flash()->success(translate('Item Added successfully!'));
            }else{
                $banner = $this->servicecategorybanner->where('sub_category_id' , $category->id)->first();
                $banner->attechment = Helpers_update('Images/banners/', $banner->attechment , $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->save();
                flash()->success(translate('Item Updated successfully!'));
            }
            return redirect()->back();
        }
        flash()->error(translate('Image size is wrong.!'));
        return redirect()->back();
    }
}
