<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MMRController extends Controller
{
    public function index()
    {
        return view('hr.setup.mmr.index');
    }
}
