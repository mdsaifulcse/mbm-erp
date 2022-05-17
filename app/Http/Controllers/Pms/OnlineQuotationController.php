<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\PmsModels\Product;
use App\Models\PmsModels\Suppliers;
use App\Models\PmsModels\Rfp\RequestProposal;
use App\Models\PmsModels\Rfp\RequestProposalDetails;
use App\Models\PmsModels\Rfp\RequestProposalDefineSupplier;
use App\Models\PmsModels\Quotations;
use App\Models\PmsModels\QuotationsItems;
use App,DB,Auth;

class OnlineQuotationController extends Controller
{
    public function showOnlineQuotationForm($proposalId,$supplierId){


        $proposalId=decrypt($proposalId);
        $supplierId=decrypt($supplierId);

        try {
            $supplier=Suppliers::findOrFail($supplierId);
            $title = 'Dear '.$supplier->name.' Please Submit Your Quotation Here';
            $maxId=Quotations::max('id')+1;
            $refNo=Quotations::REFNO+$maxId;
            $refNo='RN-'.$refNo;

            $modal=Quotations::where([
                'supplier_id'=>$supplierId,
                'request_proposal_id'=>$proposalId,
                'type'=>'online'
            ])->first();

            if(count((array)$modal)>0){
                return 'Already Your Quotation Submit ';
            }

            $requestProposal=RequestProposal::where('type','online')->with('requestProposalDetails','requestProposalDetails.product','createdBy')->findOrFail($proposalId);


            $submitQuotation=Quotations::where(['request_proposal_id'=>$proposalId, 'supplier_id'=>$supplierId,'type'=>'online'])->get();

            return view('pms.backend.pages.quotation.online-quotation-form', compact('title','supplier','requestProposal','refNo','submitQuotation'));

        }catch (\Throwable $th){

            return$th->getMessage();
        }
    }


    public function store(Requests\Pms\QuotationRequest $request){

        $type=$request->type;
        $modal=Quotations::where([
            'supplier_id'=>$request->supplier_id,
            'request_proposal_id'=>$request->request_proposal_id,
            'type'=>$type
        ])->first();

        if(count((array)$modal)>0){
            return 'Already Your quotation Submit ';
        }

        DB::beginTransaction();
        try {

            $quotationFilePath='';
            if ($request->hasFile('quotation_file'))
            {
                $quotationFilePath=$this->fileUpload($request->file('quotation_file'),'quotation/pdf-file');
            }

            $quotation=Quotations::create([
                'supplier_id'=>$request->supplier_id,
                'request_proposal_id'=>$request->request_proposal_id,
                'reference_no'=>$request->reference_no,
                'quotation_date'=>date('Y-m-d',strtotime($request->quotation_date)),
                'total_price'=>$request->sum_of_subtoal,
                'discount'=>$request->discount==null?0:$request->discount,
                'vat'=>$request->vat==null?0:$request->vat,
                'gross_price'=>$request->gross_price,
                'status'=>'active',
                'type'=>$type,
                'created_by'=>$request->supplier_id,
                'quotation_file'=>$quotationFilePath
            ]);

            foreach ($request->product_id as $i=>$product_id){
                $quotationItemsInput[]=[
                    'quotation_id'=>$quotation->id,
                    'product_id'=>$product_id,
                    'unit_price'=>$request->unit_price[$product_id],
                    'qty'=>$request->qty[$product_id],
                    'sub_total_price'=>$request->sub_total_price[$product_id],
                    'discount'=>0,
                    'vat'=>0,
                    'total_price'=>$request->sub_total_price[$product_id],
                    'created_at'=>date('Y-m-d h:i'),
                ];
            }

            QuotationsItems::insert($quotationItemsInput);

            DB::commit();

            return 'Your Quotation Successfully Submit';
        }
        catch (Throwable $th){
            DB::rollback();
            return $th->getMessage();
        }
    }

}
