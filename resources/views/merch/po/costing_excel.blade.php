
                                        <table>
                                            <tr>
                                                <td align="center" colspan="21"
                                                    style=" font-weight: bold; font-size:15px; border:1px solid black; background:#d9d9d9;"><h4>Po Costing</h4></td>
                                            </tr>
                                            
                                        
                                            <tr>
                                        
                                                <th colspan="2"  style="font-weight: bold; font-size:11px;border:1px solid black;">Internal Order No :</th>
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
                                        <table class="table table-bordered table-hover table-fixed table-head" id="itemList">
                                            <thead>
                                                <tr class="text-center active">

                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Main Category</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Item</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Item Description</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Supplier</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Article No/Item Code</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Color/Shade</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Size/Width</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Thread Brand</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Consumption</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Extra (%)</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">UoM</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Terms</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">FOB</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">L/C</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Frieght</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Unit Price</th>
                                                    <th style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Total Price</th>
                                                    <th  style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Order Cost</th>
                                                    <th  style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Style Cost</th>
                                                    <th  style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Req. Qty</th>
                                                    <th  style="font-weight: bold; font-size:11px; border:1px solid black; background:#d9d9d9;">Total Value</th>
                                                </tr>
                                                
                                            </thead>

                                            @foreach($itemCategory as $itemCat)
                                            <tbody>
                                                
                                                @php $totalOrdPrice = 0;
                                                $totalPriceItemWise = 0;
                                                $tswingAndFinishing = 0;
                                                
                                                
                                               
                                                @endphp
                                                @if(count($groupBom) > 0 && isset($groupBom[$itemCat->mcat_id]))
                                                  @foreach($groupBom[$itemCat->mcat_id] as $itemBom)
                                                    @php
                                                      $itemOrdUnitPrice = 0;
                                                      $itemPrice = $itemBom->precost_unit_price;

                                                    @endphp
                                                    @if(isset($orderCosting[$itemBom->ord_bom_id]) && ($orderCosting[$itemBom->ord_bom_id]->mr_cat_item_id == $itemBom->mr_cat_item_id))
                                                      @php
                                                        $itemOrdUnitPrice = $orderCosting[$itemBom->ord_bom_id]->precost_unit_price??0;
                                                        
                                                      @endphp
                                                    @endif

                                                    @php
                                                        
                                                          $totalOrdPrice += $itemOrdUnitPrice;
                                                          $totalPrice = ((($itemBom->consumption * $itemBom->extra_percent)/100) + $itemBom->consumption)*$itemPrice??0;
                                                          $totalPriceItemWise +=$totalPrice;

                                                          if($itemCat->mcat_name == 'Sewing Accessories' || $itemCat->mcat_name == 'Finishing Accessories' ){
                                                            $tswingAndFinishing+=$totalPriceItemWise;
                                                          }
                                                          
                                                          
                                                    @endphp

                                                  <tr>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">{{ $itemCat->mcat_name }}</td>
                                                      
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                          
                                                          {{ $getItem[$itemBom->mr_cat_item_id]->item_name??'' }}
                                                      </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                          {{ $itemBom->item_description }}
                                                      </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;"> {{ $getSupplier[$itemBom->mr_supplier_sup_id]->sup_name??'' }} </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;"> {{ $getArticle[$itemBom->mr_article_id]->art_name??'' }} </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;"> {{ $getColor[$itemBom->clr_id]->clr_name??'' }} </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;"> {{ $itemBom->size }} </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;"> {{ $itemBom->thread_brand }} </td>
                                                      
                                                      
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">{{ $itemBom->consumption }}</td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">{{ $itemBom->extra_percent }}</td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;"> {{ $itemBom->uom }} </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">{{ $itemBom->bom_term }}</td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                          {{ $itemBom->precost_fob??'0' }}
                                                      </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                          {{ $itemBom->precost_lc??'0' }}
                                                      </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                          {{ $itemBom->precost_freight??'0' }}
                                                      </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                          {{ $itemPrice??'0' }}
                                                      </td>
                                                     
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                       {{ $totalPrice??'0'}}
                                                      </td>


                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                        
                                                          {{ number_format((float)$itemOrdUnitPrice, 6, '.', '') }}
                                                        
                                                      </td>
                                                      <td  style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                          
                                                              {{ number_format((float)$itemBom->style_cost, 6, '.', '') }}
                                                          
                                                      </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                        <p id="perqty-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="text-right fwb totalperqty">0</p>
                                                      </td>
                                                      <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                        <p id="pervalue-{{ $itemBom->mcat_id}}_{{ $itemBom->mr_cat_item_id }}{{ $itemBom->sl }}" class="text-right fwb totalpervalue">0</p>
                                                      </td>
                                                  </tr>
                                                  @endforeach
                                                  <tr class="table-default">
                                                    <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="16">Total {{ $itemCat->mcat_name }} Price</td>
                                                    <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                      {{ $totalPriceItemWise??'0' }}
                                                    </td>
                                                    <td style="font-weight: bold; font-size:11px;border:1px solid black;" class="table-warning">
                                                      <p class="text-right fwb">
                                                        {{ number_format((float)$totalOrdPrice, 6, '.', '') }}
                                                        {{ number_format((float)$itemBom->order_cost, 6, '.', '') }}
                                                      </p>
                                                    </td>
                                                    <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="2"></td>
                                                    <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                                  </tr>


                                                @endif

                                                @php
                                                          if($itemCat->mcat_id == 2 || $itemCat->mcat_id == 3 ){
                                                            $tswingAndFinishing+=$totalPriceItemWise;
                                                          }    
                                                    @endphp

                                            </tbody>
                                            @endforeach
                                            <tbody>
                                              
                                              <tr class="table-default">
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="16"><h5 class="capilize">Total Sewing and Finishing Accessories Price</h5></td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                    {{ $tswingAndFinishing }}
                                                  </td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="2"></td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                              </tr>

                                              @foreach($specialOperation as $spo)
                                              <tr class="table-default">
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="8"><p class="capilize">{{ $spo->opr_name }}</p></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"> 1 </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"> 0 </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  {{$spo->uom??'0'}}
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="4"></td>

                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                 {{ $spo->unit_price??'0' }}
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  <p id="sp-{{ $spo->id }}" class="text-right fwb categoryPrice sp_per_price">{{ number_format((float)($spo->unit_price??'0'), 6,'.','') }}</p>

                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)(isset($ordSPOperation[$spo->id])?($ordSPOperation[$spo->id]->unit_price):'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="2"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                              </tr>
                                              @endforeach
                                              <tr class="table-default">
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="8"><p class="capilize">Testing Cost</p></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"> 1 </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"> 0 </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">Piece</td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="4"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  {{ $otherCosting->testing_cost??'0' }}
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  <p id="testing-cost" class="text-right fwb categoryPrice sp_per_price">{{ number_format((float)($otherCosting->testing_cost??'0'), 6,'.','') }}</p>

                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($ordOthCosting->testing_cost??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="2"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                              </tr>
                                              <tr class="table-default">
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="8"><p class="capilize">CM</p></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"> 1 </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"> 0 </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">Piece</td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="4"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  {{ $otherCosting->cm??'0' }}
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  <p id="cm-cost" class="text-right fwb categoryPrice sp_per_price">{{ number_format((float)($otherCosting->cm??'0'), 6,'.','') }}</p>

                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($ordOthCosting->cm??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="2"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                              </tr>
                                              <tr class="table-default">
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="11"><p class="capilize">Commercial Cost</p></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="4"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  {{ $otherCosting->commercial_cost??'0' }}
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  <p id="commercial-cost" class="text-right fwb categoryPrice sp_per_price">{{ number_format((float)($otherCosting->commercial_cost??'0'), 6,'.','') }}</p>

                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($ordOthCosting->commercial_cost??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="2"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                              </tr>
                                              <tr class="table-default">
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="16"><h5 class="capilize">Net FOB</h5></td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                    {{ $otherCosting->net_fob??'0' }}
                                                  </td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;" class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($ordOthCosting->net_fob??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="2"></td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                              </tr>
                                              <tr class="table-default">
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="8"><h5 class="capilize">Buyer FOB</h5></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  {{ $otherCosting->buyer_comission_percent??'0' }}
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">%</td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="4"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  <input type="text" step="any" min="0" id="buyer-commission-unitprice" class="form-control buyer-commission-unitprice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"  value="0" readonly>
                                                  <input type="hidden" name="buyer_fob" value="0" id="buyer_fob">
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  {{ $otherCosting->net_fob??'0' }}

                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($ordOthCosting->buyer_fob ??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="2"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                              </tr>
                                              <tr class="table-default">
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="8"><h5 class="capilize">Agent FOB</h5></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  {{ $otherCosting->agent_comission_percent??'0' }}
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">%</td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="4"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  <input type="text" step="any" min="0" id="agent-commission-unitprice" class="form-control agent-commission-unitprice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"  value="0" readonly>

                                                  <input type="hidden" step="any" min="0" name="agent_fob" id="agent_fob" class="form-control agent-commission-unitprice" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;"  value="0">
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;">
                                                  {{ $otherCosting->agent_fob??'0' }}

                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" class="table-warning">
                                                  <p class="text-right fwb">
                                                    {{ number_format((float)($ordOthCosting->agent_fob??'0'), 6,'.','') }}
                                                  </p>
                                                </td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="2"></td>
                                                <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                              </tr>

                                              <tr class="table-default">
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="16" class="tsticky-bottom"><h5 class="capilize ">Total FOB</h5></td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;" class="tsticky-bottom">
                                                    {{ $otherCosting->agent_fob??'0' }}
                                                  </td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;" class="tsticky-bottom table-warning">
                                                    <p class="text-right fwb">
                                                      {{ number_format((float)($ordOthCosting->agent_fob??'0'), 6,'.','') }}
                                                    </p>
                                                  </td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;" colspan="2" class="tsticky-bottom"></td>
                                                  <td style="font-weight: bold; font-size:11px;border:1px solid black;"></td>
                                              </tr>
                                            </tbody>

                                        </table>

                                    