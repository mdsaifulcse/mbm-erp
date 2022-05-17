<?php

namespace App\Http\Controllers\Pms\Purchase;

use App\Http\Controllers\Controller;
use App\Mail\Pms\PurchaseOrderMail;
use App\Models\PmsModels\Purchase\PurchaseOrder;
use App\Models\PmsModels\Purchase\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use DB;

class PurchaseController extends Controller
{

    public function orderIndex()
    {
        try {
            $title = 'Purchase Order';
            $purchaseOrderList= PurchaseOrder::paginate(100);

            //dd($purchaseOrderList);
            return view('pms.backend.pages.purchase.order-list', compact('title', 'purchaseOrderList'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function sendMailToSupplier(PurchaseOrder $order)
    {   
        DB::beginTransaction();
        try {

            $order->is_send = 'yes';
            $order->save();

            //Mail::to($order->relQuotation->relSuppliers->email)->send(new PurchaseOrderMail($order));
            //Order Tracking System
            \App\Models\PmsModels\RequestProposalTracking::StoreRequestProposalTracking($order->relQuotation->request_proposal_id,'PO-Submit');

            DB::commit();

            return $this->backWithSuccess('Successfully PO send to supplier!!');

        }catch (\Throwable $th){
            DB::rollback();
            return $this->backWithError($th->getMessage());
        }
    }

    public function show($id)
    {
        $response=[];

        try{
             $modal = PurchaseOrder::with('relQuotation','relQuotation.relSuppliers')->findOrFail($id);
             if ($modal) {
                $body = \Illuminate\Support\Facades\View::make('pms.backend.pages.purchase.show',
                        ['purchaseOrder'=> $modal]);
                    $contents = $body->render();

                    $response['result'] = 'success';
                    $response['body'] = $contents;
                    $response['message'] = 'Successfully Generated PO';
            }else{
               $response['result'] = 'error';
               $response['message'] = 'Purchase Order not found!!';
           }

        }catch(\Throwable $th){
            $response['result'] = 'error';
            $response['message'] = $th->getMessage();
        }

        return $response;
    }
}
