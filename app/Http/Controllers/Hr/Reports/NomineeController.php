<?php

namespace App\Http\Controllers\Hr\Reports;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB, PDF;

class NomineeController extends Controller
{    
	public function showForm(Request $request)
    {
        
        $info = array();
        if($request->has('associate')){
    	   $info = DB::table('hr_as_basic_info AS b')
            ->where("b.associate_id", $request->associate)
            ->select(
                'u.hr_unit_name_bn',
                'u.hr_unit_address_bn',
                'dp.hr_department_name_bn',
                'dg.hr_designation_name_bn',
                'bn.*',  
                'b.*',
                "per_dist.dis_name AS permanent_district",
                "per_dist.dis_name_bn AS permanent_district_bn",
                "per_upz.upa_name AS permanent_upazilla",
                "per_upz.upa_name_bn AS permanent_upazilla_bn", 
                # present district & upazilla
                "pres_dist.dis_name AS present_district",
                "pres_dist.dis_name_bn AS present_district_bn",
                "pres_upz.upa_name AS present_upazilla",
                "pres_upz.upa_name_bn AS present_upazilla_bn" 
            )
            ->leftJoin('hr_unit AS u', 'u.hr_unit_id', '=', 'b.as_unit_id')
            ->leftJoin('hr_department AS dp', 'dp.hr_department_id', '=', 'b.as_department_id')
            ->leftJoin('hr_designation AS dg', 'dg.hr_designation_id', '=', 'b.as_designation_id')
            ->leftJoin("hr_as_adv_info AS a", "a.emp_adv_info_as_id", "=", "b.associate_id") 
            #permanent district & upazilla
            ->leftJoin('hr_dist AS per_dist', 'per_dist.dis_id', '=', 'a.emp_adv_info_per_dist')
            ->leftJoin('hr_upazilla AS per_upz', 'per_upz.upa_id', '=', 'a.emp_adv_info_per_upz')
            #present district & upazilla
            ->leftJoin('hr_dist AS pres_dist', 'pres_dist.dis_id', '=', 'a.emp_adv_info_pres_dist')
            ->leftJoin('hr_upazilla AS pres_upz', 'pres_upz.upa_id', '=', 'a.emp_adv_info_pres_upz') 
            ->leftJoin('hr_employee_bengali AS bn', 'bn.hr_bn_associate_id', '=', 'b.associate_id')  
            ->first(); 


        }
        if ($request->get('pdf') == true) { 
            $pdf = PDF::loadView('hr/reports/nominee_pdf', []);
            return $pdf->download('Nominee_Report_'.date('d_F_Y').'.pdf');
        }

    	return view('hr/reports/nominee', compact('info'));
    }
}
