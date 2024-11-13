<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attributes;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};

class ServiceAttributeController extends Controller
{
    public function __construct(
        private ServiceAttribute   $serviceattribute,
    ){}

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function index(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $attributes = $this->serviceattribute->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            })->orderBy('name');
            $queryParam = ['search' => $request['search']];
        } else {
            $attributes = $this->serviceattribute->orderBy('name');
        }
        $attributes = $attributes->latest()->paginate(Helpers_getPagination())->appends($queryParam);
        return view('Admin.views.service-attribute.index', compact('attributes', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:attributes',
        ], [
            'name.required' => translate('Name is required'),
            'name.unique' => translate('Name is already taken'),
        ]);
        
        if (strlen($request->name) > 255) {
            flash()->error(translate('Name is too long!'));
            return back();
        }

        $attribute = $this->serviceattribute;
        $attribute->name = $request->name;
        $attribute->save();

        flash()->success(translate('Service Attribute added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $attribute = $this->serviceattribute->find($id);
        return view('Admin.views.service-attribute.edit', compact('attribute'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:attributes,name,' . $request->id,
        ], [
            'name.required' => translate('Name is required'),
        ]);
        
        if (strlen($request->name) > 255) {
            flash()->error(translate('Name is too long!'));
            return back();
        }

        $attribute = $this->serviceattribute->find($id);
        $attribute->name = $request->name;
        $attribute->save();

        flash()->success(translate('Service Attribute updated successfully!'));
        return redirect()->route('admin.attribute.add-new');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $attribute = $this->serviceattribute->find($request->id);
        $attribute->delete();
        flash()->success(translate('Service Attribute removed!'));
        return back();
    }
}
