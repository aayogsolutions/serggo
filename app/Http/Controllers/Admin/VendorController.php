<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};
use App\Models\{
    Vendor,
    Order,
    VendorCategory,
};
use Illuminate\Support\Facades\File;

class VendorController extends Controller
{
    public function __construct(
        private Vendor $vendor,
        private Order $order,
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function list(Request $request): Factory|View|Application
    {
        
        $queryParam = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $vendors = $this->vendor->where('role','0')->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                    $q->orWhere('id', 'like', "%{$value}%");
                }
            })->orderBy('id', 'DESC');
            $queryParam = ['search' => $request['search']];
        }else{
            $vendors = $this->vendor->where('role','0')->orderBy('id','DESC');
            
        }
        $vendors = $vendors->paginate(Helpers_getPagination())->appends($queryParam);
        
        return view('Admin.views.vendor.list', compact('vendors','search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $vendor = Vendor::find($request->id);
        $vendor->is_block = $request->status;
        $vendor->save();
        flash()->success(translate('Vendor status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return View|Factory|RedirectResponse|Application
     */
    public function view(Request $request, $id): Factory|View|Application|RedirectResponse
    {
        $vendor = $this->vendor->where('id',$id)->with(['vendororders','Products'])->first();
        
        if (isset($vendor)) {
            $queryParam = [];
            $search = $request['search'];
            if($request->has('search'))
            {
                $key = explode(' ', $request['search']);
                $orders = $this->order->where(['vender_id' => $id])
                    ->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('id', 'like', "%{$value}%")
                                ->orWhere('order_amount', 'like', "%{$value}%");
                        }
                });
                $queryParam = ['search' => $request['search']];
            }else{
                $orders = $this->order->where(['user_id' => $id]);
            }
            $orders = $orders->latest()->paginate(Helpers_getPagination())->appends($queryParam);
            
            return view('Admin.views.vendor.view', compact('vendor', 'orders', 'search'));
        }
        flash()->error(translate('Vendor not found!'));
        return back();
    }

    // Category Setup

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    function index(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $categories = VendorCategory::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $queryParam = ['search' => $request['search']];
            $categories = $categories->orderBY('priority', 'ASC')->paginate(Helpers_getPagination())->appends($queryParam);
        } else {
            $categories = VendorCategory::orderBY('priority', 'ASC')->paginate(Helpers_getPagination())->appends($queryParam);
        }
        return view('Admin.views.vendor.category.index', compact('categories', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required|image',
        ]);
        
        if (strlen($request->name) > 255) {
            flash()->error(translate('Name is too long!'));
            return back();
        }

        $allCategory = VendorCategory::pluck('title')->toArray();

        if (in_array($request->name, $allCategory)) {
            flash()->error(translate('Category already exists!'));
            return back();
        }
        $image = $request->file('image');
            
        $category = new VendorCategory();
        $category->title = $request->name;
        $category->image = Helpers_upload('Images/category/',  $image->getClientOriginalExtension() , $image);;
        $category->save();
        
       
        flash()->success(translate('Category Added Successfully!'));
        return redirect()->route('admin.vendor.category.add');
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $category = VendorCategory::find($id);
        return view('Admin.views.vendor.category.edit', compact('category'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function CategoryStatus(Request $request): RedirectResponse
    {
        $category = VendorCategory::find($request->id);
        $category->status = $request->status;
        $category->save();
        flash()->success(translate('Category status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' =>'required|unique:categories,name,'.$request->id
        ]);
        
        if (strlen($request->name) > 255) {
            flash()->error(translate('Name is too long!'));
            return back();
        }

        $category = VendorCategory::find($id);
        $category->title = $request->name;
        $category->image = $request->has('image') ? Helpers_update('Images/category/', $category->image, $request->file('image')->getClientOriginalExtension(), $request->file('image')) : $category->image;
        $category->save();
        
        flash()->success(translate('Category updated Successfully!'));
        return redirect()->route('admin.vendor.category.add');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $category = VendorCategory::find($request->id);
        if ($category->childes->count() == 0) {
            if (File::exists($category['image'])) {
                File::delete($category['image']);
            }
            $category->delete();
            flash()->success($category->parent_id == 0 ? translate('Category removed!') : translate('Sub Category removed!'));
        } else {
            flash()->warning($category->parent_id == 0 ? translate('Remove subcategories first!') : translate('Sub Remove subcategories first!'));
        }
       
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function priority(Request $request): RedirectResponse
    {
        $category = VendorCategory::find($request->id);
        $category->priority = $request->priority;
        $category->save();

        flash()->success(translate('priority updated!'));
        return back();
    }
}
