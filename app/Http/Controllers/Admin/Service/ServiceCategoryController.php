<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\RedirectResponse;
use App\Models\{
    Category, 
    ServiceCategory, 
    DisplaySectionContent, 
    HomeSliderBanner,  
    Service
};
use Illuminate\Support\Facades\File;

class ServiceCategoryController extends Controller
{
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
            $categories = ServiceCategory::where(['position' => 0])->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $queryParam = ['search' => $request['search']];
        } else {
            $categories = ServiceCategory::where(['position' => 0]);
        }
        $categories = $categories->orderBY('priority', 'ASC')->paginate(Helpers_getPagination())->appends($queryParam);
        return view('Admin.views.services.ServiceCategory.index', compact('categories', 'search'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    function subIndex(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $categories = ServiceCategory::with(['parent'])->where(['position' => 1])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            $queryParam = ['search' => $request['search']];
        } else {
            $categories = ServiceCategory::with(['parent'])->where(['position' => 1]);
        }
        $categories = $categories->latest()->paginate(Helpers_getPagination())->appends($queryParam);
        return view('Admin.views.services.ServiceCategory.sub-index', compact('categories', 'search'));
    }

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    function childIndex(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $categories = ServiceCategory::with(['parent'])->where(['position' => 1])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            $queryParam = ['search' => $request['search']];
        } else {
            $categories = ServiceCategory::with(['parent'])->where(['position' => 2]);
        }
        $categories = $categories->latest()->paginate(Helpers_getPagination())->appends($queryParam);
        return view('Admin.views.services.ServiceCategory.child-index', compact('categories', 'search'));
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
        
        $parentId = $request->parent_id ?? 0;
        $allCategory = ServiceCategory::where(['parent_id' => $parentId])->pluck('name')->toArray();

        if (in_array($request->name, $allCategory)) {
            flash()->error(translate(($request->parent_id == null ? 'Category' : 'Sub_category') . ' already exists!'));
            return back();
        }
        $image = $request->file('image');
            
        $category = new ServiceCategory();
        $category->name = $request->name;
        $category->image = Helpers_upload('Images/category/',  $image->getClientOriginalExtension() , $image);;
        $category->parent_id = $request->parent_id ?? 0;
        $category->position = $request->position;
        $category->fees = $request->fees;
        $category->save();
        
        if($request->parent_id == 0)
        {
            flash()->success(translate('Category Added Successfully!'));
            return redirect()->route('admin.service.category.add');
        }else{
            flash()->success(translate('Sub Category Added Successfully!'));
            return redirect()->route('admin.service.category.add-sub-category');
        }
        
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    function Childstore(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required|image',
        ]);
        
        if (strlen($request->name) > 255) {
            flash()->error(translate('Name is too long!'));
            return back();
        }
        
        // $parentId = $request->parent_id ?? 0;
        // $allCategory = ServiceCategory::where(['parent_id' => $parentId])->pluck('name')->toArray();

        // if (in_array($request->name, $allCategory)) {
        //     flash()->error(translate(($request->parent_id == null ? 'Category' : 'Sub_category') . ' already exists!'));
        //     return back();
        // }
        $image = $request->file('image');
            
        $child_category = new ServiceCategory();
        $child_category->name = $request->name;
        $child_category->image = Helpers_upload('Images/category/',  $image->getClientOriginalExtension() , $image);;
        $child_category->parent_id = $request->parent_id;
        $child_category->position = $request->position;
        $child_category->save();       
    
        flash()->success(translate('Child Category Added Successfully!'));
        return redirect()->route('admin.service.category.add-child-category');
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $category = ServiceCategory::find($id);
        return view('Admin.views.services.ServiceCategory.edit', compact('category'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $category = ServiceCategory::find($request->id);
        
        if($category->position == 0 || $category->position == 1)
        {
            if(Category::where(['parent_id' => $category->id])->exists())
            {
                flash()->error(translate('Childs of this Category! exists!'));
                return back();
            }
        }else{
            if(Service::where(['child_category_id' => $category->id])->exists())
            {
                flash()->error(translate('Product exists!'));
                return back();
            }
        }
        if($category->position == 0)
        {
            if(HomeSliderBanner::where(['item_type' => 'service', 'item_id' => $request->id])->exists())
            {
                flash()->warning(translate('Slider Banner is available for this Category! Cannot Change Status!'));
                return back();
            }

            if(DisplaySectionContent::where(['item_type' => 'service', 'item_id' => $request->id])->exists())
            {
                flash()->warning(translate('Display Section Content is available for this Category! Cannot Change Status!'));
                return back();
            }
        }

        
        $category->status = $request->status;
        $category->save();
        flash()->success($category->parent_id == 0 ? translate('Category status updated!') : translate('Sub Category status updated!'));
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

        $category = ServiceCategory::find($id);
        $category->name = $request->name;
        $category->fees = $request->fees;
        $category->image = $request->has('image') ? Helpers_update('Images/category/', $category->image, $request->file('image')->getClientOriginalExtension(), $request->file('image')) : $category->image;
        $category->save();
        
        if($category->position == 0)
        {
            flash()->success(translate('Service Category updated Successfully!'));
            return redirect()->route('admin.service.category.add');
        }elseif($category->position == 1){
            flash()->success(translate('Service Sub Category updated Successfully!'));
            return redirect()->route('admin.service.category.add-sub-category');
        }else{
            flash()->success(translate('Service child Category updated Successfully!'));
            return redirect()->route('admin.service.category.add-child-category');
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        
        $category = ServiceCategory::find($request->id);

        if($category->position == 0 || $category->position == 1)
        {
            if(Category::where(['parent_id' => $category->id])->exists())
            {
                flash()->error(translate('Childs of this Category! exists!'));
                return back();
            }
        }else{
            if(Service::where(['child_category_id' => $category->id])->exists())
            {
                flash()->error(translate('Product exists!'));
                return back();
            }
        }
        if($category->position == 0)
        {
            if(HomeSliderBanner::where(['item_type' => 'service', 'item_id' => $request->id])->exists())
            {
                flash()->warning(translate('Slider Banner is available for this Category! Cannot delete!'));
                return back();
            }

            if(DisplaySectionContent::where(['item_type' => 'service', 'item_id' => $request->id])->exists())
            {
                flash()->warning(translate('Display Section Content is available for this Category! Cannot delete!'));
                return back();
            }
        }
        
        if ($category->childes->count() == 0) {
            if (File::exists($category['image'])) {
                File::delete($category['image']);
            }
            $category->delete();
            flash()->success($category->parent_id == 0 ? translate('Service Category removed!') : translate('Service Sub Category removed!'));
        } else {
            flash()->warning($category->parent_id == 0 ? translate('Service Remove subcategories first!') : translate('Service Sub Remove subcategories first!'));
        }
       
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function priority(Request $request): RedirectResponse
    {
        $category = ServiceCategory::find($request->id);
        $category->priority = $request->priority;
        $category->save();

        flash()->success(translate('priority updated!'));
        return back();
    }
}
