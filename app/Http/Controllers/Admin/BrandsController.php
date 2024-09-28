<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brands;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\File;

class BrandsController extends Controller
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
            $brands = Brands::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $queryParam = ['search' => $request['search']];
            $brands = $brands->orderBY('priority', 'ASC')->paginate(Helpers_getPagination())->appends($queryParam);
        } else {
            $brands = Brands::orderBY('priority', 'ASC')->paginate(Helpers_getPagination())->appends($queryParam);
        }
        
        return view('Admin.views.brands.index', compact('brands', 'search'));
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

        $allbrands = Brands::pluck('name')->toArray();

        if (in_array($request->name, $allbrands)) {
            flash()->error(translate('Brand name already exists!'));
            return back();
        }
        
        $image = $request->file('image');
        $imageName = Helpers_upload('Images/brands/',  $image->getClientOriginalExtension() , $image);
        
        $category = new Brands();
        $category->name = lcfirst($request->name);
        $category->image = $imageName;
        $category->save();
        
        flash()->success(translate('Brand Added Successfully!'));
        return redirect()->route('admin.brands.add');
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $brand = Brands::find($id);
        return view('Admin.views.brands.edit', compact('brand'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $category = Brands::find($request->id);
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
            'name' =>'required|unique:categories,name,'.$request->id,
        ]);
        
        if (strlen($request->name) > 255) {
            flash()->error(translate('Name is too long!'));
            return back();
        }

        $brands = Brands::find($id);
        $brands->name = lcfirst($request->name);
        $brands->image = $request->has('image') ? Helpers_update('Images/brands/', $brands->Image, $request->file('image')->getClientOriginalExtension(), $request->file('image')) : $brands->image;
        $brands->save();
        
        flash()->success(translate('Brands updated Successfully!'));
        return redirect()->route('admin.brands.add');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $brands = Brands::find($request->id);
        if ($brands->childes->count() == 0) {
            if (File::exists($brands['Image'])) {
                File::delete($brands['Image']);
            }
            $brands->delete();
            flash()->success(translate('Brand removed!'));
        } else {
            flash()->warning(translate('brand has Products!'));
        }
       
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function priority(Request $request): RedirectResponse
    {
        $category = Brands::find($request->id);
        $category->priority = $request->priority;
        $category->save();

        flash()->success(translate('priority updated!'));
        return back();
    }

}
