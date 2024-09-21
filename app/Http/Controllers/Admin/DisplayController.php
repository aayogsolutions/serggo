<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    Category,
    DisplaySection,
    DisplaySectionContent,
    Products,
};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\File;

class DisplayController extends Controller
{
    public function __construct(
        private DisplaySection $displaysection,
        private DisplaySectionContent $displaysectioncontent,
        private Products $product,
        private Category $category
    ) {}

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function Index(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $banners = $this->displaysection->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                    $q->orWhere('ui_type', 'like', "%{$value}%");
                    $q->orWhere('section_type', 'like', "%{$value}%");
                }
            })->orderBy('id', 'desc');
            $queryParam = ['search' => $request['search']];
        } else {
            $banners = $this->displaysection->orderBy('id', 'desc')->with('childes');
        }
        $banners = $banners->with('childes')->paginate(Helpers_getPagination())->appends($queryParam);

        $data['slider'] = $this->displaysection->where([
            ['section_type', '=','slider'],
            ['status', '=', 0],
            ])->with('childes')->get();
        $data['cart'] = $this->displaysection->where([
            ['section_type', '=','cart'],
            ['status', '=', 0],
            ])->with('childes')->get();
        $data['box_section'] = $this->displaysection
        ->where([
            ['section_type', '=','box_section'],
            ['status', '=', 0],
        ])
        ->with('childes', function($q) {
            $q->whereNotIn('priority' , [0]);
        })->get();
        
        return view('Admin.views.display.index', compact('banners', 'search','data'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function Store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'type' => 'required',
            'section_type' => 'required',
        ], [
            'title.required' => translate('Title is required'),
            'type.required' => translate('Type is required'),
            'section_type.required' => translate('Section Type is required'),
        ]);
       
        try {

            $banner = $this->displaysection;
            $banner->title = $request->title;
            $banner->ui_type = $request->type;
            $banner->section_type = $request->section_type;
            $banner->save();
            flash()->success(translate('Banner added successfully!'));
            return back();
        } catch (\Throwable $th) {
            flash()->error(translate('Details are wrong.!'));
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function Status(Request $request): RedirectResponse
    {
        $allbanner = $this->displaysection->where([
            ['id','!=',$request->id],
            ['ui_type','=',$request->type],
            ['section_type','=',$request->section_type],
        ])->get();

        foreach ($allbanner as $key => $value) {
            $banner = $this->displaysection->find($value->id);
            $banner->status = 1;
            $banner->save();
        }

        $banner = $this->displaysection->find($request->id);
        $banner->status = $request->status;
        $banner->save();

        flash()->success(translate('Banner status updated!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function Edit($id): View|Factory|Application
    {
        $banner = $this->displaysection->where('id',$id)->with('childes')->first();

        $products = $this->product->status()->orderBy('name')->get();
        $categories = $this->category->status()->where('parent_id',0)->orderBy('name')->get();

        return view('Admin.views.display.sub-index', compact('banner','categories','products'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function AddContent(Request $request, $id): RedirectResponse
    {
        // dd($request->all(),$id);
        $request->validate([
            'type' => 'required',
        ], [
            'type.required' => translate('UI Type is required'),
        ]);

        if ($request->type == 'box_section') 
        {
            $request->validate([
                'image' => 'required|image',
                'item_type' => 'required',
            ], [
                'image.required' => translate('Image is required'),
                'item_type.required' => translate('Item Type is required'),
            ]);

            $file_size = getimagesize($request->file('image'));
            // Width Check              Height Check
            if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
                $banner = $this->displaysectioncontent;
                $banner->section_id = $id;
                $banner->item_type = $request->item_type;
                if($request->item_type == 'product')
                {
                    $data = $this->product->find($request->product_id);
                    $banner->item_id = $request->product_id;
                    $banner->item_detail = $data;
                }else{
                    $data = $this->category->find($request->category_id);
                    $banner->item_id = $request->category_id;
                    $banner->item_detail = $data;
                }
                $banner->attechment = Helpers_update('Images/banners/', $banner->attechment , $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->save();
                flash()->success(translate('Item Added successfully!'));
                return redirect()->back();
            }
            flash()->error(translate('Image size is wrong.!'));
            return redirect()->back();
        } 
        elseif ($request->type == 'cart') 
        {
            $request->validate([
                'product_id' => 'required',
            ], [
                'product_id.required' => translate('Product Id is required'),
            ]);
            
            $banner = $this->displaysectioncontent;
            $banner->section_id = $id;
            $banner->item_type = 'product';

            $data = $this->product->find($request->product_id);
            $banner->item_id = $request->product_id;
            $banner->item_detail = $data;
            $banner->save();

            flash()->success(translate('Product Added successfully!'));
            return redirect()->back();
        }else{
            $request->validate([
                'image' => 'required|image',
                'product_id' => 'required',
            ], [
                'image.required' => translate('Image is required'),
                'product_id.required' => translate('Product Id is required'),
            ]);

            $file_size = getimagesize($request->file('image'));
            // Width Check              Height Check
            if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
                $banner = $this->displaysectioncontent;
                $banner->section_id = $id;
                $banner->item_type = 'product';
                
                $data = $this->product->find($request->product_id);
                $banner->item_id = $request->product_id;
                $banner->item_detail = $data;
                
                $banner->attechment = Helpers_update('Images/banners/', $banner->attechment , $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->save();
                flash()->success(translate('Item Added successfully!'));
                return redirect()->back();
            }
            flash()->error(translate('Image size is wrong.!'));
            return redirect()->back();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function DetailSection(Request $request): JsonResponse
    {
        try {

            $data = $this->displaysection->find($request->id);
            return response()->json([
                'success' => true,
                'data' => $data
            ],200);
        } catch (\Throwable $th) {

            return response()->json([
                'error' => true,
                'data' => 'Unexpected Error'
            ]);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function DetailItem(Request $request): JsonResponse
    {
        try {
            // dd($request->all());
            $data = $this->displaysectioncontent->find($request->id);
            return response()->json([
                'success' => true,
                'data' => $data
            ],200);
        } catch (\Throwable $th) {

            return response()->json([
                'error' => true,
                'data' => 'Unexpected Error'
            ]);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function UpdateSection(Request $request): RedirectResponse
    {
        // dd($request->all());
        $request->validate([
            'type' => 'required',
            'title' => 'required',
            'id' => 'required',
            'section_type' => 'required',
        ], [
            'type.required' => translate('UI Type is required'),
            'title.required' => translate('title is required'),
            'id.required' => translate('Unexpected Error'),
            'section_type.required' => translate('section type is required'),
        ]);

        try {

            $banner = $this->displaysection->find($request->id);
            $banner->title = $request->title;
            $banner->ui_type = $request->type;
            $banner->section_type = $request->section_type;
            $banner->save();

            flash()->success(translate('Section Updated successfully!'));
            return redirect()->back();
        } catch (\Throwable $th) {
            flash()->success(translate('Section Not Updated successfully!'));
            return redirect()->back();
        }      
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function UpdateItem(Request $request): RedirectResponse
    {
        // dd($request->all());
        $request->validate([
            'type' => 'required',
            'title' => 'required',
            'id' => 'required',
            'section_type' => 'required',
        ], [
            'type.required' => translate('UI Type is required'),
            'title.required' => translate('title is required'),
            'id.required' => translate('Unexpected Error'),
            'section_type.required' => translate('section type is required'),
        ]);

        try {

            $banner = $this->displaysectioncontent->find($request->id);
            $banner->title = $request->title;
            $banner->ui_type = $request->type;
            $banner->section_type = $request->section_type;
            $banner->save();

            flash()->success(translate('Item Updated successfully!'));
            return redirect()->back();
        } catch (\Throwable $th) {
            flash()->success(translate('Item Not Updated successfully!'));
            return redirect()->back();
        }      
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function DeleteSection(Request $request): RedirectResponse
    {
        $banner = $this->displaysection->find($request->id);
        if ($banner->exists()) {
            $banner->with('childes')->delete();

            flash()->success(translate('Section removed!'));
            return back();
        }else{
            
            flash()->success(translate('Section not Exists!'));
            return back();
        }
        
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function DeleteContent(Request $request): RedirectResponse
    {
        $banner = $this->displaysectioncontent->find($request->id);
        if ($banner->exists()) {
            $banner->delete();

            flash()->success(translate('Item removed!'));
            return back();
        }else{
            
            flash()->success(translate('Item not Exists!'));
            return back();
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function priority(Request $request): RedirectResponse
    {
        $categorymain = $this->displaysectioncontent->where('priority',$request->priority)->first();
        if($categorymain != null){
            $categorymain->priority = 0;
            $categorymain->save();
        }

        $category = $this->displaysectioncontent->find($request->id);
        $category->priority = $request->priority;
        $category->save();

        flash()->success(translate('priority updated!'));
        return back();
    }
}
