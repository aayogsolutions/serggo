<?php

namespace App\Http\Controllers\Admin\vendor;

use App\Http\Controllers\Controller;
use App\Models\HomeSliderBanner;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\File;

class BannerController extends Controller
{
    public function __construct(
        private HomeSliderBanner $homesilderbanner,
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
            $banners = $this->homesilderbanner->where('ui_type' , 'vender_service')->orwhere(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                    $q->orWhere('ui_type', 'like', "%{$value}%");
                }
            })->orderBy('priority', 'asc');
            $queryParam = ['search' => $request['search']];
        } else {
            $banners = $this->homesilderbanner->where('ui_type' , 'vender_service')->orderBy('priority', 'asc');
        }
        $banners = $banners->paginate(Helpers_getPagination())->appends($queryParam);

        return view('Admin.views.vendor.banner.index', compact('banners','search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function Store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'required|image',
        ], [
            'title.required' => translate('Title is required'),
            'image.required' => translate('Image is required'),
        ]);
        
        $file_size = getimagesize($request->file('image'));
        // Width Check                 Height Check
        if ($file_size[0] <= 5000 && $file_size[1] <= 5000) 
        {
            $banner = $this->homesilderbanner;
            $banner->title = $request->title;
            $banner->ui_type = $request->type;
            $banner->item_type = 'product';
            $banner->item_id = 0;
            $banner->item_detail = "none";
            $banner->attechment = Helpers_upload('Images/banners/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
            $banner->save();

            flash()->success(translate('Banner added successfully!'));
            return back();
        }
        flash()->error(translate('Image size is wrong.!'));
        return back();
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function Edit($id): View|Factory|Application
    {
        $banner = $this->homesilderbanner->find($id);

        return view('Admin.views.vendor.banner.edit', compact('banner'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function Status(Request $request): RedirectResponse
    {
        $banner = $this->homesilderbanner->find($request->id);
        $banner->status = $request->status;
        $banner->save();

        flash()->success(translate('Banner status updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function Update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
        ], [
            'title.required' => translate('Title is required'),
        ]);

        if($request->has('image'))
        {
            $file_size = getimagesize($request->file('image'));
            // Width Check              Height Check
            if ($file_size[0] <= 5000 && $file_size[1] <= 5000) 
            {
                $banner = $this->homesilderbanner->find($id);
                $banner->title = $request->title;
                $banner->attechment = Helpers_update('Images/banners/', $banner->attechment , $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->save();

                flash()->success(translate('Banner updated successfully!'));
                return redirect()->route('admin.vendor.banner.add');
            }
            flash()->error(translate('Image size is wrong.!'));
            return back();
        }
        else
        {
            $banner = $this->homesilderbanner->find($id);
            $banner->title = $request->title;
            $banner->save();

            flash()->success(translate('Banner updated successfully!'));
            return redirect()->route('admin.vendor.banner.add');
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function Priority(Request $request): RedirectResponse
    {
        $banner = $this->homesilderbanner->find($request->id);
        $banner->priority = $request->priority;
        $banner->save();

        flash()->success(translate('priority updated!'));
        return back();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function Delete(Request $request): RedirectResponse
    {
        $banner = $this->homesilderbanner->find($request->id);
        if (File::exists($banner->attechment)) {
            File::delete($banner->attechment);
        }
        $banner->delete();
        flash()->success(translate('Banner removed!'));
        return back();
    }
}
