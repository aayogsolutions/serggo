<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin_roles;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomRoleController extends Controller
{
    public  function __construct(
        private Admin_roles $adminRole
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function create(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request->search;
        if (auth('admins')->user()->id == 1 || auth('admins')->user()->id == 2) {
            if($request->has('search'))
            {
                $key = explode(' ', $request->search);
                $adminRoles = $this->adminRole->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
                $queryParam = ['search' => $request->search];
            }else{
                $adminRoles = $this->adminRole->whereNotIn('id',[1]);
            }
            $adminRoles = $adminRoles->latest()->paginate(Helpers_getPagination())->appends($queryParam);
        }else{
            $id = auth('admins')->user()->id;

            if($request->has('search'))
            {
                $key = explode(' ', $request->search);
                $adminRoles = $this->adminRole->where('id',$id)->orWhere(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
                $queryParam = ['search' => $request->search];
            }else{
                $adminRoles = $this->adminRole->where('id',$id)->whereNotIn('id',[1]);
            }
            $adminRoles = $adminRoles->latest()->paginate(Helpers_getPagination())->appends($queryParam);
        }
        

        return view('Admin.views.custom-role.create',compact('adminRoles', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        
        $request->validate([
            'name' => 'required|unique:admin_roles',
        ],[
            'name.required'=>translate('Role name is required!')
        ]);

        $module = [];
        
        if (!isset($request['order_management'])) 
        {
            if (isset($request['manage_order'])) {
                $request['order_management'] = 'order_management';
            }
        }
        if (!isset($request['product_management'])) 
        {
            if (isset($request['brand_setup'])) {
                $request['product_management'] = 'product_management';
            }elseif (isset($request['category_setup'])) {
                $request['product_management'] = 'product_management';
            }elseif (isset($request['product_setup'])) {
                $request['product_management'] = 'product_management';
            }elseif (isset($request['product_approval'])) {
                $request['product_management'] = 'product_management';
            }

        }
        if (!isset($request['promotion_management'])) 
        {
            if (isset($request['banner'])) {
                $request['promotion_management'] = 'promotion_management';
            }elseif (isset($request['display'])) {
                $request['promotion_management'] = 'promotion_management';
            }elseif (isset($request['coupons'])) {
                $request['promotion_management'] = 'promotion_management';
            }elseif (isset($request['send_notification'])) {
                $request['promotion_management'] = 'promotion_management';
            }
        }
        if (!isset($request['report_management'])) 
        {
            if (isset($request['sales_report'])) {
                $request['report_management'] = 'report_management';
            }elseif (isset($request['order_report'])) {
                $request['report_management'] = 'report_management';
            }elseif (isset($request['earning_report'])) {
                $request['report_management'] = 'report_management';
            }
        }
        if (!isset($request['user_management'])) 
        {
            if (isset($request['customer_list'])) {
                $request['user_management'] = 'user_management';
            }elseif (isset($request['vender_list'])) {
                $request['user_management'] = 'user_management';
            }elseif (isset($request['serviceman_list'])) {
                $request['user_management'] = 'user_management';
            }elseif (isset($request['coustomer_wallet'])) {
                $request['user_management'] = 'user_management';
            }elseif (isset($request['product_reviews'])) {
                $request['user_management'] = 'user_management';
            }elseif (isset($request['employees'])) {
                $request['user_management'] = 'user_management';
            }
        }
        if (!isset($request['system_management'])) 
        {
            if (isset($request['business_setup'])) {
                $request['system_management'] = 'system_management';
            }elseif (isset($request['branch_setup'])) {
                $request['system_management'] = 'system_management';
            }elseif (isset($request['pages_media'])) {
                $request['system_management'] = 'system_management';
            }
        }

        $modules = $request->all();
        
        array_shift($modules);
        array_shift($modules);

        foreach ($modules as $key => $value) {
            array_push($module,$value);
        }

        // dd($module);
        if($module == null) {
            flash()->error(translate('Select at least one module permission'));
            return back();
        }

        Admin_roles::insert([
            'name'=>$request->name,
            'module_access'=>json_encode($module),
            'status'=> 0,
            'created_at'=>now(),
            'updated_at'=>now()
        ]);

        flash()->success(translate('Role added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $role = $this->adminRole->where(['id'=>$id])->first(['id','name','module_access']);
        return view('Admin.views.custom-role.edit',compact('role'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return Application|RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse|Application
    {
        $request->validate([
            'name' => 'required',
        ],[
            'name.required'=> translate('Role name is required!')
        ]);

        $module = [];
        
        if (!isset($request['order_management'])) 
        {
            if (isset($request['manage_order'])) {
                $request['order_management'] = 'order_management';
            }
        }
        if (!isset($request['product_management'])) 
        {
            if (isset($request['category_setup'])) {
                $request['product_management'] = 'product_management';
            }elseif (isset($request['product_setup'])) {
                $request['product_management'] = 'product_management';
            }elseif (isset($request['product_approval'])) {
                $request['product_management'] = 'product_management';
            }

        }
        if (!isset($request['promotion_management'])) 
        {
            if (isset($request['banner'])) {
                $request['promotion_management'] = 'promotion_management';
            }elseif (isset($request['coupons'])) {
                $request['promotion_management'] = 'promotion_management';
            }elseif (isset($request['send_notification'])) {
                $request['promotion_management'] = 'promotion_management';
            }elseif (isset($request['offers'])) {
                $request['promotion_management'] = 'promotion_management';
            }elseif (isset($request['category_discount'])) {
                $request['promotion_management'] = 'promotion_management';
            }
        }
        if (!isset($request['report_management'])) 
        {
            if (isset($request['sales_report'])) {
                $request['report_management'] = 'report_management';
            }elseif (isset($request['order_report'])) {
                $request['report_management'] = 'report_management';
            }elseif (isset($request['earning_report'])) {
                $request['report_management'] = 'report_management';
            }
        }
        if (!isset($request['user_management'])) 
        {
            if (isset($request['customer_list'])) {
                $request['user_management'] = 'user_management';
            }elseif (isset($request['vender_list'])) {
                $request['user_management'] = 'user_management';
            }elseif (isset($request['serviceman_list'])) {
                $request['user_management'] = 'user_management';
            }elseif (isset($request['coustomer_wallet'])) {
                $request['user_management'] = 'user_management';
            }elseif (isset($request['product_reviews'])) {
                $request['user_management'] = 'user_management';
            }elseif (isset($request['employees'])) {
                $request['user_management'] = 'user_management';
            }
        }
        if (!isset($request['system_management'])) 
        {
            if (isset($request['business_setup'])) {
                $request['system_management'] = 'system_management';
            }elseif (isset($request['branch_setup'])) {
                $request['system_management'] = 'system_management';
            }elseif (isset($request['pages_media'])) {
                $request['system_management'] = 'system_management';
            }
        }

        $modules = $request->all();

        array_shift($modules);
        array_shift($modules);

        foreach ($modules as $key => $value) {
            array_push($module,$value);
        }

        Admin_roles::where(['id'=>$id])->update([
            'name'=>$request->name,
            'module_access'=>json_encode($module),
            'status'=>1,
            'updated_at'=>now()
        ]);

        flash()->success(translate('Role updated successfully!'));
        return redirect(route('admin.custom-role.create'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $role = $this->adminRole->find($request->id);
        $role->delete();
        flash()->success(translate('Role removed!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $role = $this->adminRole->find($request->id);
        $role->status = $request->status;
        $role->save();
        flash()->success(translate('Role status updated!'));
        return back();
    }
}
