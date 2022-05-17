
                                                                
<table>
    <tr>
        <td align="center" colspan="19" style=" font-weight: bold; font-size:15px; border:1px solid black; background:#d9d9d9;"><h4>Style Costing</h4></td>
    </tr>
    <tr></tr>
    
    
    
    <tr>
        
        <th colspan="2" style="font-weight:  font-size:11px;border:1px solid black;">Style Reference 1</th>
        <td colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">{{ (!empty($style->stl_no)?$style->stl_no:null) }}</td>
        <td></td>
        
        <th colspan="1" style="font-weight:  font-size:11px; border:1px solid black;">Style Reference 2</th>
        <td colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">{{ (!empty($style->stl_product_name)?$style->stl_product_name:null) }}</td>
        
    </tr>
    <tr>
        <th colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">Buyer</th>
        <td colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">{{ (!empty($style->b_name)?$style->b_name:null) }}</td>
        <td></td>
        
        <th colspan="1" style="font-weight:  font-size:11px; border:1px solid black;">Production Type</th>
        <td colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">{{-- (!empty($style->stl_type)?($style->stl_type=='Development'?'Development':'Bulk'):null) --}}</td>
        
    </tr>
    <tr>
        
        <th colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">Sample Type</th>
        <td colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">{{ (!empty($samples->name)?$samples->name:null) }}</td>
        <td></td>
        
        <th colspan="1" style="font-weight:  font-size:11px; border:1px solid black;">Operation</th>
        <td colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">{{ (!empty($operations->name)?$operations->name:null) }}</td>
        
    </tr>
    <tr>
        <th colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">SMV/PC</th>
        <td colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">{{ (!empty($style->stl_smv)?$style->stl_smv:null) }}</td>
        <td></td>
        
        <th colspan="1" style="font-weight:  font-size:11px; border:1px solid black;">Speacial Machine</th>
        <td colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">{{ (!empty($machines->name)?$machines->name:null) }}</td>
        
    </tr>
    <tr>
        
        <th colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">Remarks</th>
        <td colspan="2" style="font-weight:  font-size:11px; border:1px solid black;">{{ (!empty($style->stl_description)?$style->stl_description:null) }}</td>
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
            
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Composition</th>
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Construction</th>
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Consumption</th>
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Extra (%)</th>
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">UoM</th>
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Terms</th>
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">FOB</th>
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">L/C</th>
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Frieght</th>
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Unit Price</th>
            <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Total Price</th>
        </tr>
        </thead>
        <tbody>
            @if(!empty($other_cost))
                @if($other_cost->agent_fob != 0)
                    @if(count($styleCatMcatFabs) > 0)
                       

                            @php
                            $netFab = 0; @endphp
                            @foreach ($styleCatMcatFabs as $styleCatMcat)
                            <tr>
                             @php
                            $thisUnit = $styleCatMcat->precost_unit_price;
                            $thisTotal = ($thisUnit + $thisUnit * ($styleCatMcat->extra_percent / 100)) * $styleCatMcat->consumption;
                            $netFab = $netFab + $thisTotal;
                            @endphp
                            <td style="font-size:11px; border:1px solid black;"> {{ $styleCatMcat->mcat_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->item_name}}</td>
                            
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->item_description}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->sup_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->art_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->clr_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->size}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->thread_brand}}</td>
                            
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->composition}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->construction}}</td>
                            
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->consumption}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->extra_percent}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->uom}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->bom_term}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->precost_fob}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->precost_lc}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->precost_freight}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $thisUnit}}</td>
                            <th style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $thisTotal}}</th>
                        </tr>
                    @endforeach
                        <tr>
                        </tr>
                        <tr>
                            <th colspan="18"
                                class="text-center" style="font-weight:bold; font-size:11px; border:1px solid black;"> Total
                                Fabric Price
                            </th>
                            <th style="font-weight:bold; font-size:11px; border:1px solid black;">{{ $netFab }}</th>
                        </tr>
                        @endif
                        @if(count($styleCatMcatSwings) > 0)
                        
                            @php
                            $netSwings = 0; @endphp
                            @foreach ($styleCatMcatSwings as $styleCatMcat)
                            <tr>
                            @php
                            $thisUnit = $styleCatMcat->precost_unit_price;
                            $thisTotal = ($thisUnit + $thisUnit * ($styleCatMcat->extra_percent / 100)) * $styleCatMcat->consumption;
                            $netSwings = $netSwings + $thisTotal;
                            @endphp
                            <td  style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->mcat_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->item_name}}</td>
                            
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->item_description}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->sup_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->art_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->clr_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->size}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->thread_brand}}</td>
                            
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->composition}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->construction}}</td>
                            
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->consumption}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->extra_percent}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->uom}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->bom_term}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->precost_fob}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->precost_lc}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $styleCatMcat->precost_freight}}</td>
                            <td style="font-size:11px; border:1px solid black;">
                            {{ $thisUnit }} </td>
                            <th style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $thisTotal }}</th>
                        </tr>
                        @endforeach
                        <tr>
                        </tr>
                        <tr>
                            <th colspan="18"
                                class="text-center" style="font-weight: bold; font-size:11px; border:1px solid black;"> Total
                                Sweing Accessories Price
                            </th>
                            <th style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $netSwings }}</th>
                        </tr>
                        @endif
                    @if(count($styleCatMcatFinishing) > 0)
                        
                            @php
                            $netFinishing = 0; @endphp
                            @foreach ($styleCatMcatFinishing as $styleCatMcat)
                            <tr>
                            @php
                            $thisUnit = $styleCatMcat->precost_unit_price;
                            $thisTotal = ($thisUnit + $thisUnit * ($styleCatMcat->extra_percent / 100)) * $styleCatMcat->consumption;
                            $netFinishing = $netFinishing + $thisTotal;
                            @endphp
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->mcat_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->item_name}}</td>
                            
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->item_description}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->sup_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->art_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->clr_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->size}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->thread_brand}}</td>
                            
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->composition}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->construction}}</td>
                            
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->consumption}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->extra_percent}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->uom}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->bom_term}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->precost_fob}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->precost_lc}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $styleCatMcat->precost_freight}}</td>
                            <td style="font-size:11px; border:1px solid black;">{{ $thisUnit }} </td>
                            <th style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $thisTotal }}</th>
                        </tr>
                    @endforeach
                        <tr>
                        </tr>
                        <tr>
                            <th colspan="18"
                                class="text-center" style="font-weight: bold; font-size:11px; border:1px solid black;"> Total
                                Finising Price
                            </th>
                            <th style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $netFinishing }}</th>
                        </tr>
                    @endif
                        @if(count($special_operations) > 0) 
                        @foreach ($special_operations as $special_operation)
                        <tr>
                            <td colspan='10'
                                class='text-center' style="font-size:11px; border:1px solid black;">{{$special_operation->opr_name}}</td>
                            <td style="font-size:11px; border:1px solid black;">1</td>
                            <td style="font-size:11px; border:1px solid black;">0</td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{$special_operation->uom}}
                            </td>
                            <td colspan='4' style="font-size:11px; border:1px solid black;"></td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{$special_operation->unit_price}}
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{$special_operation->unit_price}}
                            </td>
                        </tr>
                        @endforeach
                         @endif
                        <tr>
                            <td colspan="10"
                                class="text-center" style="font-size:11px; border:1px solid black;">Testing Cost
                            </td>
                            <td class="consumption" style="font-size:11px; border:1px solid black;">1</td>
                            <td style="font-size:11px; border:1px solid black;">0</td>
                            <td style="font-size:11px; border:1px solid black;">Piece</td>
                            <td colspan="4" style="font-size:11px; border:1px solid black;"></td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->testing_cost)?$other_cost->testing_cost:''}}
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->testing_cost)?$other_cost->testing_cost :''}}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10"
                                class="text-center" style="font-size:11px; border:1px solid black;">CM
                            </td>
                            <td class="consumption" style="font-size:11px; border:1px solid black;">1</td>
                            <td style="font-size:11px; border:1px solid black;">0</td>
                            <td style="font-size:11px; border:1px solid black;">Piece</td>
                            <td colspan="4" style="font-size:11px; border:1px solid black;"></td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->cm)?$other_cost->cm:''}}
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->cm)?$other_cost->cm:''}}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="10" class="text-right" style="font-size:11px; border:1px solid black;">
                                Commertial Cost
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                            </td>
                            <td colspan="6"
                                class="text-left"style="font-size:11px; border:1px solid black;" ></td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->commercial_cost)?$other_cost->commercial_cost:''}}
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->commercial_cost)?$other_cost->commercial_cost:''}}
                            </td>
                        </tr>
                        <tr>
                            <th colspan="18"
                                class="text-center" style="font-weight: bold; font-size:11px; border:1px solid black;">Net FOB
                            </th>
                            <th style="font-weight: bold; font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->net_fob)?$other_cost->net_fob:''}}
                            </th>
                        </tr>
                        <tr>
                            <td colspan="10" class="text-right" style="font-size:11px; border:1px solid black;">
                                Buyer Commision
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->buyer_comission_percent)?$other_cost->buyer_comission_percent:''}}
                            </td>
                            <td colspan="6" class="text-left" style="font-size:11px; border:1px solid black;">
                                %
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                                @php
                                $buyer_comm = number_format(($other_cost->net_fob * ($other_cost->buyer_comission_percent / 100)), 2, '.', '');
                                @endphp
                                {{ $buyer_comm }}
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{ $buyer_comm }}
                            </td>
                        </tr>
                        <tr>
                            <th colspan="18"
                                class="text-center" style="font-weight: bold; font-size:11px; border:1px solid black;">Buyer FOB
                            </th>
                            <th style="font-weight: bold; font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->buyer_fob)?$other_cost->buyer_fob:''}}
                            </th>
                        </tr>
                        <tr>
                            <td colspan="10" class="text-right" style="font-size:11px; border:1px solid black;">
                                Agent Commision
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->agent_comission_percent)?$other_cost->agent_comission_percent:''}}
                            </td >
                            <td colspan="6" class="text-left" >
                                %
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                                @php
                                $agent_comm = number_format(($other_cost->buyer_fob * ($other_cost->agent_comission_percent / 100)), 2, '.', '');
                                @endphp
                                {{ $agent_comm }}
                            </td>
                            <td style="font-size:11px; border:1px solid black;">
                                {{ $agent_comm }}
                            </td>
                        </tr>
                        <tr>
                            <th colspan="18"
                                class="text-center" style="font-weight: bold; font-size:11px; border:1px solid black;">Agent FOB
                            </th>
                            <th style="font-weight: bold; font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->agent_fob)?$other_cost->agent_fob:''}}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="18"
                                class="text-center" style="font-weight: bold; font-size:11px; border:1px solid black;">Total FOB
                            </th>
                            <th style="font-weight: bold; font-size:11px; border:1px solid black;">
                                {{ isset($other_cost->agent_fob)?$other_cost->agent_fob:''}}
                            </th>
                        </tr>
                        @else
                        <tr>
                            <td colspan="19" style="font-size:11px; border:1px solid black;"><h4
                                    class="text-center">No
                                    Costing found for this
                                    style</h4></td>
                        </tr>
                           @endif
                            @else
                        <tr>
                            <td colspan="19" style="font-size:11px; border:1px solid black;"><h4
                                    class="text-center">No
                                    Costing found for this
                                    style</h4></td>
                        </tr>
                        @endif
            </tbody>
        
    </table>
                                                                     
                                                                        
                                   