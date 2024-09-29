<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Brands, 
    BusinessSetting,
    Category,
    Products,
    ProductReview
};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function __construct(
        private BusinessSetting $business_setting,
        private Category $category,
        private Products $product,
        private Brands $brand,
        private ProductReview $review,
    ){}

    /**
     * @return Factory|View|Application
     */
    public function index(): View|Factory|Application
    {
        $categories = $this->category->status()->where('position' , 0)->get();
        $brand = $this->brand->status()->get();
        return view('Admin.views.product.index', compact('categories','brand'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCategories(Request $request): JsonResponse
    {
        $categories = $this->category->where(['parent_id' => $request->parent_id])->get();
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
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function variantCombination(Request $request): JsonResponse
    {
        $options = [];
        $price = $request->price;
        $product_name = $request->name;

        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }

        $result = [[]];
        foreach ($options as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        $combinations = $result;
        return response()->json([
            'view' => view('Admin.views.product.partials._variant-combinations', compact('combinations', 'price', 'product_name'))->render(),
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
            'total_stock' => 'required|numeric|min:1',
            'price' => 'required|numeric|min:0',
            'brand' => 'required',
        ], [
            'name.required' => translate('Product name is required!'),
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
                $imageData = Helpers_upload('Images/productImages/', $img->getClientOriginalExtension() , $img);
                $imageNames[] = $imageData;
            }
            $imageData = json_encode($imageNames);
        } else {
            $imageData = json_encode([]);
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


        $choiceOptions = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
                    return response()->json(['errors' => Helpers_error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                $choiceOptions[] = $item;
            }
        }

        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }
        $combinations = Helpers_combinations($options);

        $stockCount = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);

                if ($request['discount_type'] == 'amount' && $item['price'] <= $request['discount'] ){
                    $validator->getMessageBag()->add('discount_mismatch', 'Discount can not be more or equal to the price. Please change variant '. $item['type'] .' price or change discount amount!');
                }

                $variations[] = $item;
                $stockCount += $item['stock'];
            }
        } else {
            $stockCount = (integer)$request['total_stock'];
        }

        if ((integer)$request['total_stock'] != $stockCount) {
            $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
        }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers_error_processor($validator)]);
        }

        $product = $this->product;
        $product->name = $request->name;
        $product->brand_name = json_encode($this->brand->find($request->brand));
        if(isset($request->otherbrand) && !is_null($request->otherbrand))
        {
            $product->brandname_if_other = $request->otherbrand;
        }
        $product->admin_id = auth('admins')->user()->id;
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id ;
        $product->description = $request->description;
        $product->choice_options = json_encode($choiceOptions);
        $product->variations = json_encode($variations);
        $product->price = $request->price;
        $product->unit = $request->unit;
        $product->image = $imageData;
        $product->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $product->discount_type = $request->discount_type;
        $product->total_stock = $request->total_stock;
        $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $product->status = $request->status? $request->status : 0 ;
        $product->tags = json_encode($request->tag_name);
        
        $product->save();

        return response()->json([], 200);
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
            $query = $this->product->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->latest();
            $queryParam = ['search' => $request['search']];
        }else{
            $query = $this->product->latest();
        }
        $products = $query->with('order_details.order')->paginate(Helpers_getPagination())->appends($queryParam);

        foreach ($products as $product) {
            $totalSold = 0;
            foreach ($product->order_details as $detail) {
                if ($detail->order->order_status == 'delivered'){
                    $totalSold += $detail->quantity;
                }
            }
             $product->total_sold = $totalSold;
        }

        return view('Admin.views.product.list', compact('products','search'));
    }

    /**
     * @param $id
     * @return Application|Factory|View|RedirectResponse
     */
    public function view($id): View|Factory|RedirectResponse|Application
    {
        $product = $this->product->where(['id' => $id])->first();
        if (!$product){
            flash()->error(translate('product not found'));
            return back();
        }
        $reviews = $this->review->where(['product_id' => $id])->latest()->paginate(20);
        return view('Admin.views.product.view', compact('product', 'reviews'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->status = $request->status;
        $product->save();
        flash()->success(translate('Product status updated!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $product = $this->product->find($id);
        $categories = $this->category->where(['parent_id' => 0])->get();
        $subcategories = $this->category->status()->where('parent_id' , $product->category_id)->get();
        $brand = $this->brand->status()->get();
        return view('Admin.views.product.edit', compact('product', 'categories','subcategories','brand'));
    }

    /**
     * @param $id
     * @param $name
     * @return RedirectResponse
     */
    public function removeImage($id, $name): \Illuminate\Http\RedirectResponse
    {
        if (File::exists($name)) {
            File::delete($name);
        }

        $product = $this->product->find($id);
        $imageArray = [];

        foreach (json_decode($product['image'], true) as $img) {
            if (strcmp($img, $name) != 0) {
                $imageArray[] = $img;
            }
        }

        $this->product->where(['id' => $id])->update([
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
            'total_stock' => 'required|numeric|min:1',
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

        $product = $this->product->find($id);

        $images = json_decode($product->image);
        if (!empty($request->file('images'))) {
            foreach ($request->images as $img) {
                $imageData = Helpers_upload('Images/productImages/', $img->getClientOriginalExtension() , $img);
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
        
        $choiceOptions = [];
        if ($request->has('choice')) {
            foreach ($request->choice_no as $key => $no) {
                $str = 'choice_options_' . $no;
                if ($request[$str][0] == null) {
                    $validator->getMessageBag()->add('name', 'Attribute choice option values can not be null!');
                    return response()->json(['errors' => Helpers_error_processor($validator)]);
                }
                $item['name'] = 'choice_' . $no;
                $item['title'] = $request->choice[$key];
                $item['options'] = explode(',', implode('|', preg_replace('/\s+/', ' ', $request[$str])));
                $choiceOptions[] = $item;
            }
        }

        $variations = [];
        $options = [];
        if ($request->has('choice_no')) {
            foreach ($request->choice_no as $key => $no) {
                $name = 'choice_options_' . $no;
                $my_str = implode('|', $request[$name]);
                $options[] = explode(',', $my_str);
            }
        }
       
        $combinations = Helpers_combinations($options);
        $stockCount = 0;
        if (count($combinations[0]) > 0) {
            foreach ($combinations as $key => $combination) {
                $str = '';
                foreach ($combination as $k => $item) {
                    if ($k > 0) {
                        $str .= '-' . str_replace(' ', '', $item);
                    } else {
                        $str .= str_replace(' ', '', $item);
                    }
                }
                $item = [];
                $item['type'] = $str;
                $item['price'] = abs($request['price_' . str_replace('.', '_', $str)]);
                $item['stock'] = abs($request['stock_' . str_replace('.', '_', $str)]);

                if ($request['discount_type'] == 'amount' && $item['price'] <= $request['discount'] ){
                    $validator->getMessageBag()->add('discount_mismatch', 'Discount can not be more or equal to the price. Please change variant '. $item['type'] .' price or change discount amount!');
                }

                $variations[] = $item;
                $stockCount += $item['stock'];
            }
        } else {
            $stockCount = (integer)$request['total_stock'];
        }

        if ((integer)$request['total_stock'] != $stockCount) {
            $validator->getMessageBag()->add('total_stock', 'Stock calculation mismatch!');
        }

        if ($validator->getMessageBag()->count() > 0) {
            return response()->json(['errors' => Helpers_error_processor($validator)]);
        }

        $product->name = $request->name;
        $product->brand_name = json_encode($this->brand->find($request->brand));
        if(isset($request->otherbrand) && !is_null($request->otherbrand))
        {
            $product->brandname_if_other = $request->otherbrand;
        }
        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->description = $request->description;
        $product->choice_options = json_encode($choiceOptions);
        $product->variations = json_encode($variations);
        $product->price = $request->price;
        $product->unit = $request->unit;
        $product->image = json_encode($images);
        $product->tags = json_encode($tags);
        $product->tax = $request->tax_type == 'amount' ? $request->tax : $request->tax;
        $product->tax_type = $request->tax_type;
        $product->discount = $request->discount_type == 'amount' ? $request->discount : $request->discount;
        $product->discount_type = $request->discount_type;
        $product->total_stock = $request->total_stock;
        $product->attributes = $request->has('attribute_id') ? json_encode($request->attribute_id) : json_encode([]);
        $product->status = $request->status? $request->status:0;
        $product->save();
        
        return response()->json([], 200);
    }































    public function ProductAjax(Request $request)
    {
        $request->validate([
            'data' => 'required'
        ]);

        $product = Products::all();
        return $product;
    }

    public function ProductDataAjax(Request $request)
    {
        $request->validate([
            'data' => 'required'
        ]);

        $product = Products::where('id', $request->data)->first();

        return $product;
    }
    



    

    

    

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function dailyNeeds(Request $request): JsonResponse
    {
        $product = $this->product->find($request->id);
        $product->daily_needs = $request->status;
        $product->save();
        return response()->json([], 200);
    }

    

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $product = $this->product->find($request->id);
        foreach (json_decode($product['image'], true) as $img) {
            if (File::exists($img)) {
                File::delete($img);
            }
        }

        
        $product_reviews = ProductReview::where('product_id', $product->id)->get();
        foreach ($product_reviews as $review) {
            $review->delete();
        }
        $product->delete();
        flash()->success(translate('Product removed!'));
        return back();
    }

    

    /**
     * @return Factory|View|Application
     */
    public function bulkImportIndex(): View|Factory|Application
    {
        return view('admin-views.product.bulk-import');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function bulkImportProduct(Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $collections = (new FastExcel)->import($request->file('products_file'));
        } catch (\Exception $exception) {
            flash()->error(translate('You have uploaded a wrong format file, please upload the right file.'));
            return back();
        }
        $columnKey = ['name','description','price','tax','category_id','sub_category_id','discount','discount_type','tax_type','unit','total_stock','capacity','daily_needs'];
        foreach ($collections as $collectionKey => $collection) {
            foreach ($collection as $key => $value) {
                if ($key!="" && !in_array($key, $columnKey)) {
                    flash()->error('Please upload the correct format file.');
                    return back();
                }
            }
        }

        $data = [];
        foreach ($collections as $collection) {

            $data[] = [
                'name' => $collection['name'],
                'description' => $collection['description'],
                'image' => json_encode(['def.png']),
                'price' => $collection['price'],
                'variations' => json_encode([]),
                'tax' => $collection['tax'],
                'status' => 1,
                'attributes' => json_encode([]),
                'category_ids' => json_encode([['id' => (string)$collection['category_id'], 'position' => 0], ['id' => (string)$collection['sub_category_id'], 'position' => 1]]),
                'choice_options' => json_encode([]),
                'discount' => $collection['discount'],
                'discount_type' => $collection['discount_type'],
                'tax_type' => $collection['tax_type'],
                'unit' => $collection['unit'],
                'total_stock' => $collection['total_stock'],
                'capacity' => $collection['capacity'],
                'daily_needs' => $collection['daily_needs'],
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        Products::insert($data);
        flash()->success(count($data) . (translate(' - Products imported successfully!')));
        return back();
    }

    /**
     * @return Factory|View|Application
     */
    public function bulkExportIndex(): View|Factory|Application
    {
        return view('admin-views.product.bulk-export-index');
    }

    /**
     * @param Request $request
     * @return StreamedResponse|string
     * @throws IOException
     * @throws InvalidArgumentException
     * @throws UnsupportedTypeException
     * @throws WriterNotOpenedException
     */
    public function bulkExportProduct(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse|string
    {
        $startDate = $request->type == 'date_wise' ? $request['start_date'] : null;
        $endDate = $request->type == 'date_wise' ? $request['end_date'] : null;

        $products = $this->product->when((!is_null($startDate) && !is_null($endDate)), function ($query) use ($startDate, $endDate) {
            return $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
            })
            ->get();

        $storage = [];
        foreach($products as $item){
            $categoryId = 0;
            $subCategoryId = 0;

            foreach(json_decode($item->category_ids, true) as $category)
            {
                if($category['position']==1)
                {
                    $categoryId = $category['id'];
                }
                else if($category['position']==2)
                {
                    $subCategoryId = $category['id'];
                }
            }

            if (!isset($item['description'])) {
                $item['description'] = 'No description available';
            }

            if (!isset($item['capacity'])) {
                $item['capacity'] = 0;
            }

            $storage[] = [
                'name' => $item['name'],
                'description' => $item['description'],
                'price' => $item['price'],
                'tax' => $item['tax'],
                'category_id'=>$categoryId,
                'sub_category_id'=>$subCategoryId,
                'discount'=>$item['discount'],
                'discount_type'=>$item['discount_type'],
                'tax_type'=>$item['tax_type'],
                'unit'=>$item['unit'],
                'total_stock'=>$item['total_stock'],
                'capacity'=>$item['capacity'],
                'daily_needs'=>$item['daily_needs'],
            ];

        }
        return (new FastExcel($storage))->download('products.xlsx');
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    public function limitedStock(Request $request): View|Factory|Application
    {
        $stockLimit = $this->business_setting->where('key','minimum_stock_limit')->first()->value;
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $query = $this->product->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            })->where('total_stock', '<', $stockLimit)->latest();
            $queryParam = ['search' => $request['search']];
        }else{
            $query = $this->product->where('total_stock', '<', $stockLimit)->latest();
        }

        $products = $query->paginate(Helpers_getPagination())->appends($queryParam);

        return view('admin-views.product.limited-stock', compact('products', 'search', 'stockLimit'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getVariations(Request $request): \Illuminate\Http\JsonResponse
    {
        $product = $this->product->find($request['id']);
        return response()->json([
            'view' => view('admin-views.product.partials._update_stock', compact('product'))->render()
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function updateQuantity(Request $request): \Illuminate\Http\RedirectResponse
    {
        $variations = [];
        $stockCount = $request['total_stock'];
        if ($request->has('type')) {
            foreach ($request['type'] as $key => $str) {
                $item = [];
                $item['type'] = $str;
                $item['price'] = (abs($request['price_' . str_replace('.', '_', $str)]));
                $item['stock'] = abs($request['qty_' . str_replace('.', '_', $str)]);
                $variations[] = $item;
            }
        }

        $product = $this->product->find($request['product_id']);

        if ($stockCount >= 0) {
            $product->total_stock = $stockCount;
            $product->variations = json_encode($variations);
            $product->save();
            flash()->success(translate('product_quantity_updated_successfully!'));
        } else {
            flash()->warning(translate('product_quantity_can_not_be_less_than_0_!'));
        }
        return back();
    }

    public function Edit_product_column(Request $request)
    {
        $request->validate([
            'data' => 'required',
            'value' => 'required'
        ]);

        $data = Products::find($request->data);
        $data->distributed_amount = $request->value;
        $data->save();
        return $request->value;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function feature(Request $request): RedirectResponse
    {
        $product = $this->product->find($request->id);
        $product->is_featured = $request->is_featured;
        $product->save();
        flash()->success(translate('product feature status updated!'));
        return back();
    }
}
