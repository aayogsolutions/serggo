<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Admin,Admin_roles};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;

class EmployeeController extends Controller
{
    public function __construct(
        private Admin $admin,
        private Admin_roles $adminRole
    ){}

    /**
     * @return Factory|View|Application
     */
    public function index(): View|Factory|Application
    {
        $adminRoles = $this->adminRole->whereNotIn('id', [1])->get();
        return view('Admin.views.employee.add-new', compact('adminRoles'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'role_id' => 'required',
            'image' => 'required',
            'email' => 'required|email|unique:admins',
            'phone'=>'required',
            'password' => 'required|min:8',
            'password_confirmation' => 'required_with:password|same:password|min:8'

        ], [
            'name.required' => translate('Role name is required!'),
            'role_id.required' => translate('Role ID is required!'),
            'role_name.required' => translate('Role id is Required'),
            'email.required' => translate('Email id is Required'),
            'image.required' => translate('Image is Required'),

        ]);

        if ($request->role_id == 1) 
        {
            flash()->warning(translate('Access Denied!'));
            return back();
        }
        if ($request->has('image')) {
            $imageName = Helpers_upload('Images/Employees/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
        } else {
            $imageName = 'def.png';
        }

        $identityImageNames = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                $identityImage = Helpers_upload('Images/EmployeeIdentity/', $img->getClientOriginalExtension(), $img);
                $identityImageNames[] = $identityImage;
            }
            $identityImage = json_encode($identityImageNames);
        } else {
            $identityImage = json_encode([]);
        }

       
        Admin::insert([
            
            'name' => $request->name,
            'number' => $request->phone,
            'email' => $request->email,
            'identity_number' => $request->identity_number,
            'identity_type' => $request->identity_type,
            'identity_image' => $identityImage,
            'role_id' => $request->role_id,
            'password' => bcrypt($request->password),
            'status'=> 0,
            'image' => $imageName,
            'created_at' => now(),
            'updated_at' => now(),
            
        ]);


        flash()->success(translate('Employee added successfully!'));
        return redirect()->route('admin.employee.list');
    }

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function list(Request $request): View|Factory|Application
    {
        $search = $request['search'];
        $key = explode(' ', $request['search']);

        $query = $this->admin->with(['role'])
            ->when($search != null, function ($query) use ($key) {
                $query->whereNotIn('id', [1])->where(function ($query) use ($key) {
                    foreach ($key as $value) {
                        $query->where('f_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
            }, function ($query) {
                $query->whereNotIn('id', [1]);
            });

        $sql = $query->toSql();
        $employees = $query->paginate(Helpers_getPagination());

        return view('Admin.views.employee.list', compact('employees','search'));
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $employee = $this->admin->where(['id' => $id])->first();
        $adminRoles = $this->adminRole->whereNotIn('id', [1])->get();
        return view('Admin.views.employee.edit', compact('adminRoles', 'employee'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'role_id' => 'required',
            'email' => 'required|email|unique:admins,email,'.$id,
            'password_confirmation' => 'required_with:password|same:password'
        ], [
            'name.required' => translate('name is required!'),
        ]);

        if ($request->role_id == 1) {
            flash()->warning(translate('Access Denied!'));
            return back();
        }

        $employee = $this->admin->find($id);
        if ($request['password'] == null) {
            $password = $employee['password'];
        } else {
            if (strlen($request['password']) < 7) {
                flash()->warning(translate('Password length must be 8 character.'));
                return back();
            }
            $password = bcrypt($request['password']);
        }

        if ($request->has('image')) {
            $employee['image'] = Helpers_update('images/Employees/', $employee['image'], 'png', $request->file('image'));
        }

        if ($request->has('identity_image')){
            foreach (json_decode($employee['identity_image'], true) as $img) {
                if (File::exists($img)) {
                    File::delete($img);
                }
            }
            $imgKeeper = [];
            foreach ($request->identity_image as $img) {
                $identityImage = Helpers_upload('Images/EmployeeIdentity/', $img->getClientOriginalExtension(), $img);
                $imgKeeper[] = $identityImage;
            }
            $identityImage = json_encode($imgKeeper);
        } else {
            $identityImage = $employee['identity_image'];
        }

        Admin::where(['id' => $id])->update([
            'f_name' => $request->first_name,
            'l_name' => $request->last_name,
            'number' => $request->phone,
            'email' => $request->email,
            'identity_number' => $request->identity_number,
            'identity_type' => $request->identity_type,
            'identity_image' => $identityImage,
            'role_id' => $request->role_id,
            'password' => $password,
            'image' => $employee['image'],
            'updated_at' => now(),
        ]);

        flash()->success(translate('Employee updated successfully!'));
        return redirect()->route('admin.employee.list');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $employee = $this->admin->find($request->id);
        $employee->status = $request->status;
        $employee->save();

        flash()->success(translate('Employee status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $employee = $this->admin->where('id', $request->id)->whereNotIn('id', [1])->first();
        $employee->delete();
        flash()->success(translate('Employee removed!'));
        return back();
    }
}
