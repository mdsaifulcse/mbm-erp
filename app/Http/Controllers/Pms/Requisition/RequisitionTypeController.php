<?php

namespace App\Http\Controllers\Pms\Requisition;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\RequisitionType;
use Illuminate\Http\Request;

class RequisitionTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        try {
            $title = 'Requisition Type';
            $types = RequisitionType::all();
            return view('pms.backend.pages.requisitionsType.index', compact('title', 'types'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            $data = [
                'status' => 'success',
                'info' => RequisitionType::all()
            ];
            return response()->json($data);
        }catch (\Throwable $th){
            $data = [
                'status' => null,
                'info' => $th->getMessage()
            ];
            return response()->json($data);
        }
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
            'name' => ['required']
        ]);
        $inputs = $request->all();
        unset($inputs['_token']);
        unset($inputs['_method']);
        try {
            RequisitionType::create($inputs);
            $data = [
                'status' => 'success',
                'info' => RequisitionType::all()
            ];
            return response()->json($data);
        }catch (\Throwable $th){

            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(RequisitionType $type)
    {
        try {
            $type->src = url('pms/requisition/type',$type->id);
            $type->req_type = 'PUT';
            $data = [
                'status' => 'success',
                'info' => RequisitionType::find($type->id)
            ];
            return response()->json($data);
        }catch (\Throwable $th){
            $data = [
                'status' => null,
                'info' => $th->getMessage()
            ];
            return response()->json($data);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RequisitionType $type)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255']
        ]);
        $inputs = $request->all();
        unset($inputs['_token']);
        unset($inputs['_method']);
        try {
            $type->update($inputs);


            $type->update($request->all());
            $data = [
                'status' => 'success',
                'info' => $type
            ];
            return response()->json($data);
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(RequisitionType $type)
    {
        try {
            $type->delete();
        }catch (\Throwable $th){
            return response()->json($th->getMessage());
        }
    }
}
