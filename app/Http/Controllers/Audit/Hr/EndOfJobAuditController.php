<?php

namespace App\Http\Controllers\Audit\Hr;

use App\Http\Controllers\Controller;
use App\Models\Audit\EndOfJobAudit;
use App\Models\Audit\MaternityAudit;
use App\Repository\Hr\EmployeeRepository;
use App\Repository\Hr\FinalSettlementRepository;
use Carbon\Carbon;
use DB, Validator;
use Illuminate\Http\Request;

class EndOfJobAuditController extends Controller
{
    protected $finalSettlement;

    public function __construct(FinalSettlementRepository $finalSettlement)
    {
        $this->finalSettlement = $finalSettlement;
    }

    public function index(Request $request)
    {
        
        return view('audit.hr.end-of-job.index');
    }


    public function fetch(Request $request)
    {
        $input = $request->all();
        $employees = $this->finalSettlement->get($input);
    
        $summary = (object)[
            'service_benefits' => round($employees->sum('service_benefits')),
            'earn_leave_amount' => round($employees->sum('earn_leave_amount')),
            'total_amount' => round($employees->sum('total_amount'))
        ];
        $type = $this->finalSettlement->benefitType;

        $grouped = $employees->groupBy('benefit_on',true)->map(function($q){
            return count($q);
        });

        foreach($grouped as $k => $v){
            $summary->{strtolower($type[$k])} = $v;
        }
        
        $audited = $employees->filter(function($q){
            return $q->audit != null;
        })->count();

    

        return view('audit.hr.end-of-job.report', compact('audited','employees','summary','input'));
    }

    public function action(Request $request)
    {
        $validator= Validator::make($request->all(),[
            'id'=>'required',
            'status'=>'required'

        ]);
        if($validator->fails()){
            return 'failed';
        }

        $audit = EndOfJobAudit::firstOrNew(['hr_end_of_job_id' => $request->id]);
        $audit->hr_end_of_job_id = $request->id;
        $audit->status = $request->status;
        $audit->comment = $request->comment;
        $audit->audit_date = date('Y-m-d');
        $audit->created_by = auth()->id();
        $audit->save();

        return $audit;
    }
}
