<?php

namespace App\Http\Controllers\Audit\Hr;

use App\Http\Controllers\Controller;
use App\Models\Audit\MaternityAudit;
use App\Repository\Hr\EmployeeRepository;
use App\Repository\Hr\MaternityRepository;
use Carbon\Carbon;
use DB, Validator;
use Illuminate\Http\Request;

class MaternityAuditController extends Controller
{
    protected $maternityRepository;

    public function __construct(MaternityRepository $maternityRepository)
    {
        $this->maternityRepository = $maternityRepository;
    }

    public function index(Request $request)
    {
        
        return view('audit.hr.maternity.maternity');
    }


    public function fetch(Request $request)
    {
        $input = $request->all();
        

        $employees = $this->maternityRepository->get($input);

        $summary = (object)[
            'first_payment' => 0,
            'second_payment' => 0,
            'total_payment' => 0,
            'ot' => 0,
            'non_ot' => 0,
            'third_child_payment' => 0,
            'third_child' => 0
        ];

        collect($employees)->each(function($p, $k) use (&$summary) {

            $summary->first_payment += $p->payment->first_payment;
            $summary->second_payment += $p->payment->second_payment;
            $summary->total_payment += $p->payment->total_payment;
            if($p->employee->as_ot == 1){
                $summary->ot += 1;
            }else{
                $summary->non_ot += 1;
            }
            if($p->payment->maternity_for_3 == 1){
                $summary->third_child += 1;
                $summary->third_child_payment += $p->payment->total_payment;
            }
        });
        

        $audited = $employees->filter(function($q){
            return $q->audit != null;
        })->count();

    

        return view('audit.hr.maternity.maternity-report', compact('audited','employees','summary','input'));
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

        $audit = MaternityAudit::firstOrNew(['hr_maternity_leave_id' => $request->id]);
        $audit->status = $request->status;
        $audit->comment = $request->comment;
        $audit->audit_date = date('Y-m-d');
        $audit->created_by = auth()->id();
        $audit->save();

        return $audit;
    }
}
