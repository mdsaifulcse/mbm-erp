<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use Cache, DB;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {    $title='PMS Dashboard';
         return view('pms.backend.pages.dashboard',compact('title'));
    }
}
