<?php

namespace App\Http\Controllers\Pms;

use App\Models\PmsModels\PaymentTerm;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentTermController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Payment Terms List';
        $status=[
            PaymentTerm::ACTIVE=>PaymentTerm::ACTIVE,
            PaymentTerm::INACTIVE=>PaymentTerm::INACTIVE
        ];

        $paymentTerms=PaymentTerm::get();

        return view('pms.backend.pages.payment-term.index',compact('title','status','paymentTerms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [

            'term' => 'required|max:200',
            'remarks' => 'nullable|max:300'

        ]);

        $input = $request->except('_token');

        try{
            PaymentTerm::create($input);
            return $this->backWithSuccess('Payment Term created successfully');
        }catch (\Exception $e){

            return $this->backWithError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PmsModels\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function show(PaymentTerm $paymentTerm)
    {
        $title='Edit Payment Term Information';
        $status=[
            PaymentTerm::ACTIVE=>PaymentTerm::ACTIVE,
            PaymentTerm::INACTIVE=>PaymentTerm::INACTIVE
        ];
        return view('pms.backend.pages.payment-term.show',compact('title','status','paymentTerm'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PmsModels\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentTerm $paymentTerm)
    {
        return response()->json($paymentTerm);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PmsModels\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PaymentTerm $paymentTerm)
    {
        $this->validate($request, [
            'term' => 'required|max:200',
            'remarks' => 'nullable|max:300'

        ]);

        $input = $request->except('password', '_token');

        try {

            $paymentTerm->update($input);

            return $this->backWithSuccess('Payment Term Data Update successfully');

        } catch (\Exception $e) {
            return $this->backWithError($e->getMessage());

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PmsModels\PaymentTerm  $paymentTerm
     * @return \Illuminate\Http\Response
     */
    public function destroy(PaymentTerm $paymentTerm)
    {

        try{
            $paymentTerm->delete();

            return $this->backWithSuccess('Payment Term Data Update successfully');
        }catch(\Exception $e){
            return $this->backWithError($e->getMessage());

        }
    }
}
