<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use App\Models\Admin;

class SystemController extends Controller
{
    public function __construct(
        private Admin $admin
    ){}


    /**
     * @return Application|Factory|View
     */
    public function settings(): View|Factory|Application
    {
        return view('Admin.views.settings');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function settingsUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'number' => 'required',
        ], [
            'name.required' => 'Name is required!',
        ]);

        $admin = $this->admin->find(auth('admins')->id());
        $newimage = $request->file('image');
        if ($request->has('image')) {
            $imageName =Helpers_update('Images/Admin', $admin->image, $newimage->getClientOriginalExtension(), $newimage);
        } else {
            $imageName = $admin['image'];
        }

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->number = $request->number;
        $admin->image = $imageName;
        $admin->save();
        flash()->success(translate('Admin updated successfully!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function settingsPasswordUpdate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'password' => 'required|same:confirm_password|min:8',
            'confirm_password' => 'required',
        ]);

        $admin = $this->admin->find(auth('admins')->id());
        $admin->password = $request['password'];
        $admin->save();
        flash()->success(translate('Admin password updated successfully!'));
        return back();
    }
}
