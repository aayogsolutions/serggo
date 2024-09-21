<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{
    SplashBanner,
    AuthBanners,
    HomeBanner,
    HomeSliderBanner,
    Products,
    Category
};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory, View};
use Illuminate\Http\{JsonResponse, RedirectResponse};
use Illuminate\Support\Facades\File;

class BannersController extends Controller
{
    public function __construct(
        private Products $product,
        private Category $category,
        private SplashBanner $splashbanner,
        private AuthBanners $authBanners,
        private HomeBanner $homebanner,
        private HomeSliderBanner $homesilderbanner
    ) {}

    // Splash Banners

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function SplashIndex(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $banners = $this->splashbanner->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                    $q->orWhere('ui_type', 'like', "%{$value}%");
                }
            })->orderBy('id', 'desc');
            $queryParam = ['search' => $request['search']];
        } else {
            $banners = $this->splashbanner->orderBy('id', 'desc');
        }
        $banners = $banners->paginate(Helpers_getPagination())->appends($queryParam);

        return view('Admin.views.banner.splash.index', compact('banners', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function SplashStore(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'required',
            'type' => 'required',
        ], [
            'title.required' => translate('Title is required'),
            'image.required' => translate('Image is required'),
            'type.required' => translate('Type is required'),
        ]);


        $mimeType = $request->file('image')->getClientMimeType();
        $fileType = explode('/', $mimeType)[0];

        if ($fileType == 'image') {
            $file_size = getimagesize($request->file('image'));
            // Width Check              Height Check
            if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
                $banner = $this->splashbanner;
                $banner->title = $request->title;
                $banner->ui_type = $request->type;
                $banner->attechment = Helpers_upload('Images/banners/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->attechment_type = $fileType;
                $banner->save();
                flash()->success(translate('Banner added successfully!'));
                return back();
            }
            flash()->error(translate('Image size is wrong.!'));
            return back();
        } elseif ($fileType == 'video') {

            // Width Check              Height Check
            if ($request->width <= 5000 && $request->height <= 5000) {
                $banner = $this->splashbanner;
                $banner->title = $request->title;
                $banner->ui_type = $request->type;
                $banner->attechment = Helpers_upload('Images/banners/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->attechment_type = $fileType;
                $banner->save();
                flash()->success(translate('Banner added successfully!'));
                return back();
            }
            flash()->error(translate('video size is wrong.!'));
            return back();
        }
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function SplashEdit($id): View|Factory|Application
    {
        $banner = $this->splashbanner->find($id);
        return view('Admin.views.banner.splash.edit', compact('banner'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function SplashStatus(Request $request): RedirectResponse
    {
        $allbanner = $this->splashbanner->where([
            ['id','!=',$request->id],
            ['ui_type','=',$request->type]
            ])->get();

        foreach ($allbanner as $key => $value) {
            $banner = $this->splashbanner->find($value->id);
            $banner->status = 1;
            $banner->save();
        }

        $banner = $this->splashbanner->find($request->id);
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
    public function SplashUpdate(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'type' => 'required',
        ], [
            'title.required' => translate('Title is required'),
            'type.required' => translate('Type is required'),
        ]);

        if($request->has('image'))
        {
            $mimeType = $request->file('image')->getClientMimeType();
            $fileType = explode('/', $mimeType)[0];

            if ($fileType == 'image') {
                $file_size = getimagesize($request->file('image'));
                // Width Check              Height Check
                if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
                    $banner = $this->splashbanner->find($id);
                    $banner->title = $request->title;
                    $banner->ui_type = $request->type;
                    $banner->attechment = Helpers_update('Images/banners/', $banner->attechment , $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                    $banner->attechment_type = $fileType;
                    $banner->save();
                    flash()->success(translate('Banner updated successfully!'));
                    return redirect()->route('admin.banners.splash.add');
                }
                flash()->error(translate('Image size is wrong.!'));
                return back();
            } elseif ($fileType == 'video') {

                // Width Check              Height Check
                if ($request->width <= 5000 && $request->height <= 5000) {
                    $banner = $this->splashbanner->find($id);
                    $banner->title = $request->title;
                    $banner->ui_type = $request->type;
                    $banner->attechment = Helpers_update('Images/banners/', $banner->attechment , $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                    $banner->attechment_type = $fileType;
                    $banner->save();
                    flash()->success(translate('Banner updated successfully!'));
                    return redirect()->route('admin.banners.splash.add');
                }
                flash()->error(translate('video size is wrong.!'));
                return back();
            }
        }else{
            $banner = $this->splashbanner->find($id);
            $banner->title = $request->title;
            $banner->ui_type = $request->type;
            $banner->save();
            flash()->success(translate('Banner updated successfully!'));
            return redirect()->route('admin.banners.splash.add');
        }
        
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function SplashDelete(Request $request): RedirectResponse
    {
        $banner = $this->splashbanner->find($request->id);
        if (File::exists('Images/banners/'.$banner->attechment)) {
            File::delete('Images/banners/'.$banner->attechment);
        }
        $banner->delete();
        flash()->success(translate('Banner removed!'));
        return back();
    }

    // Auth Banners

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function AuthIndex(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $banners = $this->authBanners->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                    $q->orWhere('ui_type', 'like', "%{$value}%");
                    $q->orWhere('section_type', 'like', "%{$value}%");
                }
            })->orderBy('id', 'desc');
            $queryParam = ['search' => $request['search']];
        } else {
            $banners = $this->authBanners->orderBy('id', 'desc');
        }
        $banners = $banners->paginate(Helpers_getPagination())->appends($queryParam);

        return view('Admin.views.banner.auth.index', compact('banners', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function AuthStore(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'required',
            'type' => 'required',
            'screen' => 'required',
        ], [
            'title.required' => translate('Title is required'),
            'image.required' => translate('Image is required'),
            'type.required' => translate('Type is required'),
            'screen.required' => translate('Screen is required'),
        ]);


        $mimeType = $request->file('image')->getClientMimeType();
        $fileType = explode('/', $mimeType)[0];

        if ($fileType == 'image') {
            $file_size = getimagesize($request->file('image'));
            // Width Check                 Height Check
            if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
                $banner = $this->authBanners;
                $banner->title = $request->title;
                $banner->ui_type = $request->type;
                $banner->section_type = $request->screen;
                $banner->attechment = Helpers_upload('Images/banners/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->attechment_type = $fileType;
                $banner->save();
                flash()->success(translate('Banner added successfully!'));
                return back();
            }
            flash()->error(translate('Image size is wrong.!'));
            return back();
        } elseif ($fileType == 'video') {

            // Width Check                  Height Check
            if ($request->width <= 5000 && $request->height <= 5000) {
                $banner = $this->authBanners;
                $banner->title = $request->title;
                $banner->ui_type = $request->type;
                $banner->section_type = $request->screen;
                $banner->attechment = Helpers_upload('Images/banners/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->attechment_type = $fileType;
                $banner->save();
                flash()->success(translate('Banner added successfully!'));
                return back();
            }
            flash()->error(translate('video size is wrong.!'));
            return back();
        }
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function AuthEdit($id): View|Factory|Application
    {
        $banner = $this->authBanners->find($id);
        return view('Admin.views.banner.auth.edit', compact('banner'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function AuthStatus(Request $request): RedirectResponse
    {
        $allbanner = $this->authBanners->where([
            ['id','!=',$request->id],
            ['ui_type','=',$request->type],
            ['section_type','=',$request->screen]
            ])->get();

        foreach ($allbanner as $key => $value) {
            $banner = $this->authBanners->find($value->id);
            $banner->status = 1;
            $banner->save();
        }

        $banner = $this->authBanners->find($request->id);
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
    public function AuthUpdate(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'type' => 'required',
            'screen' => 'required',
        ], [
            'title.required' => translate('Title is required'),
            'type.required' => translate('UI Type is required'),
            'screen.required' => translate('Screen Type is required'),
        ]);

        if($request->has('image'))
        {
            $mimeType = $request->file('image')->getClientMimeType();
            $fileType = explode('/', $mimeType)[0];

            if ($fileType == 'image') {
                $file_size = getimagesize($request->file('image'));
                // Width Check              Height Check
                if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
                    $banner = $this->authBanners->find($id);
                    $banner->title = $request->title;
                    $banner->ui_type = $request->type;
                    $banner->section_type = $request->screen;
                    $banner->attechment = Helpers_update('Images/banners/', $banner->attechment , $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                    $banner->attechment_type = $fileType;
                    $banner->save();
                    flash()->success(translate('Banner updated successfully!'));
                    return redirect()->route('admin.banners.auth.add');
                }
                flash()->error(translate('Image size is wrong.!'));
                return back();
            } elseif ($fileType == 'video') {

                // Width Check              Height Check
                if ($request->width <= 5000 && $request->height <= 5000) {
                    $banner = $this->authBanners->find($id);
                    $banner->title = $request->title;
                    $banner->ui_type = $request->type;
                    $banner->section_type = $request->screen;
                    $banner->attechment = Helpers_update('Images/banners/', $banner->attechment , $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                    $banner->attechment_type = $fileType;
                    $banner->save();
                    flash()->success(translate('Banner updated successfully!'));
                    return redirect()->route('admin.banners.auth.add');
                }
                flash()->error(translate('video size is wrong.!'));
                return back();
            }
        }else{
            $banner = $this->authBanners->find($id);
            $banner->title = $request->title;
            $banner->ui_type = $request->type;
            $banner->section_type = $request->screen;
            $banner->save();
            flash()->success(translate('Banner updated successfully!'));
            return redirect()->route('admin.banners.auth.add');
        }
        
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function AuthDelete(Request $request): RedirectResponse
    {
        $banner = $this->authBanners->find($request->id);
        if (File::exists('Images/banners/'.$banner->attechment)) {
            File::delete('Images/banners/'.$banner->attechment);
        }
        $banner->delete();
        flash()->success(translate('Banner removed!'));
        return back();
    }

    // Home Banners

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function HomeIndex(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $banners = $this->homebanner->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                    $q->orWhere('ui_type', 'like', "%{$value}%");
                }
            })->orderBy('id', 'desc');
            $queryParam = ['search' => $request['search']];
        } else {
            $banners = $this->homebanner->orderBy('id', 'desc');
        }
        $banners = $banners->paginate(Helpers_getPagination())->appends($queryParam);

        return view('Admin.views.banner.home.index', compact('banners', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function HomeStore(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'required',
            'type' => 'required',
            'background_color' => 'required',
            'font_color' => 'required',
        ], [
            'title.required' => translate('Title is required'),
            'image.required' => translate('Image is required'),
            'type.required' => translate('Type is required'),
            'background_color.required' => translate('background_color is required'),
            'font_color.required' => translate('font_color is required'),
        ]);


        $mimeType = $request->file('image')->getClientMimeType();
        $fileType = explode('/', $mimeType)[0];

        if ($fileType == 'image') {
            $file_size = getimagesize($request->file('image'));
            // Width Check                 Height Check
            if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
                $banner = $this->homebanner;
                $banner->title = $request->title;
                $banner->ui_type = $request->type;
                $banner->attechment = Helpers_upload('Images/banners/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->attechment_type = $fileType;
                $banner->background_color = $request->background_color;
                $banner->font_color = $request->font_color;
                $banner->save();
                flash()->success(translate('Banner added successfully!'));
                return back();
            }
            flash()->error(translate('Image size is wrong.!'));
            return back();
        } elseif ($fileType == 'video') {

            // Width Check                  Height Check
            if ($request->width <= 5000 && $request->height <= 5000) {
                $banner = $this->homebanner;
                $banner->title = $request->title;
                $banner->ui_type = $request->type;
                $banner->attechment = Helpers_upload('Images/banners/', $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                $banner->attechment_type = $fileType;
                $banner->background_color = $request->background_color;
                $banner->font_color = $request->font_color;
                $banner->save();
                flash()->success(translate('Banner added successfully!'));
                return back();
            }
            flash()->error(translate('video size is wrong.!'));
            return back();
        }
    }

    /**
     * @param $id
     * @return Factory|View|Application
     */
    public function HomeEdit($id): View|Factory|Application
    {
        $banner = $this->homebanner->find($id);
        return view('Admin.views.banner.home.edit', compact('banner'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function HomeStatus(Request $request): RedirectResponse
    {
        $allbanner = $this->homebanner->where([
            ['id','!=',$request->id],
            ['ui_type','=',$request->type],
        ])->get();

        foreach ($allbanner as $key => $value) {
            $banner = $this->homebanner->find($value->id);
            $banner->status = 1;
            $banner->save();
        }

        $banner = $this->homebanner->find($request->id);
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
    public function HomeUpdate(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'type' => 'required',
            'background_color' => 'required',
            'font_color' => 'required',
        ], [
            'title.required' => translate('Title is required'),
            'type.required' => translate('UI Type is required'),
            'background_color.required' => translate('background_color is required'),
            'font_color.required' => translate('font_color is required'),
        ]);

        if($request->has('image'))
        {
            $mimeType = $request->file('image')->getClientMimeType();
            $fileType = explode('/', $mimeType)[0];

            if ($fileType == 'image') {
                $file_size = getimagesize($request->file('image'));
                // Width Check              Height Check
                if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
                    $banner = $this->homebanner->find($id);
                    $banner->title = $request->title;
                    $banner->ui_type = $request->type;
                    $banner->attechment = Helpers_update('Images/banners/', $banner->attechment , $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                    $banner->attechment_type = $fileType;
                    $banner->background_color = $request->background_color;
                    $banner->font_color = $request->font_color;
                    $banner->save();
                    flash()->success(translate('Banner updated successfully!'));
                    return redirect()->route('admin.banners.home.add');
                }
                flash()->error(translate('Image size is wrong.!'));
                return back();
            } elseif ($fileType == 'video') {

                // Width Check              Height Check
                if ($request->width <= 5000 && $request->height <= 5000) {
                    $banner = $this->homebanner->find($id);
                    $banner->title = $request->title;
                    $banner->ui_type = $request->type;
                    $banner->attechment = Helpers_update('Images/banners/', $banner->attechment , $request->file('image')->getClientOriginalExtension(), $request->file('image'));
                    $banner->attechment_type = $fileType;
                    $banner->background_color = $request->background_color;
                    $banner->font_color = $request->font_color;
                    $banner->save();
                    flash()->success(translate('Banner updated successfully!'));
                    return redirect()->route('admin.banners.home.add');
                }
                flash()->error(translate('video size is wrong.!'));
                return back();
            }
        }else{
            $banner = $this->homebanner->find($id);
            $banner->title = $request->title;
            $banner->ui_type = $request->type;
            $banner->background_color = $request->background_color;
            $banner->font_color = $request->font_color;
            $banner->save();
            flash()->success(translate('Banner updated successfully!'));
            return redirect()->route('admin.banners.home.add');
        }
        
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function HomeDelete(Request $request): RedirectResponse
    {
        $banner = $this->homebanner->find($request->id);
        if (File::exists('Images/banners/'.$banner->attechment)) {
            File::delete('Images/banners/'.$banner->attechment);
        }
        $banner->delete();
        flash()->success(translate('Banner removed!'));
        return back();
    }

    // Home Slider Banners

    /**
     * @param Request $request
     * @return Factory|View|Application
     */
    function HomeSliderIndex(Request $request): View|Factory|Application
    {
        $queryParam = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $banners = $this->homesilderbanner->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('title', 'like', "%{$value}%");
                    $q->orWhere('ui_type', 'like', "%{$value}%");
                }
            })->orderBy('id', 'desc');
            $queryParam = ['search' => $request['search']];
        } else {
            $banners = $this->homesilderbanner->orderBy('id', 'desc');
        }
        $banners = $banners->paginate(Helpers_getPagination())->appends($queryParam);

        $products = $this->product->orderBy('name')->get();
        $categories = $this->category->where(['parent_id'=>0])->orderBy('name')->get();

        return view('Admin.views.banner.homeslider.index', compact('banners', 'products', 'categories', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function HomeSliderStore(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'image' => 'required|image',
            'type' => 'required',
            'item_type' => 'required',
        ], [
            'title.required' => translate('Title is required'),
            'image.required' => translate('Image is required'),
            'type.required' => translate('Type is required'),
            'item_type.required' => translate('Item Type is required'),
        ]);
        
        $file_size = getimagesize($request->file('image'));
        // Width Check                 Height Check
        if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
            $banner = $this->homesilderbanner;
            $banner->title = $request->title;
            $banner->ui_type = $request->type;
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
    public function HomeSliderEdit($id): View|Factory|Application
    {
        $banner = $this->homesilderbanner->find($id);

        $products = $this->product->orderBy('name')->get();
        $categories = $this->category->where(['parent_id'=>0])->orderBy('name')->get();

        return view('Admin.views.banner.homeslider.edit', compact('banner','categories','products'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function HomeSliderStatus(Request $request): RedirectResponse
    {
        $allbanner = $this->homesilderbanner->where([
            ['id','!=',$request->id],
            ['ui_type','=',$request->type],
        ])->get();

        foreach ($allbanner as $key => $value) {
            $banner = $this->homesilderbanner->find($value->id);
            $banner->status = 1;
            $banner->save();
        }

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
    public function HomeSliderUpdate(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'title' => 'required|max:255',
            'type' => 'required',
        ], [
            'title.required' => translate('Title is required'),
            'type.required' => translate('UI Type is required'),
        ]);

        if($request->has('image'))
        {
            $file_size = getimagesize($request->file('image'));
            // Width Check              Height Check
            if ($file_size[0] <= 5000 && $file_size[1] <= 5000) {
                $banner = $this->homesilderbanner->find($id);
                $banner->title = $request->title;
                $banner->ui_type = $request->type;
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
                flash()->success(translate('Banner updated successfully!'));
                return redirect()->route('admin.banners.homeslider.add');
            }
            flash()->error(translate('Image size is wrong.!'));
            return back();
        }else{
            $banner = $this->homesilderbanner->find($id);
            $banner->title = $request->title;
            $banner->ui_type = $request->type;
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
            $banner->save();
            flash()->success(translate('Banner updated successfully!'));
            return redirect()->route('admin.banners.homeslider.add');
        }
        
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function HomeSliderDelete(Request $request): RedirectResponse
    {
        $banner = $this->homesilderbanner->find($request->id);
        if (File::exists('Images/banners/'.$banner->attechment)) {
            File::delete('Images/banners/'.$banner->attechment);
        }
        $banner->delete();
        flash()->success(translate('Banner removed!'));
        return back();
    }
}
