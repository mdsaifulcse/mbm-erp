<table>
                                    <tr>
                                        <td align="center" colspan="13" style=" font-weight: bold; font-size:15px; border:1px solid black; background:#d9d9d9;"><h4>Order Costing</h4></td>
                                    </tr>
                                    <tr></tr>
                                    
                                    {{-- @php
                                    dd($excelVieworder->hr_unit_name);
                                    @endphp --}}
                                    <tr>
                                        <th colspan="2" style="font-weight: bold; font-size:11px;border:1px solid black;">Unit</th>
                                        <td align="left" colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($excelVieworder->hr_unit_name)?$excelVieworder->hr_unit_name:null) }}</td>
                                   {{--  @php
                                    dd($excelView->hr_unit_name);
                                    @endphp --}}
                                        <td></td>

                                        <th colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Operation</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($operations->name)?$operations->name:null) }}</td>
                                         <td></td>
                                    </tr>
                                    <tr>
                                        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Buyer</th>
                                        <td align="left" colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($excelVieworder->b_name)?$excelVieworder->b_name:null) }}</td>
                                        <td></td>
                                        <th colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Speacial Machine</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($machines->name)?$machines->name:null) }}</td>
                                        
                                        
                                        
                                    </tr>
                                    <tr>
                                        <th colspan="2" style="font-weight: bold; font-size:11px;border:1px solid black;">Order</th>
                                        <td align="left" colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($excelVieworder->order_code)?$excelVieworder->order_code:null) }}</td>
                                        <td></td>
                                        
                                        <th colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Sample Type</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($samples->name)?$samples->name:null) }}</td>
                                        <td></td>
                                        
                                        
                                        
                                    </tr>
                                    <tr>
                                        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Order Qty</th>
                                        <td align="left" colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($excelVieworder->order_qty)?$excelVieworder->order_qty:null) }}</td>
                                        <td></td>
                                        <th colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Production Type</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{-- (!empty($style->stl_type)?($style->stl_type=='Development'?'Development':'Bulk'):null) --}}</td>
                                        
                                        
                                        
                                    </tr>
                                    <tr>
                                        
                                        <th align="left" colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Style</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($excelVieworder->stl_no)?$excelVieworder->stl_no:null) }}</td>
                                        <td></td>
                                         <th align="left" colspan="1" style="font-weight: bold; font-size:11px; border:1px solid black;">Remarks</th>
                                        <td colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;"> </td>
                                    </tr>
                                </table>
                                                                                            
   
                                    <table 
                                            >
                                        <thead>
                                        <tr>
                                            {{-- <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Main Category</th> --}}
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Item Name</th>
                                            
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Item Description</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Supplier</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Artical/Item Code</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Color/Shade</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Size/width</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Thread Brand</th>
                                            
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">UoM</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Consumption</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Extra (%)</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Total Consumption</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Terms</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">FOB</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">LC</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Fright</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Unit Price</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Total Price</th>

                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Required Qty</th>
                                            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Total value</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        {{-- @php
                                        dd($getBom);
                                        @endphp --}}
                                        <?php if(count($getBom) == 0){ ?>
                                        <tr>
                                            <td colspan="15"><h4
                                                    class="text-center">No BOM
                                                    found for this Order</h4>
                                            </td>
                                        </tr>
                                        <?php }else{ ?>
                                            @foreach ($excelView as $orderBomItem)
                                                {{-- @foreach ($catwise as $styleCatMcat) --}}
                                       {{--  @php
                                        dd($orderBomItem);
                                        @endphp --}}
                                            <tr>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                    {{$orderBomItem->item_name}}
                                                </td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->item_description}}</td>
                                            
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->sup_name}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->art_name}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->clr_id}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->size}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->thread_brand}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->uom}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->consumption}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->extra_percent}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;"><?= (($orderBomItem->consumption * $orderBomItem->extra_percent) / 100)+$orderBomItem->consumption ?></td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->bom_term}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->precost_fob}}</td>

                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->precost_lc}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->precost_freight}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->unitPrice}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->totalPrice}}</td>
                                            
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->required_qty}}</td>
                                                <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $orderBomItem->totalvalue}}</td> 
                                            </tr>
                                            {{-- @endforeach --}}
                                        @endforeach
                                        <?php } ?>
                                        </tbody>
                                    </table>