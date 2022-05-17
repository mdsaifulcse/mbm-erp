<?php

namespace App\Http\Controllers\Hr\Payroll;

use App\Http\Controllers\Controller;
use App\Models\Hr\Benefits;
use App\Models\Hr\Location;
use App\Models\Hr\Unit;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class EarnleavePaymentController extends Controller
{
	public function index()
	{
		return view('hr.payroll.earn-leave.index');
	}
}