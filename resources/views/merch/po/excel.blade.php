<table>
    <tr>
        <td align="center" colspan="14"
            style=" font-weight: bold; font-size:15px; border:1px solid black; background:#d9d9d9;"><h4>Po Bom</h4></td>
    </tr>
   

    <tr>
                                        
        <th colspan="2" style="font-weight: bold; font-size:11px;border:1px solid black;">Internal Order No :</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; border:1px solid black;">{{ (!empty($order->order_code)?$order->order_code:null) }}</td>

        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Order Quantity :</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; text-align: left; border:1px solid black;">{{ (!empty($order->order_qty)?$order->order_qty:null) }}</td>

    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Buyer :</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $getBuyer[$order->mr_buyer_b_id]->b_name??'' }}</td>


        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Unit</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; border:1px solid black;"> {{ $getUnit[$order->unit_id]['hr_unit_name']??'' }} </td>

    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Delivery Date</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; border:1px solid black;">{{ custom_date_format($order->order_delivery_date) }}</td>


        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Style No</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $order->style->stl_no??'' }}</td>
    </tr>
    <tr>
        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Season No</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; border:1px solid black;">{{  $getSeason[$order->style->mr_season_se_id]->se_name??'' }} - {{ $order->style->stl_year??'' }}</td>

        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Po No</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $po->po_no }}</td>
    </tr>

    <tr>
        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Po Qty</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px;  text-align: left; border:1px solid black;">{{ $po->po_qty??'' }}</td>

        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Po Ex FTY</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; border:1px solid black;">{{ custom_date_format($po->po_ex_fty) }}</td>
    </tr>

    <tr>
        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Country Name</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $getCountry[$po->po_delivery_country]->cnt_name??'' }}</td>

        <th colspan="2" style="font-weight: bold; font-size:11px; border:1px solid black;">Color</th>
        <td colspan="2"
            style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $getColor[$po->clr_id]->clr_name??'' }}</td>
    </tr>

</table>


<table>
    <thead>
    <tr>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Main Category</th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Item Name</th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Item <br>Description</th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Supplier</th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Article No/<br>Item Code <br><small>(Thread-tex/count)</small></th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Color/<br>Shades</th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Size/Width</th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Thread Brand</th>
  
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">UoM</th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Consumption</th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Extra (%)</th>
        
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Per Unit Consumption</th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Garments Qty</th>
        <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Required Qty</th>
    </tr>
    </thead>

    @foreach($itemCategory as $itemCat)
                                            <tbody>
                                               
                                                
                                                @if(count($groupBom) > 0 && isset($groupBom[$itemCat->mcat_id]))
                                                @foreach($groupBom[$itemCat->mcat_id] as $itemBom)
                                                    <tr>
                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                            {{ $itemCat->mcat_name }}
                                                        </td>
                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">{{ $getItems[$itemBom->mr_cat_item_id]->item_name??'' }}</td>
                                                        
                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                            {{ $itemBom->item_description }}
                                                        </td>
                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;"> {{ $getSupplier[$itemBom->mr_supplier_sup_id]->sup_name??'' }} </td>
                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;"> {{ $getArticle[$itemBom->mr_article_id]->art_name??'' }} </td>
                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;"> {{ $getColor[$itemBom->clr_id]->clr_name??'' }} </td>
                                                        
                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                            {{ $itemBom->size }}
                                                        </td>
                                                     
                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                        {{$itemBom->thread_brand}}
                                                        </td>
                                             

                                                


                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                            {{ $itemBom->uom }}
                                                        </td>
                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                            {{ $itemBom->consumption }}
                                                        </td>
                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                            {{$itemBom->extra_percent}}
                                                        </td>
                                                        

                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                            {{ number_format($itemBom->total,4, '.', '') }}
                                                        </td>
                                                        

                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                            {{($itemBom->mcat_id==1)?
                                                                (!empty($itemBom->gmt_qty>0)?$itemBom->gmt_qty:$order->order_qty):$itemBom->gmt_qty}}

                                                        </td>

                                                        <td style="font-weight: bold; font-size:11px; border:1px solid black;">
                                                            {{$itemBom->requiredqty}}
                                                        </td>
                                                     

                                                    </tr>
                                                @endforeach
                                                @endif

                                               

                                            </tbody>
                                            @endforeach
                                            
   

    
   
</table>


