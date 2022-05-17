

    
  

            

            
  


                                                                
                                <table>
                                    <tr>
                                        <td align="center" colspan="13" style=" font-weight: bold; font-size:15px; border:1px solid black; background:#d9d9d9;"><h4>Style Bom</h4></td>
                                    </tr>
                                    <tr></tr>
                                    
                                    
                                    
                                    <tr>
                                        
                                        <th colspan="2" style="font-weight: bold; font-size:11px;border:1px solid black;">Style Reference 1</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($style->stl_no)?$style->stl_no:null) }}</td>
                                        <td></td>
                                        
                                        <th colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Style Reference 2</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($style->stl_product_name)?$style->stl_product_name:null) }}</td>
                                        
                                    </tr>
                                    <tr>
                                        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Buyer</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($style->b_name)?$style->b_name:null) }}</td>
                                        <td></td>
                                        
                                        <th colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Production Type</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{-- (!empty($style->stl_type)?($style->stl_type=='Development'?'Development':'Bulk'):null) --}}</td>
                                        
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
                                        <td align="left" colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($style->stl_smv)?$style->stl_smv:null) }}</td>
                                        <td></td>
                                        
                                        <th colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Speacial Machine</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($machines->name)?$machines->name:null) }}</td>
                                        
                                    </tr>
                                    <tr>
                                        
                                        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Remarks</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($style->stl_description)?$style->stl_description:null) }}</td>
                                    </tr>
                                </table>
                                                                                            
   
                                    <table 
                                            >
                                        <thead>
                                        <tr>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Main Category</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Item</th>
                                            
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Item Description</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Supplier</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Article No/Item Code</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Color/Shade</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Size/Width</th>
                                            
                                            
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
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->sup_name}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->art_name}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->clr_name}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $styleCatMcat->size}}</td>
                                                
                                                
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
                                                                 
                                                                    
                               