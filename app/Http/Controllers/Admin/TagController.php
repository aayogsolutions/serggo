<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};


class TagController extends Controller
{
    public function __construct(
        private Tag   $tag,
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
            $tags = $this->tag->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            })->orderBy('name');
            $queryParam = ['search' => $request['search']];
        } else {
            $tags = $this->tag->orderBy('name');
        }
        $tags = $tags->latest()->paginate(Helpers_getPagination())->appends($queryParam);
        return view('Admin.views.products.tag.index', compact('tags', 'search'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:tags',
        ], [
            'name.required' => translate('Name is required'),
            'name.unique' => translate('Name is already taken'),
        ]);
        
        if (strlen($request->name) > 255) {
            flash()->error(translate('Name is too long!'));
            return back();
        }

        $tag = $this->tag;
        $tag->name = $request->name;
        $tag->save();

        flash()->success(translate('Tag added successfully!'));
        return back();
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function edit($id): View|Factory|Application
    {
        $tag = $this->tag->find($id);
        return view('Admin.views.products.tag.edit', compact('tag'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:tags,name,' . $request->id,
        ], [
            'name.required' => translate('Name is required'),
        ]);
        
        if (strlen($request->name) > 255) {
            flash()->error(translate('Name is too long!'));
            return back();
        }

        $tag = $this->tag->find($id);
        $tag->name = $request->name;
        $tag->save();

        flash()->success(translate('Tag updated successfully!'));
        return redirect()->route('admin.product.tag.add-new');
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function delete(Request $request): RedirectResponse
    {
        $tag = $this->tag->find($request->id);
        $tag->delete();
        flash()->success(translate('Tag removed!'));
        return back();
    }
}
