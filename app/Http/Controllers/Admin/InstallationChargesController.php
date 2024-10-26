<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InstallationCharges;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};

class InstallationChargesController extends Controller
{
    public function __construct(
        private InstallationCharges   $installation,
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
            $installations = $this->installation->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('installation_name', 'like', "%{$value}%");
                }
            })->orderBy('installation_name');
            $queryParam = ['search' => $request['search']];
        } else {
            $installations = $this->installation->orderBy('installation_name');
        }
        $installations = $installations->latest()->paginate(Helpers_getPagination())->appends($queryParam);
        return view('Admin.views.InstallationCharges.index', compact('installations', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'installation_name' => 'required',
            'description' => 'required',
            'charges' => 'required'
        ], [
            'name.required' => translate('Name is required'),
            'name.unique' => translate('Name is already taken'),
        ]);
        
       
        if (strlen($request->installation_name) > 255) {
            flash()->error(translate('Name is too long!'));
            return back();
        }
       
        $installations = $this->installation;
        $installations->installation_name = $request->installation_name;
        $installations->installation_description = $request->description;
        $installations->installation_charges = $request->charges;
        $installations->status = 0 ;
        $installations->save();

        flash()->success(translate('Installation added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $installations = $this->installation->find($id);
        return view('Admin.views.InstallationCharges.edit', compact('installations'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'installation_name' => 'required',
            'description' => 'required',
            'charges' => 'required'
        ], [
            'name.required' => translate('Name is required'),
        ]);
        
        if (strlen($request->installation_name) > 255) {
            flash()->error(translate('Name is too long!'));
            return back();
        }

        $installations = $this->installation->find($id);
        $installations->installation_name = $request->installation_name;
        $installations->installation_description = $request->description;
        $installations->installation_charges = $request->charges;
        $installations->status = 0 ;
        $installations->save();

        flash()->success(translate('Installation updated successfully!'));
        return redirect()->route('admin.installation.add-new');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $installations = $this->installation->find($request->id);
        $installations->delete();
        flash()->success(translate('Installation removed!'));
        return back();
    }

     /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $installations = $this->installation->find($request->id);
        $installations->status = $request->status;
        $installations->save();
        flash()->success(translate('Installation status updated!'));
        return back();
    }
}
