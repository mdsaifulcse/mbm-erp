<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\Requisition;
use App\Models\PmsModels\RequisitionDelivery;
use Illuminate\Http\Request;
use Auth,DB;

class RequisitionDeliveryController extends Controller
{
    // delivery-list
    public function requisitionDeliveryList($requisitionId=null)
    {
        try {
            $title='Requisition Delivery List';
            $requisitionDeliveries=RequisitionDelivery::with('relDeliveryBy','relDeliveryItems','relRequisition','relRequisition.relUsersList','relRequisition.requisitionItems')
                ->orderBy('id','DESC');

            if (!is_null($requisitionId)){
                $requisitionDeliveries=$requisitionDeliveries->where(['requisition_id'=>$requisitionId]);
            }

            $requisitionDeliveries=$requisitionDeliveries->paginate(30);

            return view('pms.backend.pages.requisition-delivery.delivery-list', compact('title','requisitionDeliveries'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    public function requisitionDeliveryDetail($requisitionDeliveryId)
    {
        try {
            $title='Requisition Delivery Details';
            $requisitionDelivery=RequisitionDelivery::with('relDeliveryItems','relRequisition','relRequisition.relUsersList')
                ->findOrFail($requisitionDeliveryId);

            return view('pms.backend.pages.requisition-delivery.delivery-detail', compact('title','requisitionDelivery'));

        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
