<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\Notifications;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;

class NotificationController extends Controller
{
    public function __construct(
        private Notifications $notification
    ){}

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function index(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $notifications = $this->notification->where(function ($q) use ($key) {
                        foreach ($key as $value) {
                            $q->orWhere('title', 'like', "%{$value}%")
                            ->orWhere('description', 'like', "%{$value}%");
                        }
            });
            $queryParam = ['search' => $request['search']];
        }else{
           $notifications = $this->notification;
        }
        $notifications = $notifications->latest()->paginate(Helpers_getPagination())->appends($queryParam);
        return view('Admin.views.notification.index', compact('notifications','search'));
    }


    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
        ], [
            'title.required' => 'title is required!',
        ]);

        if ($request->has('image')) {
            $imageName = Helpers_upload('Images/notification/', 'png', $request->file('image'));
        } else {
            $imageName = null;
        }

        $notification = $this->notification;
        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $imageName;
        $notification->status = 1;
        $notification->save();
        
        // try {
        //     $notification->type = 'general';
        //     Helpers_send_push_notif_to_topic($notification);
        // } catch (\Exception $e) {
        //     flash()->warning(translate('Push notification failed!'));
        // }

        flash()->success(translate('Notification sent successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function edit($id): View|Factory|Application
    {
        $notification = $this->notification->find($id);
        return view('Admin.views.notification.edit', compact('notification'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'description' => 'required|max:255',
        ], [
            'title.required' => 'title is required!',
        ]);

        $notification = $this->notification->find($id);

        if ($request->has('image')) {
            $imageName = Helpers_update('Images/notification/', $notification->image, 'png', $request->file('image'));
        } else {
            $imageName = $notification['image'];
        }

        $notification->title = $request->title;
        $notification->description = $request->description;
        $notification->image = $imageName;
        $notification->save();
        flash()->success(translate('Notification updated successfully!'));
        return redirect()->route('admin.notification.add-new');

    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request): \Illuminate\Http\RedirectResponse
    {
        $notification = $this->notification->find($request->id);
        $notification->status = $request->status;
        $notification->save();
        flash()->success(translate('Notification status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): \Illuminate\Http\RedirectResponse
    {
        $notification = $this->notification->find($request->id);
        if (File::exists($notification['image'])) {
            File::delete($notification['image']);
        }
        $notification->delete();
        flash()->success(translate('Notification removed!'));
        return back();
    }
}
