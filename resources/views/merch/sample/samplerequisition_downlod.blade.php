{{-- <table>
	<tbody>
		<tr>
			<td colspan="4" style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
		</tr>
		<tr>
			<th> Buyer :</th>
			<td> </td>
			{{$splview->buyer}}
		</tr>
	</tbody>




</table>  --}}


<table >
                                    <tr>
                                        <td align="center" colspan="13" style=" font-weight: bold; font-size:15px; border:1px solid black; background:#d9d9d9;"><h4>{{$unit->hr_unit_name}}</h4></td>
                                    </tr>
                                    <tr>
                                    	<td  align="center" colspan="13" style="text-align: center;border: 2px solid #1b1b1c;font-weight:bold;font-size: 16px;"> SAMPLE REQUISITION FORM </td>
                                    </tr>
                                    
                                    
                                    
                                    <tr>
                                        
                                        <th colspan="2" style="font-weight: bold; font-size:11px;border:1px solid black;">Atten :</th>
                                        <td colspan="4" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($spl_man->as_name)?$spl_man->as_name:null) }}</td>
                                        
                                    </tr>
                                     <tr>
                                        
                                        <th colspan="2" style="font-weight: bold; font-size:11px;border:1px solid black;">Merchandiser :</th>
                                        <td colspan="4" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($req_merchandiser->as_name)?$req_merchandiser->as_name:null) }}</td>
                                        
                                    </tr>
                                    <tr>
                                    	<td style="padding-left:28px;font-weight: bold;font-size: 15px; border: 2px solid #1b1b1c;" colspan="13">
                                            PLS ARRANGE THE FOLLOWING SAMPLES.</td>
                                    </tr>
</table>
<div>
<table style=" border:1px solid black;">

	                                	

		<tr><th style="margin:0;padding:4px 10px"><strong >Buyer &nbsp;Name :</strong></th>
			<td align="left">{{ $splview->buyer }}</td></tr>
    <tr><th style="margin:0;padding:4px 10px"><strong >Style :</strong></th>
    	<td align="left">{{ $splview->style }}</td></tr>
    <tr><th style="margin:0;padding:4px 10px"><strong >Order/Product No :</strong></th>
    	<td align="left">{{ $splview->prd_type_name }}</td></tr>
    <tr><th style="margin:0;padding:4px 10px"><strong >Garments &nbsp;Description :</strong></th><td align="left">{{ $splview->style_description }}</td></tr>
    <tr><th style="margin:0;padding:4px 10px"><strong >Color :</strong></th>
    	<td align="left">{{ $splview->color }}</td></tr>
    <tr><th style="margin:0;padding:4px 10px"><strong >Size :</strong></th>
    	<td align="left" >{{ $splview->size }}</td></tr>
    <tr><th style="margin:0;padding:4px 10px"><strong >Wash :</strong></th>
    	<td align="left">{{ $splview->wash }}</td></tr>
    <tr><th style="margin:0;padding:4px 10px"><strong >Type Of Sample :</strong></th>
    	<td align="left">{{ $splview->sample_name }}</td></tr>
    <tr><th style="margin:0;padding:4px 10px"><strong >Requesition Date :</strong></th>
    	<td align="left">{{ $splview->requisition_date }}</td></tr>
    <tr><th style="margin:0;padding:4px 10px"><strong >Garment Delivery Date :</strong></th>
    	<td align="left">{{ $splview->send_date }}</td></tr>
                                       
	                                	
	                                
</table>

<table class="table table-bordered" style=" border:1px solid black;">
                <thead>
                <tr>
                <th> <strong> Sample size </strong></th>
                <th><strong> Qty </strong></th>
                </tr>

                </thead>
                <tbody>
                    @foreach($requsitsize as $key=>$value)
                    <tr>
                        <td align="left"> {{$key}} </td>
                        <td align="left"> {{$value}} </td>
                    </tr>
                    @endforeach
                    <tr>
                    <td align="left" style="font-weight: bold;">Total :</td> 
                    <td align="left" style="font-weight: bold;">{{ collect($requsitsize)->sum()}}</td> 
                    </tr>
                </tbody>
                </table>
                                    {{-- <tr>
                                        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Buyer</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($style->b_name)?$style->b_name:null) }}</td>
                                        <td></td>
                                        
                                        <th colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Production Type</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;"></td>
                                        
                                    </tr>
                                    <tr>
                                        
                                        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Sample Type</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($samples->name)?$samples->name:null) }}</td>
                                        <td></td>
                                        
                                        <th colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Operation</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($operations->name)?$operations->name:null) }}</td>
                                        
                                    </tr>
                                    <tr>
                                        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">SMV/PC</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($style->stl_smv)?$style->stl_smv:null) }}</td>
                                        <td></td>
                                        
                                        <th colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Speacial Machine</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($machines->name)?$machines->name:null) }}</td>
                                        
                                    </tr>
                                    <tr>
                                        
                                        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Remarks</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($style->stl_description)?$style->stl_description:null) }}</td>
                                    </tr> --}}

                                                                                            
   
{{-- <table>
                                        <thead>
                                        <tr>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Main Category</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Item</th>
                                            
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Description</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Color</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Size/Width</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Supplier</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Article</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Thread Brand</th>
                                            
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">UoM</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Consumption</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Extra (%)</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Extra Qty</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php if(count($styleCatMcats) == 0){ ?>
                                        <tr>
                                            <td colspan="15"><h4
                                                    class="text-center">No BOM
                                                    found for this style</h4>
                                            </td>
                                        </tr>
                                        <?php }else{ ?>
                                            @foreach ($styleCatMcats as $catwise)
                                                @foreach ($catwise as $styleCatMcat)
                                            <tr>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->mcat_name}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->item_name}}</td>
                                            
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->item_description}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->clr_name}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->size}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->sup_name}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->art_name}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->thread_brand}}</td>
                                                
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->uom}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->consumption}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->extra_percent}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;"><?= ($styleCatMcat->consumption * $styleCatMcat->extra_percent) / 100 ?></td>
                                            
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;"><?= $styleCatMcat->extra_percent != 0 ? (($styleCatMcat->consumption * $styleCatMcat->extra_percent) / 100) + $styleCatMcat->consumption: 0  ?></td> 
                                            </tr>
                                            @endforeach
                                        @endforeach
                                        <?php } ?>
                                        </tbody>
</table>