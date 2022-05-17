<?php
namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\InventoryModels\InventoryLogs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB,Validator, Str;

class InventoryLogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $title = 'Inventory Logs';

        $logs=InventoryLogs::where('status','active')->paginate(50);

        return view('pms.backend.pages.inventory.inventory-logs.index',compact('title','logs'));
         
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InventoryLogs  $inventoryLogs
     * @return \Illuminate\Http\Response
     */
    public function show(InventoryLogs $inventoryLogs)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InventoryLogs  $inventoryLogs
     * @return \Illuminate\Http\Response
     */
    public function edit(InventoryLogs $inventoryLogs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InventoryLogs  $inventoryLogs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventoryLogs $inventoryLogs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InventoryLogs  $inventoryLogs
     * @return \Illuminate\Http\Response
     */
    public function destroy(InventoryLogs $inventoryLogs)
    {
        //
    }
}
