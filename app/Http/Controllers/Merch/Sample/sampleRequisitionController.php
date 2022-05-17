<?php

namespace App\Http\Controllers\Merch\Sample;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Merch\Buyer;
use App\Models\Merch\Brand;
use App\Models\Merch\Country;
use App\Models\Merch\ProductType;
use App\Models\Merch\ProductSize;
use App\Models\Merch\ProductSizeGroup;
use App\Models\Merch\Operation;
use App\Models\Merch\Spmachine;
use App\Models\Merch\GarmentsType;
use App\Models\Merch\Season;
use App\Models\Merch\SampleType;
use App\Models\Merch\Style;
use App\Models\Merch\StyleOperation;
use App\Models\Merch\StyleImage;
use App\Models\Merch\SampleRequisition;
use App\Models\Merch\WashType;
use App\Models\Merch\WashCategory;
use App\Models\Merch\StlWashType;
use App\Exports\Merch\SampleExport;
use Maatwebsite\Excel\Facades\Excel;
use DataTables;
use BD,auth;


use DB;

class sampleRequisitionController extends Controller
{
   // all list of value
    public function insertForm(){

        $data['unit']     = collect(unit_by_id())->pluck('hr_unit_name', 'hr_unit_id')->toArray();
        $data['buyer']     = collect(buyer_by_id())->pluck('b_name', 'b_id')->toArray();
        // dd($data['buyer']);
        $data['productType']  = collect(product_type_by_id())->pluck('prd_type_name', 'prd_type_id');
        $data['machine']      = collect(special_machine_by_id())->pluck('spmachine_name', 'spmachine_id');
        $data['garmentsType'] = collect(garment_type_by_id())->pluck('gmt_name','gmt_id');
        $data['country']      = collect(country_by_id())->pluck('cnt_name','cnt_name');
        $data['brand']        = collect(brand_by_id())->pluck('br_name', 'br_id');
        $data['sampleType']   = collect(sample_type_by_id())->pluck('sample_name','sample_id');

        $data['style']=collect(DB::Select('call mrf_style("'.'41'.'")'))->pluck('stl_no','stl_id')->toArray();
        $data['season']=collect(DB::Select('call mrf_season("'.'41'.'")'))->pluck('se_name','se_id')->toArray();
        $data['supplier']=collect(DB::Select('call mrf_supplier'))->pluck('sup_name','sup_id')->toArray();
        $data['artical']=collect(DB::Select('call mrf_artical("'.'14'.'")'))->pluck('art_name','id')->toArray();
        $data['sample_man']=DB::table('hr_as_basic_info as spl')
                                      ->select('spl.as_name' ,'spl.associate_id')
                                      ->whereIn('spl.as_designation_id', array('278',
                                                                                        '346',
                                                                                        '347',
                                                                                        '348',
                                                                                        '349',
                                                                                        '359',
                                                                                        '443',
                                                                                        '465'))
                                      ->pluck('as_name','associate_id');

        $data['artical']=collect(DB::Select('call mrf_artical("'.'14'.'")'))->pluck('art_name','id')->toArray();

        return view('merch/sample/sample_requisition',$data);

    }
 
   // Sample Rfp save

    public function savetData(Request $request){
         
           // dd($request->all());
        try {
            $sampleStyle = '';
            $splWash = '';
            if(isset($request->mr_sample_style)){
                $splType = implode(',', $request->mr_sample_style);
            }

            if(isset($request->wash)){
                $splWash = implode(',',$request->wash);
            }
            // $insert = DB::table('mr_sample_requisition')->insertGetId([
            $insert = DB::table('mr_sample_requisition')->insert([
            'buyer'=>$request->buyer,
            'style'=>$request->mr_style,
            'style_code'=>$request->stl_code,
            'brand'=>$request->mr_brand_br_id,
            'description'=>$request->stl_des,
            'sample_type'=> $splType,
            'wash'=>$splWash,
            'product_type'=>$request->prd_type_id,
            'garment_type'=>$request->gmt_id,
            'season'=>$request->se_id,
            'product_category'=>$request->product_category,
            'color'=>$request->color,
            'size'=>$request->size,
            'supplier'=>$request->Supplier_id,
            'artical'=>$request->artical_id,
            'quantity'=>$request->qty_id,
            'send_date'=>$request->send_date,
            'sample_man_name'=>$request->sample_man,
            'stl_smv'=>$request->smv,
            'hr_unit_id'=>$request->unit,
            'entry_user'=>auth()->user()->associate_id
             ]);

            $lastid = DB::getPdo()->lastInsertId();

            // dd($lastid);

            if(isset($request->size_val)) {
            foreach($request->size_val as $key => $value){
            if(!empty($value[0])){
            DB::table('sample_req_size')->insert([
            'size_qty' => implode('',$value),
            'size_id' => $key,
            'sql_req_id' => $lastid
            ]);
            }
            }
            }

            toastr()->success('Successfully Saved');
            // return back()->with('success', 'Successfully added');
            // return back(); 
           return redirect('merch/sample/sample_requisition_list');
        } catch (\Exception $e) {
            toastr()->error($e->getMessage());
            return back();   
        }

    }


 // Modal show wash type
    public function washGroup(Request $request)
    {
        $washCategoryList = WashCategory::get();
        $data = '<div class="col-sm-12 mb-3"><div class="checkbox"><div class="row">';
        if($washCategoryList) {
        if(count($washCategoryList) > 0) {
        foreach ($washCategoryList as $key => $value) {
        $data.= "<label class='col-sm-3' style='padding:0px;'>
        <span class='lbl'> ".$value->category_name."</span>";
        if(count($value->mr_wash_type) > 0) {
        $data .= '<ul class="pl-2">';
        foreach($value->mr_wash_type as $k=>$wash) {
        $checked = '';
        if(!empty($request->checkedWash)) {
        $checked = in_array($wash->id, $request->checkedWash)!==FALSE?'checked="checked"':'';
        }
        $washName = $wash->wash_name;
        $data .= "<li style='list-style-type: none;'>";
        $data .= "<label style='padding:0px;'>";
        $data .= "<input name='washType[]' type='checkbox' class='ace' value='".$wash->id."' ".$checked.">";
        $data .= "<span class='lbl'> ".$washName."</span>";
        $data .= "</label>";
        $data .= "</li>";
        }
        $data .= '</ul>';
        }
        $data .= "</label>";
        }
        } else {
        $data .= '<div class="row"><h4 class="center" style="padding: 15px;">No Wash Group Found</h4></div>';
        }
        $data.="</div></div></div>";
        } else {
        $data .= '<div class="row"><h4 class="center" style="padding: 15px;">No Wash Group Found</h4></div>';
        }
        return json_encode($data);
    }
    
    // sample requisition list

    public function list(){

        // $splreqlist=SampleRequisition::all();
        // return ($splreqlist);

        // $reqlist=DB::select('select buyer,style ,prd_type_name Product,gmt_name Garment_description ,color,sample_name,quantity,requisition_date,send_date sample_delevary_date from v_style_requisition');
        // dd($reqlist);

        return view('merch/sample/sample_requisition_list');
    }


  //  sample requisition data table query

    public function listSelect(Request $request){

        if($request->ajax())
            {
                $data=DB::select('select buyer,style ,prd_type_name product,gmt_name garment_description ,color,sample_name,quantity,requisition_date,send_date sample_delevary_date,req_id,test_send_date,test_status from v_style_requisition');

                return DataTables::of($data)
                ->addIndexColumn()
                // ->addColumn('action', function($data){
                //    $return = "<a href=".url('merch/sample/sample_requisition_edit/'.$data->req_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Edit\">
                //             <i class=\"ace-icon fa fa-edit bigger-120 fa-fw\"></i>
                //         </a>
                //         <a href=".url('merch/sample/sample_requisition_view/'.$data->req_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"View\">
                //              <i class=\"ace-icon fa fa-eye bigger-120 fa-fw\"></i>
                //          </a>
                //         <a href=".url('merch/sample/sample_requisition_delete/'.$data->req_id)." class=\"btn btn-xs btn-primary\" data-toggle=\"tooltip\" title=\"Delete\">
                //              <i class=\"ace-icon fa fa-trash bigger-120 fa-fw\"></i>
                //          </a>";     
                //     $return .= "</div>";
                //     return $return; 
                //     })
                // las la-cog icon-action action-icon-group
                 ->addColumn('action', function ($data) {
               $return=" <center><div class=\"btn-group \">
               
                                <a type=\"button\"  data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"true\" title=\"Action\">
                                <i class=\"las la-cog icon-action action-icon-group\"></i>  
                                </a>

                               
                            <div class=\"dropdown-menu \">
                            
                                  
                                <a href=".url('merch/sample/sample_requisition_edit/'.$data->req_id)." class=\"dropdown-item btn btn-xs btn-secondary add-new\" data-toggle=\"tooltip\" title=\"Edit\">
                                <i class=\"ace-icon fa fa-pencil icon-color-edit\"></i>
                                  
                                </a>
                            
                                <a href=".url('merch/sample/sample_requisition_view/'.$data->req_id)." class=\"dropdown-item btn btn-xs btn-secondary add-new\" data-toggle=\"tooltip\" title=\"View\">
                                <i class=\"ace-icon fa fa-eye icon-color-bom\"></i>
                                  
                                </a>

                                <a href=".url('merch/sample/sample_requisition_delete/'.$data->req_id)." class=\"dropdown-item btn btn-xs btn-secondary add-new\" data-toggle=\"tooltip\" title=\"Delete\">
                                <i class=\"ace-icon fa fa-trash icon-color-costing\"></i>
                                  
                                </a>

                            </div>
                           
                        </div></center>";
             return $return;


            })
                ->rawcolumns(['action'])
                ->make(true);  
             }   
        } 

        // Sample Rfp edit

        public function splreqedit(Request $request){
             $id=$request->id;

             // dd($id);
        $data['req_data']=DB::table('mr_sample_requisition')->where('req_id',$id)->first();
        $req=DB::table('mr_sample_requisition')->where('req_id',$id)->first();
        // dd($req);
        $data['unit']     = collect(unit_by_id())->pluck('hr_unit_name', 'hr_unit_id')->toArray();
        $data['buyer']     = collect(buyer_by_id())->pluck('b_name', 'b_id')->toArray();
        $data['productType']  = collect(product_type_by_id())->pluck('prd_type_name', 'prd_type_id');
        $data['machine']      = collect(special_machine_by_id())->pluck('spmachine_name', 'spmachine_id');
        $data['garmentsType'] = collect(garment_type_by_id())->pluck('gmt_name','gmt_id');
        $data['country']      = collect(country_by_id())->pluck('cnt_name','cnt_name');
        $data['brand']        = collect(brand_by_id())->pluck('br_name', 'br_id');
        $data['sampleType']   = collect(sample_type_by_id())->pluck('sample_name','sample_id');
        $data['style']=collect(DB::Select('call mrf_style("'.$req->buyer.'")'))->pluck('stl_no','stl_id')->toArray();
        // dd($data['style']);
        $data['season']=collect(DB::Select('call mrf_season("'.$req->buyer.'")'))->pluck('se_name','se_id')->toArray();
        $data['supplier']=collect(DB::Select('call mrf_supplier'))->pluck('sup_name','sup_id')->toArray();
        $data['artical']=collect(DB::Select('call mrf_artical("'.$req->supplier.'")'))->pluck('art_name','id')->toArray();
        $data['sample_man']=DB::table('hr_as_basic_info as spl')
                                      ->select('spl.as_name' ,'spl.associate_id')
                                      ->whereIn('spl.as_designation_id', array('278',
                                                                                        '346',
                                                                                        '347',
                                                                                        '348',
                                                                                        '349',
                                                                                        '359',
                                                                                        '443',
                                                                                        '465'))
                                      ->pluck('as_name','associate_id');
               // dd($req);


             //Selected Wash Type Show
            $data['selectedWahsData'] = '';
            $selectedWashes =explode(',',$req->wash);
             // dd($selectedWashes);
            $tr_end1           = 0;
            $data['selectedWahsData'] .= '<table class="table">';
            $data['selectedWahsData'] .= '<tbody>';

            foreach ($selectedWashes as $k=>$selW) {
                $wname=DB::table('mr_wash_type')->select('wash_name')->where('id',$selW)->first();
                // dd($wname);
            // $tr_end1 = $k+1;
            $data['selectedWahsData'] .= '<td style="border-bottom: 1px solid lightgray;">'.$wname->wash_name.'</td>';
            $data['selectedWahsData'] .= '<input class="washType" type="hidden" name="wash[]" value="'.$selW.'"></input>';

            if($tr_end1 == 1 || $tr_end1 == 2 || $tr_end1 == 4) {
            $data['selectedWahsData'] .= '</tr>';
            }


            }
            $data['selectedWahsData'] .= '</tbody>';
            $data['selectedWahsData'] .= '</table>';
            
            // dd($request->id);
           // select Sizegroup
            // $sz =DB::table('sample_req_size as a')
            //          ->leftJoin('mr_product_size as b','a.size_id','b.id')
            //          ->where('a.sql_req_id',$request->id)
            //          ->pluck('size_qty','b.mr_product_pallete_name');
            // dd($req->style);
            $allsz =DB::table('mr_stl_size_group as a')
                     // ->select('b.id','c.size_qty')
                     ->leftJoin('mr_product_size as b','a.mr_product_size_group_id','b.mr_product_size_group_id')
                     ->leftJoin('sample_req_size as c','b.id','c.size_id')
                     ->leftJoin('mr_sample_requisition as d','d.req_id','c.sql_req_id')
                     // ->where('c.sql_req_id',$id)
                     ->where('a.mr_style_stl_id',$req->style)
                     // ->get();
                     // ->pluck('c.size_qty','b.mr_product_pallete_name');
                     ->pluck('b.mr_product_pallete_name','b.id');

                     $sz=DB::table('mr_stl_size_group as a')
                     // ->select('c.size_qty')
                     ->leftJoin('mr_product_size as b','a.mr_product_size_group_id','b.mr_product_size_group_id')
                     ->leftJoin('sample_req_size as c','b.id','c.size_id')
                     ->where('c.sql_req_id',$id)
                     ->where('a.mr_style_stl_id',$req->style)
                     // ->get();
                     ->pluck('c.size_qty','b.id')->toArray();


              // dd($allsz,$sz);
            $data['sizeGroupDatatoShow'] = view('merch/sample/sample_requisition_size_edit',compact('sz','allsz'))->render();

            // $data['sizeGroupDatatoShow']= 


             // dd($data);
            return view('merch/sample/sample_requisition_edit',$data);

        }    
        
  // Sample requisition update
           public function splupdate(Request $request,$id){
            // dd(auth()->user()->associate_id);
                 // return $request->all();
             // dd($request->all());
            DB::beginTransaction();
            try{


            // dd($request->all());

            $currentValue = $request->size_val;
            $currentValue = array_filter($currentValue);

            $getSampleSize = DB::table('sample_req_size')
                    ->where('sql_req_id',$id)->pluck('sql_req_id', 'size_id')->toArray();

            

            // delete 
            $deleteSize = array_diff_key($getSampleSize, $currentValue);
            foreach($deleteSize as $key => $d){
                DB::table('sample_req_size')
                ->where('size_id', $key)
                ->where('sql_req_id', $d)
                ->delete();
            }

            // create 
            $insertSize = array_diff_key($currentValue, $getSampleSize);
            foreach($insertSize as $key => $i){
                DB::table('sample_req_size')
                ->insert([
                    'sql_req_id' => $id,
                    'size_id' => $key,
                    'size_qty' => $i
                ]);
            }
            // insert
            // dd($getSampleSize, $currentValue, $insertSize, $deleteSize);

            // DB::table('sample_req_size')->whereIn('')

            $sampleStyle = '';
            $splWash = '';
            if(isset($request->mr_sample_style)){
                $splType = implode(',', $request->mr_sample_style);
            }

            if(isset($request->wash)){
                $splWash = implode(',',$request->wash);
            }
                DB::table('mr_sample_requisition')
                // ->where('id', $request->id)
                ->where('req_id',$id)
                ->update([
                    'buyer'=>$request->buyer,
                    'brand'=>$request->mr_brand_br_id,
                    'style'=>$request->mr_style,
                    'style_code'=>$request->stl_code,
                    'description'=>$request->stl_des,
                    'sample_type'=> $splType,
                    'wash'=>$splWash,
                    'product_type'=>$request->prd_type_id,
                    'garment_type'=>$request->gmt_id,
                    'season'=>$request->se_id,
                    'product_category'=>$request->product_category,
                    'color'=>$request->color,
                    'size'=>$request->size,
                    'supplier'=>$request->Supplier_id,
                    'artical'=>$request->artical_id,
                    'quantity'=>$request->qty_id,
                    'send_date'=>$request->send_date,
                    'sample_man_name'=>$request->sample_man,
                    'stl_smv'=>$request->smv,
                    'hr_unit_id'=>$request->unit,
                    'entry_user'=>auth()->user()->associate_id
                     ]);  


                   // $lastid = DB::getPdo()->lastInsertId();

            // dd($request->size_val);
            // $d = [];
            if(isset($request->size_val)) {
                foreach($request->size_val as $key => $value){
                    // $d[] =$value;
                    // dd($value);
                    // if(!empty($value[0])){
                    if($value!=null ||$value!=0){
                       // dd($value);
                DB::table('sample_req_size')
                    ->where('sql_req_id',$id)
                    ->where('size_id',$key)
                    ->update([
                    'size_qty' =>$value,
                    // 'size_qty' => implode('',$value),
                    'size_id' => $key,
                    'sql_req_id' => $id
                    ]);
                    }
                }
            }


                DB::commit();

                toastr()->success('Successfully Saved');
                // return back()->with('success', 'Successfully added');
                return redirect('merch/sample/sample_requisition_list');
                // return back(); 
               } catch (\Exception $e) {
                DB::rollback();
                toastr()->error($e->getMessage());
                return back();   
               }
            } 
        public function splreqdelete(Request $request,$id){

               try{

               // DB::table('store_receive')
               //  // ->where('id', $request->id)
               //  ->where('id',$id)
               //  ->delete();

                DB::table('mr_sample_requisition')->where('req_id', $request->id)->delete(); 
                toastr()->success('Successfully Saved');
                return back(); 

            }catch(\Exception $e){
                toastr()->error($e->getMessage());
                return back(); 
            }
        }

        public function splreqview(Request $request,$id){

            // dd($request);
        $data=[];
        $data['id']=$id;
        $data['unit']=DB::table('mr_sample_requisition as a')->select('b.hr_unit_name')
                     ->leftJoin('hr_unit as b','a.hr_unit_id','b.hr_unit_id')
                     ->where('a.req_id',$id)
                     ->first();
                     // dd($data['unit']);



        $data['splview']=DB::table('v_style_requisition')->where('req_id',$id)->first();
        $data['requsitsize']=DB::table('mr_stl_size_group as a')
                     // ->select('b.id','c.size_qty')
                     ->leftJoin('mr_product_size as b','a.mr_product_size_group_id','b.mr_product_size_group_id')
                     ->leftJoin('sample_req_size as c','b.id','c.size_id')
                     ->where('c.sql_req_id',$id)
                     ->pluck('c.size_qty','b.mr_product_pallete_name');
        // dd($requsitsize);
        $data['spl_man']=DB::table('hr_as_basic_info as sample_man')->select('as_name')->where('associate_id',$data['splview']->sample_man)->first();
        $data['req_merchandiser']=DB::table('hr_as_basic_info as sample_man')->select('as_name')->where('associate_id',$data['splview']->entry_user)->first();
        $data['stl']=DB::table('mr_style as style')->select('stl_img_link','stl_id')->where('stl_no',$data['splview']->style)->first();

         $data['styleImages'] = DB::table('mr_style_image')->where('mr_stl_id',$id)->get();
        
        /// BOM single view add /////////////
            $stylebom_id=$data['stl']->stl_id;

            // dd($data['stl']);
            $data['stylebom_id']=$stylebom_id;
            $data['style'] = DB::table("mr_style AS s")
                        ->select(
                        "s.stl_id",
                        "s.stl_type",
                        "s.stl_no",
                        "b.b_name",
                        "t.prd_type_name",
                        "g.gmt_name",
                        "s.stl_product_name",
                        "s.stl_description",
                        "se.se_name",
                        "s.stl_smv",
                        "s.stl_img_link",
                        "s.stl_addedby",
                        "s.stl_added_on",
                        "s.updated_by",
                        "s.updated_at",
                        "s.stl_status",
                        "s.techpack"
                        )
                        ->leftJoin("mr_buyer AS b", "b.b_id", "=", "s.mr_buyer_b_id")
                        ->whereIn('b.b_id', auth()->user()->buyer_permissions())
                        ->leftJoin("mr_product_type AS t", "t.prd_type_id", "=", "s.prd_type_id")
                        ->leftJoin("mr_garment_type AS g", "g.gmt_id", "=", "s.gmt_id")
                        ->leftJoin("mr_season AS se", "se.se_id", "=", "s.mr_season_se_id")
                        ->where("s.stl_id", $stylebom_id)
                        ->first();
                    //dd($style);exit;

                    $data['styleImages'] = DB::table('mr_style_image')->where('mr_stl_id',$stylebom_id)->get();

                    
                    $data['samples'] = DB::table("mr_stl_sample AS ss")
                            ->select(DB::raw("GROUP_CONCAT(st.sample_name SEPARATOR ', ') AS name"))
                            ->leftJoin("mr_sample_type AS st", "st.sample_id", "ss.sample_id")
                            ->where("ss.stl_id", $stylebom_id)
                            ->first();

            //operations
            
            $data['operations'] = DB::table("mr_style_operation_n_cost AS oc")
                            ->select("o.opr_name")
                            ->select(DB::raw("GROUP_CONCAT(o.opr_name SEPARATOR ', ') AS name"))
                            ->leftJoin("mr_operation AS o", "o.opr_id", "oc.mr_operation_opr_id")
                            ->where("oc.mr_style_stl_id", $stylebom_id)
                            ->first();

            //machines
            
            $data['machines'] = DB::table("mr_style_sp_machine AS sm")
                            ->select(DB::raw("GROUP_CONCAT(m.spmachine_name SEPARATOR ', ') AS name"))
                            ->leftJoin("mr_special_machine AS m", "m.spmachine_id", "sm.spmachine_id")
                            ->where("sm.stl_id", $stylebom_id)
                            ->first();


            //style bom information
            
            $data['styleCatMcats'] = DB::table("mr_stl_bom_n_costing")
                                ->leftJoin('mr_material_category','mr_stl_bom_n_costing.mr_material_category_mcat_id','=','mr_material_category.mcat_id')
                                ->leftJoin('mr_cat_item','mr_stl_bom_n_costing.mr_cat_item_id','=','mr_cat_item.id')
                                ->leftJoin('mr_supplier','mr_stl_bom_n_costing.mr_supplier_sup_id','=','mr_supplier.sup_id')
                                ->leftJoin('mr_article','mr_stl_bom_n_costing.mr_article_id','=','mr_article.id')
                                ->leftJoin('mr_composition','mr_stl_bom_n_costing.mr_composition_id','=','mr_composition.id')
                                ->leftJoin('mr_construction','mr_stl_bom_n_costing.mr_construction_id','=','mr_construction.id')
                                ->leftJoin('mr_material_color','mr_stl_bom_n_costing.clr_id','=','mr_material_color.clr_id')
                                ->where('mr_stl_bom_n_costing.mr_style_stl_id',$stylebom_id)
                                ->get()
                                ->groupBy('mcat_name');

                                
            

            

            
            //dd($styleCatMcats);exit;
            
            
            
            // $styleImages = DB::table('mr_style_image')->where('mr_stl_id',$stylebom_id)->get();

                        $styleImages =[];

                        // if(isset($request->export)){
                        //     $filename = 'Style-Bom';
                        //     $filename .= '.xlsx';
                        //     return Excel::download(new StyleBomExport($data), $filename);
                        // }

             // dd($data['samples']);
            // $data['BomRender']= view('merch/sample/splreq_style_bom_single_view', $data)->render();
            $data['BomRender']= view('merch/sample/sampal_bom_add', $data)->render();
            //dd($data['BomRender']);
            /// END BOM single view ////////////  

       
          
         if(isset($request->export)){
                            $filename = 'Sample-Rfp';
                            $filename .= '.xlsx';
                            return Excel::download(new SampleExport($data), $filename);
                        }

            // Return view('merch/sample/sample_requisition_view',compact('id','unit','splview','spl_man','styleImages','stl'));
            Return view('merch/sample/sample_requisition_view',$data);
        }

        public function consumption(Request $request){
          // return($request->all());
    // $data=DB::table('v_stl_sample_requisition')->where('stl_id',$request->mr_style)->first();
        $data=DB::table('mr_style')
             ->select('unit_id' ,'mr_buyer_b_id','stl_id','stl_no','mr_brand_br_id','stl_product_name','prd_type_id','gmt_id','stl_smv','gender','mr_season_se_id','stl_year','stl_img_link','techpack')
             ->where('stl_id',$request->mr_style)->first();
            if($data != ''){
                $data->sample_id = DB::table('mr_stl_sample')->where('stl_id',$request->mr_style)->pluck('sample_id');

                 $data->wash_names =DB::table('mr_stl_wash_type as p')
                    ->leftjoin('mr_wash_type as z','z.id','p.mr_wash_type_id')
                    ->where('p.mr_style_stl_id',$request->mr_style)
                    ->pluck('wash_name','mr_wash_type_id');
                    // pluck returens {value, key}
                /* $req = DB::table('mr_stl_wash_type as p')
                ->where('p.mr_style_stl_id',$request->mr_style)->get();*/
                $data->product_size =DB::table('mr_stl_size_group as a')
                     ->leftJoin('mr_product_size as b','a.mr_product_size_group_id','b.mr_product_size_group_id')
                     ->where('a.mr_style_stl_id',$request->mr_style)
                     ->pluck('mr_product_pallete_name','b.id');      
                }


            $data->viewRender = view('merch/sample/sample_requisition_wash',compact('data'))->render();
            $data->viewRendersize = view('merch/sample/sample_requisition_size',compact('data'))->render();
            return response()->json($data);

        }

}
