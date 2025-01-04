<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\{Factory,View};
use Illuminate\Http\{JsonResponse,RedirectResponse};

class AmcController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @return View|Factory|Application
     */
    public function List(): View|Factory|Application
    {
        return view('Admin.views.amc.plan.list');
    }
}
