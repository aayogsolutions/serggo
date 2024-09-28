<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;


class BranchController extends Controller
{
    public function __construct(
        private Branch $branch
    ){}

    /**
     * @param Request $request
     * @return Application|Factory|View
     */
    public function index(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $branches = $this->branch->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('name', 'like', "%{$value}%");
                        }
            })->orderBy('id', 'desc');
            $queryParam = ['search' => $request['search']];
        }else{
           $branches = $this->branch->orderBy('id', 'desc');
        }
        $branches = $branches->paginate(Helpers_getPagination())->appends($queryParam);
        return view('Admin.views.branch.add-new', compact('branches','search'));
    }

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
            $branches = $this->branch->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                    $q->orWhere('id', 'like', "%{$value}%");
                }
            })->orderBy('id', 'desc');
            $queryParam = ['search' => $request['search']];
        }else{
            $branches = $this->branch->orderBy('id', 'desc');
        }
        $branches = $branches->paginate(Helpers_getPagination())->appends($queryParam);
        return view('Admin.views.branch.list', compact('branches','search'));
    }

    /**
     * @param Request $request
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request): Redirector|RedirectResponse|Application
    {
        $request->validate([
            'name' => 'required|max:255|unique:branches',
            'email' => 'required|max:255|unique:branches',
            'password' => 'required|min:8|max:255',
            'image' => 'required|max:2048',
            'longitude' => 'required',
            'latitude' => 'required',
            'coverage' => 'required',
        ], [
            'name.required' => translate('Name is required!'),
            'name.unique' => translate('Name must be unique'),
            'email.required' => translate('Email is required!'),
            'email.unique' => translate('Email must be unique'),
            'password.required' => translate('Password is required!'),
            'Image.required' => translate('Image is required!'),
        ]);

        if (!empty($request->file('image'))) {
            $imageName = Helpers_upload('Images/branch/', 'png', $request->file('image'));
        } else {
            $imageName = 'def.png';
        }

        $branch = $this->branch;
        $branch->admin_id = Auth::guard('admins')->id();
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->phone = $request->phone;
        $branch->longitude = $request->longitude;
        $branch->latitude = $request->latitude;
        $branch->coverage = $request->coverage ? $request->coverage : 0;
        $branch->address = $request->address;
        $branch->password = bcrypt($request->password);
        $branch->image = $imageName;
        $branch->save();

        try {
            $emailServices = Helpers_get_business_settings('mail_config');
            // if (isset($emailServices['status']) && $emailServices['status'] == 1) {
            //     Mail::to($branch->email)->send(new \App\Mail\Branch\BranchRegistration($branch, $request->password));
            // }
        } catch (\Exception $e) {
        }

        flash()->success(translate('Branch added successfully!'));
        return redirect('admin/branch/list');
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $branch = $this->branch->find($id);
        return view('Admin.views.branch.edit', compact('branch'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => ['required', 'unique:branches,email,'.$id.',id'],
            'longitude' => 'required',
            'latitude' => 'required',
        ], [
            'name.required' => translate('Name is required!'),
            'email.required' => translate('Email is required!'),
            'email.unique' => translate('Email must be unique!'),
        ]);

        $branch = $this->branch->find($id);
        $branch->name = $request->name;
        $branch->email = $request->email;
        $branch->phone = $request->phone;
        $branch->longitude = $request->longitude;
        $branch->latitude = $request->latitude;
        $branch->coverage = $request->coverage ? $request->coverage : 0;
        $branch->address = $request->address;
        $branch->image = $request->has('image') ? Helpers_update('Images/branch/', $branch->image, 'png', $request->file('image')) : $branch->image;
        if ($request['password'] != null) {
            $branch->password = bcrypt($request->password);
        }
        $branch->save();
        flash()->success(translate('Branch updated successfully!'));

        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $branch = $this->branch->where('id', $request->id)->whereNotIn('id', [1])->first();
        if ($branch){
            $branch->delete();

            try {
                $emailServices = Helpers_get_business_settings('mail_config');
                // if (isset($emailServices['status']) && $emailServices['status'] == 1) {
                //     Mail::to($branch->email)->send(new \App\Mail\Branch\BranchDelete($branch));
                // }
            } catch (\Exception $e) {
            }

            flash()->success(translate('Branch removed!'));
        }else{
            flash()->warning(translate('Access denied!'));
        }
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): RedirectResponse
    {
        $branch = $this->branch->find($request->id);
        $branch->status = $request->status;
        $branch->save();

        try {
            $emailServices = Helpers_get_business_settings('mail_config');
            // if (isset($emailServices['status']) && $emailServices['status'] == 1) {
            //     Mail::to($branch->email)->send(new \App\Mail\Branch\BranchChangeStatus($branch));
            // }
        } catch (\Exception $e) {
        }
        flash()->success(translate('Branch status updated!'));
        return back();
    }
}
