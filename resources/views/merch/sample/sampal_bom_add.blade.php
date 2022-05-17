                        <div class="row">
                            <div class="col-md-12">
                                
                                                <!-- Basic Information -->
                                                <div class="panel panel-default printArea bomClass">
                                                    
                                                    <div class="panel-collapse "
                                                         id="basicInfo"  style="">
                                                        <div class=" table-responsive">
                                                           
                                                                
                                                                <br>
                                                                
                                                                <div  id="" style="border-radius:0;">
                                                                    {{-- class="widget-body" --}}
                                                                    <table 
                                                                           class="custom-font-table table table-bordered">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Main Category</th>
                                                                            <th>Item</th>
                                                                          
                                                                            <th>Description</th>
                                                                            <th >Color</th>
                                                                            <th>Size/Width</th>
                                                                            <th >Supplier</th>
                                                                            <th >Article</th>
                                                                            <th >Thread Brand</th>
                                                                           
                                                                            <th >UoM</th>
                                                                            <th>Consumption</th>
                                                                            <th>Extra (%)</th>
                                                                            <th>Extra Qty</th>
                                                                            <th>Total</th>
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
                                                                                <td>{{ $styleCatMcat->mcat_name}}</td>
                                                                                <td>{{ $styleCatMcat->item_name}}</td>
                                                                            
                                                                                <td>{{ $styleCatMcat->item_description}}</td>
                                                                                <td width="80">{{ $styleCatMcat->clr_name}}</td>
                                                                                <td>{{ $styleCatMcat->size}}</td>
                                                                                <td width="80">{{ $styleCatMcat->sup_name}}</td>
                                                                                <td width="80">{{ $styleCatMcat->art_name}}</td>
                                                                                <td>{{ $styleCatMcat->thread_brand}}</td>
                                                                                
                                                                                <td width="80">{{ $styleCatMcat->uom}}</td>
                                                                                <td>{{ $styleCatMcat->consumption}}</td>
                                                                                <td>{{ $styleCatMcat->extra_percent}}</td>
                                                                                <td><?= ($styleCatMcat->consumption * $styleCatMcat->extra_percent) / 100 ?></td>
                                                                            
                                                                                <td><?= $styleCatMcat->extra_percent != 0 ? (($styleCatMcat->consumption * $styleCatMcat->extra_percent) / 100) + $styleCatMcat->consumption: 0  ?></td> 
                                                                            </tr>
                                                                            @endforeach
                                                                        @endforeach
                                                                        <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                 
                                                                    
                                                                </div><!-- /.col -->
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Advance Information -->
                                                
                                                
                                            
                            </div> <!-- END col md 12 -->
                            
                        
                        </div><!-- end row -->