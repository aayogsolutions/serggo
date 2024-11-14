<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    ServiceCategory, 
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
        return view('Admin.views.services.service-list');
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
    
}
