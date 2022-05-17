var base_url = $("#base_url").val();
var i = $('table tr').length;
$(document).on('click', '.add-arrows', function () {
    // check exists empty item
    var lastId = i - 1;
    // console.log(lastId);
    var catid = $(this).data('catid');
    var ordqty = $(this).data('ordqty');
    var gmtqty = catid == 1 ? ordqty : 0;

    var flag = 0;
    var totalRow = $('.items-' + catid).length;

    if (flag === 0) {
        if ($("#blade_type").val() === 'po') {
            html = '<tr id="itemRow_' + catid + '_' + i + '">';
            html += '<td class="right-btn"><a class="btn btn-sm btn-outline-primary arrows-alt" data-toggle="tooltip" data-placement="top" title="" data-original-title="Right Click Action"><i class="las la-arrows-alt"></i></a><div class="context-menu" id="context-menu-file-" style="display:none;position:absolute;z-index:1;"><ul><li><a class="textblack arrows-context add-arrows" data-catid="' + catid + '"><i class="las la-cart-plus"></i> Add Row</a></li><li><a class="textblack arrows-context remove-arrows" data-catid="' + catid + '" ><i class="las la-trash"></i> Remove Row</a></li><li><a class="textblack arrows-context add-new" data-type="item" data-catid="{{ $itemCat->mcat_id }}" id="additem_' + catid + '_' + i + '"><i class="las la-folder-plus"></i> Add New Item</a></li></ul></div></td>';
            html += '<td><input type="hidden" id="bomitemid_' + catid + '_' + i + '" name="bomitemid[]" value=""><input type="hidden" id="itemcatid_' + catid + '_' + i + '" value="' + catid + '" name="itemcatid[]"><input type="hidden" id="itemid_' + catid + '_' + i + '" class="itemid" value="" name="itemid[]">';
            if ($("#blade_type").val() === 'po') {
                html += '<input type="hidden" name="ord_bom_id[]" id="stlbomid_' + catid + '_' + i + '" value="">';
            } else {
                html += '<input type="hidden" name="stl_bom_id[]" id="stlbomid_' + catid + '_' + i + '" value="">';
            }
            html += '<input type="text" data-category="' + catid + '" data-type="item" name="item[]" id="item_' + catid + '_' + i + '" class="form-control autocomplete_txt items_' + catid + '" autocomplete="off" onClick="this.select()"></td>';
            html += '<td><input type="text" data-type="description" name="description[]" id="description_' + catid + '_' + i + '" class="form-control" autocomplete="off"></td>';
            html += '<td><input type="hidden" name="supplierid[]" id="supplierid_' + catid + '_' + i + '"><div class="row m-0"><div class="col-9 p-0"><select name="supplier[]" id="supplier_' + catid + '_' + i + '" data-category="' + catid + '" class="form-control supplier" disabled><option value=""> - Select - </option></select></div></div></td>';
            
            html += '<td><div class="row m-0"><div class="col-9 p-0"><select name="article[]" id="article_' + catid + '_' + i + '" class="form-control articlechange" disabled><option value=""> - Select - </option></select><input type="hidden" class="articleid" name="articleid[]" id="articleid_' + catid + '_' + i + '" value=""></div><div class="col-3 pl-0 pr-0 pt-2"><a class="btn btn-xs btn-primary text-white add-new"  data-type="article" id="addarticle_' + catid + '_' + i + '" data-catid="' + catid + '" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Article"><i class="fa fa-plus"></i></a></div></div></td>';
            html += '<td><select name="color[]" id="color_' + catid + '_' + i + '" class="form-control" data-toggle="tooltip" data-placement="top" title="" data-original-title="this.value"><option value=""> - Select - </option></select></td>';
            html += '<td><input type="text" name="size_width[]" id="sizewidth_' + catid + '_' + i + '" class="form-control" autocomplete="off" ></td>';
            if (catid == 1) {
                html += '<td style="pointer-events:none;"><select readonly  name="threadbrand[]" id="threadbrand_' + catid + '_' + i + '" class="form-control" ><option value=""> - Select - </option> <option value="Astra">Astra</option><option value="Dual Duty">Dual Duty</option><option value="Epic">Epic</option><option value="PPC">PPC</option> </select></td>';
            } else {
                html += '<td><select name="threadbrand[]" id="threadbrand_' + catid + '_' + i + '" class="form-control" ><option value=""> - Select - </option> <option value="Astra">Astra</option><option value="Dual Duty">Dual Duty</option><option value="Epic">Epic</option><option value="PPC">PPC</option> </select></td>';
            }
            // @if($itemBom->mcat_id==1) disabled @else name="threadbrand[]" @endif
            if ($("#blade_type").val() === 'order' || $("#blade_type").val() === 'po') {
                html += '<td><input type="hidden" class="dependsid" name="depends_on[]" id="dependson_' + catid + '_' + i + '" value="0"><div class="custom-control custom-checkbox custom-control-inline"><input type="checkbox" class="custom-control-input" id="dependenciescolor_' + catid + '_' + i + '"><label class="custom-control-label" for="dependenciescolor_' + catid + '_' + i + '">Color</label></div><div class="custom-control custom-checkbox custom-control-inline"><input type="checkbox" class="custom-control-input" id="dependenciessize_' + catid + '_' + i + '"><label class="custom-control-label" for="dependenciessize_' + catid + '_' + i + '">Size</label></div></td>';
            }
            html += '<td><select name="uom[]" id="uom_' + catid + '_' + i + '" class="form-control uomchange" disabled><option value=""> - Select - </option></select><input type="hidden" class="uomname" name="uomname[]" id="uomname_' + catid + '_' + i + '" value=""></td>';
            html += '<td><input type="text" step="any" min="0" value="0" name="consumption[]" id="consumption_' + catid + '_' + i + '" data-category="' + catid + '" class="form-control changesNo action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
            html += '<td><input type="text" step="any" min="0" value="5" data-category="' + catid + '" name="extraper[]" id="extraper_' + catid + '_' + i + '" class="form-control changesNo action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
            html += '<td><input type="text" step="any" min="0" value="0" name="total[]" id="total_' + catid + '_' + i + '" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
            html += '<td><input type="text" step="any" min="0" value="' + gmtqty + '" data-category="' + catid + '" name="garmentqty[]" id="garmentqty_' + catid + '_' + i + '" class="form-control changesNo action-input" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
            html += '<td><input type="text" step="any" min="0" value="0" name="req_qty[]" id="req_qty_' + catid + '_' + i + '" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
            html += '</tr>';
            $(this).parent().parent().parent().parent().parent().after(html);
            // $('#').append(html);
            $('#item_' + catid + '_' + i).focus();
            i++;
        } else if ($("#blade_type").val() === 'style') {
            html = '<tr id="itemRow_' + catid + '_' + i + '">';
            html += '<td class="right-btn"><a class="btn btn-sm btn-outline-primary arrows-alt" data-toggle="tooltip" data-placement="top" title="" data-original-title="Right Click Action"><i class="las la-arrows-alt"></i></a><div class="context-menu" id="context-menu-file-" style="display:none;position:absolute;z-index:1;"><ul><li><a class="textblack arrows-context add-arrows" data-catid="' + catid + '"><i class="las la-cart-plus"></i> Add Row</a></li><li><a class="textblack arrows-context remove-arrows" data-catid="' + catid + '" ><i class="las la-trash"></i> Remove Row</a></li><li><a class="textblack arrows-context add-new" data-type="item" data-catid="{{ $itemCat->mcat_id }}" id="additem_' + catid + '_' + i + '"><i class="las la-folder-plus"></i> Add New Item</a></li></ul></div></td>';
            html += '<td><input type="hidden" id="bomitemid_' + catid + '_' + i + '" name="bomitemid[]" value=""><input type="hidden" id="itemcatid_' + catid + '_' + i + '" value="' + catid + '" name="itemcatid[]"><input type="hidden" id="itemid_' + catid + '_' + i + '" class="itemid" value="" name="itemid[]">';
            if ($("#blade_type").val() === 'po') {
                html += '<input type="hidden" name="ord_bom_id[]" id="stlbomid_' + catid + '_' + i + '" value="">';
            } else {
                html += '<input type="hidden" name="stl_bom_id[]" id="stlbomid_' + catid + '_' + i + '" value="">';
            }
            html += '<input type="text" data-category="' + catid + '" data-type="item" name="item[]" id="item_' + catid + '_' + i + '" class="form-control autocomplete_txt items_' + catid + '" autocomplete="off" onClick="this.select()"></td>';
            html += '<td><input type="text" data-type="description" name="description[]" id="description_' + catid + '_' + i + '" class="form-control" autocomplete="off"></td>';
            html += '<td><input type="hidden" name="supplierid[]" id="supplierid_' + catid + '_' + i + '"><div class="row m-0"><div class="col-9 p-0"><select name="supplier[]" id="supplier_' + catid + '_' + i + '" data-category="' + catid + '" class="form-control supplier" disabled><option value=""> - Select - </option></select></div></div></td>';
            
            html += '<td><div class="row m-0"><div class="col-9 p-0"><select name="article[]" id="article_' + catid + '_' + i + '" class="form-control articlechange" disabled><option value=""> - Select - </option></select><input type="hidden" class="articleid" name="articleid[]" id="articleid_' + catid + '_' + i + '" value=""></div><div class="col-3 pl-0 pr-0 pt-2"><a class="btn btn-xs btn-primary text-white add-new"  data-type="article" id="addarticle_' + catid + '_' + i + '" data-catid="' + catid + '" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Article"><i class="fa fa-plus"></i></a></div></div></td>';
            html += '<td><select name="color[]" id="color_' + catid + '_' + i + '" class="form-control" data-toggle="tooltip" data-placement="top" title="" data-original-title="this.value"><option value=""> - Select - </option></select></td>';
            html += '<td><input type="text" name="size_width[]" id="sizewidth_' + catid + '_' + i + '" class="form-control" autocomplete="off" ></td>';
            if ($("#blade_type").val() === 'order' || $("#blade_type").val() === 'po') {
                html += '<td><input type="hidden" class="dependsid" name="depends_on[]" id="dependson_' + catid + '_' + i + '" value="0"><div class="custom-control custom-checkbox custom-control-inline"><input type="checkbox" class="custom-control-input" id="dependenciescolor_' + catid + '_' + i + '"><label class="custom-control-label" for="dependenciescolor_' + catid + '_' + i + '">Color</label></div><div class="custom-control custom-checkbox custom-control-inline"><input type="checkbox" class="custom-control-input" id="dependenciessize_' + catid + '_' + i + '"><label class="custom-control-label" for="dependenciessize_' + catid + '_' + i + '">Size</label></div></td>';
            }
            
            
            if(catid == 1){
                html += '<td style="pointer-events:none;"><select readonly  name="threadbrand[]" id="threadbrand_'+catid+'_'+i+'" class="form-control" ><option value=""> - Select - </option> <option value="Astra">Astra</option><option value="Dual Duty">Dual Duty</option><option value="Epic">Epic</option><option value="PPC">PPC</option> </select></td>';
            }else{
                html += '<td><select name="threadbrand[]" id="threadbrand_'+catid+'_'+i+'" class="form-control" ><option value=""> - Select - </option> <option value="Astra">Astra</option><option value="Dual Duty">Dual Duty</option><option value="Epic">Epic</option><option value="PPC">PPC</option> </select></td>';
            }
            html += '<td><select name="uom[]" id="uom_' + catid + '_' + i + '" class="form-control uomchange" disabled><option value=""> - Select - </option></select><input type="hidden" class="uomname" name="uomname[]" id="uomname_' + catid + '_' + i + '" value=""></td>';
            html += '<td><input type="text" step="any" min="0" value="0" name="consumption[]" id="consumption_' + catid + '_' + i + '" data-category="' + catid + '" class="form-control changesNo action-input consumption" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
            html += '<td><input type="text" step="any" min="0" value="5" data-category="' + catid + '" name="extraper[]" id="extraper_' + catid + '_' + i + '" class="form-control changesNo action-input extraper" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';

            if ($("#blade_type").val() === 'order' || $("#blade_type").val() === 'po') {
                html += '<td><input type="text" step="any" min="0" value="0" name="totalconsumption[]" id="totalconsumption_' + catid + '_' + i + '" class="form-control changesNo totalconsumption" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" readonly></td>';
                html += '<td><input type="text" step="any" min="0" value="0" name="garments_qty[]" id="garments_qty_' + catid + '_' + i + '" data-category="' + catid + '" class="form-control changesNo action-input garments_qty" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
                if (catid == 1) {
                    html += '<td><input type="text" step="any" min="0" value="0" name="requiredqty[]" id="requiredqty_' + catid + '_' + i + '" autocomplete="off" class=" form-control requiredqty" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
                } else {
                    html += '<td><input type="text" step="any" min="0" value="0" name="requiredqty[]" id="requiredqty_' + catid + '_' + i + '" autocomplete="off" class=" form-control garments_requiredqty" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
                }
            }

            if ($("#blade_type").val() === 'style') {
                html += '<td><input type="text" step="any" min="0" value="0" name="extraqty[]" id="extraqty_' + catid + '_' + i + '" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" readonly></td>';
                html += '<td><input type="text" step="any" min="0" value="0" name="total[]" id="total_' + catid + '_' + i + '" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
            }
            html += '</tr>';
            $(this).parent().parent().parent().parent().parent().after(html);
            // $('#').append(html);
            $('#item_' + catid + '_' + i).focus();
            i++;
        } else if ($("#blade_type").val() === 'order') {
            html = '<tr id="itemRow_' + catid + '_' + i + '">';
            html += '<td class="right-btn"><a class="btn btn-sm btn-outline-primary arrows-alt" data-toggle="tooltip" data-placement="top" title="" data-original-title="Right Click Action"><i class="las la-arrows-alt"></i></a><div class="context-menu" id="context-menu-file-" style="display:none;position:absolute;z-index:1;"><ul><li><a class="textblack arrows-context add-arrows" data-catid="' + catid + '" data-ordqty="' + ordqty + '"><i class="las la-cart-plus"></i> Add Row</a></li><li><a class="textblack arrows-context remove-arrows" data-catid="' + catid + '" ><i class="las la-trash"></i> Remove Row</a></li><li><a class="textblack arrows-context add-new" data-type="item" data-catid="{{ $itemCat->mcat_id }}" id="additem_' + catid + '_' + i + '"><i class="las la-folder-plus"></i> Add New Item</a></li></ul></div></td>';
            html += '<td><input type="hidden" id="bomitemid_' + catid + '_' + i + '" name="bomitemid[]" value=""><input type="hidden" id="itemcatid_' + catid + '_' + i + '" value="' + catid + '" name="itemcatid[]"><input type="hidden" id="itemid_' + catid + '_' + i + '" class="itemid" value="" name="itemid[]">';
            if ($("#blade_type").val() === 'po') {
                html += '<input type="hidden" name="ord_bom_id[]" id="stlbomid_' + catid + '_' + i + '" value="">';
            } else {
                html += '<input type="hidden" name="stl_bom_id[]" id="stlbomid_' + catid + '_' + i + '" value="">';
            }
            html += '<input type="text" data-category="' + catid + '" data-type="item" name="item[]" id="item_' + catid + '_' + i + '" class="form-control autocomplete_txt items_' + catid + '" autocomplete="off" onClick="this.select()"></td>';
            html += '<td><input type="text" data-type="description" name="description[]" id="description_' + catid + '_' + i + '" class="form-control" autocomplete="off"></td>';
            html += '<td><input type="hidden" name="supplierid[]" id="supplierid_' + catid + '_' + i + '"><div class="row m-0"><div class="col-9 p-0"><select name="supplier[]" id="supplier_' + catid + '_' + i + '" data-category="' + catid + '" class="form-control supplier" disabled><option value=""> - Select - </option></select></div></div></td>';
            html += '<td><div class="row m-0"><div class="col-9 p-0"><select name="article[]" id="article_' + catid + '_' + i + '" class="form-control articlechange" disabled><option value=""> - Select - </option></select><input type="hidden" class="articleid" name="articleid[]" id="articleid_' + catid + '_' + i + '" value=""></div><div class="col-3 pl-0 pr-0 pt-2"><a class="btn btn-xs btn-primary text-white add-new"  data-type="article" id="addarticle_' + catid + '_' + i + '" data-catid="' + catid + '" data-toggle="tooltip" data-placement="top" title="" data-original-title="Add New Article"><i class="fa fa-plus"></i></a></div></div></td>';
            html += '<td><select name="color[]" id="color_' + catid + '_' + i + '" class="form-control" data-toggle="tooltip" data-placement="top" title="" data-original-title="this.value"><option value=""> - Select - </option></select></td>';
            html += '<td><input type="text" name="size_width[]" id="sizewidth_' + catid + '_' + i + '" class="form-control" autocomplete="off" ></td>';
            if ($("#blade_type").val() === 'order' && catid == 1) {
                html += '<td><select type="text" name="thread_brand[]" disabled id="thread_brand_' + catid + '_' + i + '" class="form-control" autocomplete="off" ><option value="" > - Select -</option><option value="Astra">Astra</option><option value="Dual Duty">Dual Duty</option><option value="Epic">Epic</option><option value="PPC">PPC</option></select></td>';
            } else {
                html += '<td><select type="text" name="thread_brand[]" id="thread_brand_' + catid + '_' + i + '" class="form-control" autocomplete="off" ><option value="" > - Select -</option><option value="Astra">Astra</option><option value="Dual Duty">Dual Duty</option><option value="Epic">Epic</option><option value="PPC">PPC</option></select></td>';
            }
            if ($("#blade_type").val() === 'order' || $("#blade_type").val() === 'po') {
                html += '<td><input type="hidden" class="dependsid" name="depends_on[]" id="dependson_' + catid + '_' + i + '" value="0"><div class="custom-control custom-checkbox custom-control-inline"><input type="checkbox" class="custom-control-input" id="dependenciescolor_' + catid + '_' + i + '"><label class="custom-control-label" for="dependenciescolor_' + catid + '_' + i + '">Color</label></div><div class="custom-control custom-checkbox custom-control-inline"><input type="checkbox" class="custom-control-input" id="dependenciessize_' + catid + '_' + i + '"><label class="custom-control-label" for="dependenciessize_' + catid + '_' + i + '">Size</label></div></td>';
            }


            html += '<td><select name="uom[]" id="uom_' + catid + '_' + i + '" class="form-control uomchange" disabled><option value=""> - Select - </option></select><input type="hidden" class="uomname" name="uomname[]" id="uomname_' + catid + '_' + i + '" value=""></td>';
            html += '<td><input type="text" step="any" min="0" value="0" name="consumption[]" id="consumption_' + catid + '_' + i + '" data-category="' + catid + '" class="form-control changesNo action-input consumption" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
            html += '<td><input type="text" step="any" min="0" value="5" data-category="' + catid + '" name="extraper[]" id="extraper_' + catid + '_' + i + '" class="form-control changesNo action-input extraper" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';

            if ($("#blade_type").val() === 'order' || $("#blade_type").val() === 'po') {
                html += '<td><input type="text" step="any" min="0" value="0" name="totalconsumption[]" id="totalconsumption_' + catid + '_' + i + '" class="form-control changesNo totalconsumption" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" readonly></td>';
                html += '<td><input type="text" step="any" min="0" value="'+ gmtqty +'" name="garments_qty[]" id="garments_qty_' + catid + '_' + i + '" data-category="' + catid + '" class="form-control changesNo action-input garments_qty" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()"></td>';
                html += '<td><input type="text" step="any" min="0" value="0" name="requiredqty[]" id="requiredqty_' + catid + '_' + i + '" autocomplete="off" class=" form-control garments_requiredqty" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
            }

            if ($("#blade_type").val() === 'style') {
                html += '<td><input type="text" step="any" min="0" value="0" name="extraqty[]" id="extraqty_' + catid + '_' + i + '" class="form-control changesNo" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" onClick="this.select()" readonly></td>';
                html += '<td><input type="text" step="any" min="0" value="0" name="total[]" id="total_' + catid + '_' + i + '" class="form-control" autocomplete="off" onkeypress="return IsNumeric(event);" ondrop="return false;" onpaste="return false;" readonly></td>';
            }
            html += '</tr>';
            $(this).parent().parent().parent().parent().parent().after(html);
            // $('#').append(html);
            $('#item_' + catid + '_' + i).focus();
            i++;
        } else {
            $('#item_' + catid + '_' + i).focus();
        }
    }

});

if ($("#blade_type").val() === 'order' || $("#blade_type").val() === 'po') {

    $(document).on('keyup', '.changesNo', function () {
        var index = $(this).parent().parent();
        var consumption = index.find('.consumption').val();
        var extraper = parseFloat(index.find(".extraper").val());
        var orderQty = $('.orderQty').val();
        var garments_qty = index.find('.garments_qty').val();


        consumption = (isNaN(consumption) || consumption == '') ? '0' : consumption;
        extraper = (isNaN(extraper) || garments_qty == '') ? '0' : extraper;

        var extraqty = parseFloat((parseFloat(consumption) * parseFloat(extraper)) / 100)

        var totalconsumption = parseFloat(consumption) + parseFloat(extraqty);

        garments_qty = (isNaN(garments_qty) || garments_qty == '') ? '0' : garments_qty;
        totalconsumption = (isNaN(totalconsumption) || totalconsumption == '') ? '0' : totalconsumption;

        var garments_requiredqty = parseFloat(Number(parseFloat(garments_qty)) * Number(parseFloat(totalconsumption))).toFixed(2);
        var requiredqty = parseFloat(totalconsumption * orderQty).toFixed(2);


        index.find(".garments_requiredqty").val(garments_requiredqty);
        index.find(".requiredqty").val(requiredqty);
        if ($("#blade_type").val() === 'po') {
            index.find(".extraqty").val(extraqty);
        } else {
            index.find(".totalconsumption").val(totalconsumption);
        }
    });
}

// supplier
$(document).on('change', '.supplier', function () {
    var id_arr = $(this).attr('id'),
        id = id_arr.split("_");
    $('#article_' + id[1] + '_' + id[2]).empty().select2({
        data: [{
            id: '',
            text: ' Select Article'
        }]
    }).attr('disabled', true);
    if ($(this).val() !== '') {
        var supid = $(this).val();
        $.ajax({
            type: "GET",
            url: base_url + '/merch/search/ajax-supplier-article-search',
            data: {
                mr_supplier_sup_id: $(this).val()
            },
            success: function (response) {
                if (response !== '') {

                    $('#supplierid_' + id[1] + '_' + id[2]).val(supid);
                    $('#article_' + id[1] + '_' + id[2]).select2({
                        data: response
                    }).removeAttr('disabled');
                }
            },
            error: function (reject) {
                console.log(reject);
            }
        });
    }
});


//auto-complete script
$(document).on('focus keyup', '.autocomplete_txt', function () {
    type = $(this).data('type');
    typeId = $(this).attr('id');
    itemCat = $(this).data('category');
    name = $(this).val();
    // console.log(itemCat);
    inputIdSplit = typeId.split("_");

    if (type == 'item') autoTypeNo = 0;

    $(this).autocomplete({
        source: function (request, response) {
            $.ajax({
                url: base_url + '/merch/search/ajax-item-search',
                //dataType: "json",
                method: 'get',
                data: {
                    keyvalue: request.term,
                    type: type,
                    category: itemCat
                },
                success: function (data) {

                    response($.map(data.items, function (item) {
                        if (item.item_name !== '') {
                            if (type == 'item') autoTypeShow = item.item_name;
                            return {
                                // label: autoTypeShow + ' - ' + item.item_code,
                                label: autoTypeShow ,
                                value: autoTypeShow,
                                data: item,
                                supplier: data.supplier
                            }
                        } else {
                            return {
                                label: item.item_code,
                                value: ' ',
                                data: '',
                                supplier: ''
                            }
                        }
                    }));
                }
            });
        },
        autoFocus: true,
        minLength: 0,
        select: function (event, ui) {
            var item = ui.item.data;
            var supplier = ui.item.supplier;
            //var uomList = ui.data.uomList
            // console.log(item);
            // console.log(supplier);
            id_arr = $(this).attr('id');
            id = id_arr.split("_");
            $('#supplier_' + id[1] + '_' + id[2]).empty().select2({
                data: [{
                    id: '',
                    text: ' Select Supplier'
                }]
            }).attr('disabled', true);
            $('#article_' + id[1] + '_' + id[2]).empty().select2({
                data: [{
                    id: '',
                    text: ' Select Article'
                }]
            }).attr('disabled', true);
            $('#uom_' + id[1] + '_' + id[2]).empty().select2({
                data: [{
                    id: '',
                    text: ' Select UOM'
                }]
            }).attr('disabled', true);
            // $('#threadbrand_'+id[1]+'_'+id[2]).empty().select2({data: [{id: '', text: ' Select UOM'}]}).attr('disabled', true);
            if (item !== '') {

                $('#itemid_' + item.mcat_id + '_' + id[2]).val(item.id);
                if ($("#blade_type").val() === 'order' || $("#blade_type").val() === 'po') {
                    $('#dependson_' + item.mcat_id + '_' + id[2]).val(item.dependent_on);
                    dependsOnCheck('dependson_' + item.mcat_id + '_' + id[2]);
                }
                $('#color_' + item.mcat_id + '_' + id[2]).select2({
                    data: getColor
                }).removeAttr('disabled');

                $('#uom_' + item.mcat_id + '_' + id[2]).select2({
                    //data: item.uom
                    data: uomList
                }).removeAttr('disabled');

                $('#supplier_' + item.mcat_id + '_' + id[2]).select2({
                    data: supplier
                }).removeAttr('disabled');
                setTimeout(function () {
                    // saveBOM('added');
                }, 500);
                setTimeout(function () {
                    $('#description_' + item.mcat_id + '_' + id[2]).focus().select();
                }, 100);
                var nextinput = $('#itemid_' + item.mcat_id + '_' + id[2]).closest('tr').next().find('td:eq(' + $('#itemid_' + item.mcat_id + '_' + id[2]).closest('td').index() + ')').find('input')[0];
                if (nextinput === undefined) {
                    $(this).parent().parent().find('.right-btn .add-arrows').click();
                }

            }

        }
    });
});

//It restrict the non-numbers
var specialKeys = new Array();
specialKeys.push(8, 46); //Backspace
function IsNumeric(e) {
    var keyCode = e.which ? e.which : e.keyCode;
    //console.log( keyCode );
    var ret = ((keyCode >= 48 && keyCode <= 57) || specialKeys.indexOf(keyCode) != -1);
    return ret;
}

$(document).on('click', '.remove-arrows', function () {
    var isGood = confirm('Are you sure you want to remove this row?');
    if (isGood) {
        $(this).parent().parent().parent().parent().parent().remove();
        // saveBOM('remove');
    }
    // $(this).parent().parent().parent().parent().parent().remove();
})
$(document).on("contextmenu", ".right-btn", function (e) {
    // Show context menu
    $(".context-menu").hide();
    $(this).parent().find('.context-menu').toggle(100).css({
        display: "block",
        left: "15px"
    });

    // disable default context menu
    return false;
});

// Hide context menu
$(document).bind('contextmenu click', function () {
    $(".context-menu").hide();
});

// $(document).on('keyup', 'input, select', function(e) {
//     if (e.which == 39) { // right arrow
//       $(this).closest('td').next().find('input, select').focus().select();
//     } else if (e.which == 37) { // left arrow
//       $(this).closest('td').prev().find('input, select').focus().select();
//     } else if (e.which == 40) { // down arrow
//       $(this).closest('tr').next().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus().select();
//     } else if (e.which == 38) { // up arrow
//       $(this).closest('tr').prev().find('td:eq(' + $(this).closest('td').index() + ')').find('input').focus().select();
//     }
// });

$(document).on('keypress', function (e) {
    var that = document.activeElement;
    if (e.which == 13) {
        if ($(document.activeElement).attr('type') == 'submit') {
            return true;
        } else {
            e.preventDefault();
        }
    }
});
$(function () {
    $(".xyz-body").sortable({
        items: 'tr:not(tr:first-child)',
        cursor: 'pointer',
        axis: 'y',
        dropOnEmpty: false,
        start: function (e, ui) {
            ui.item.addClass("selected");
        },
        stop: function (e, ui) {
            ui.item.removeClass("selected");

            $(this).find("tr").each(function (index) {
                if (index > 0) {
                    // $(this).find("td").eq(2).html(index);
                }
            });
            // saveBOM('move');
        }
    });
});

var loaderContent = '<div class="animationLoading"><div id="container-loader"><div id="one"></div><div id="two"></div><div id="three"></div></div><div id="four"></div><div id="five"></div><div id="six"></div></div>';
$(document).on('click', '.add-new', function () {
    itemid = $(this).attr('id');
    i_id = itemid.split("_");
    type = $(this).data('type');
    typeCat = $(this).data('catid');
    $("#itemForm").hide();
    $('#right_modal_item').modal('show');
    $('#modal-title-right').html(' <i class="fa fa-plus"></i> Add New ' + type);

    $("#content-result").html(loaderContent);
    var data = {};
    if (type === 'article') {
        data = {
            type: type,
            item_category: typeCat,
            index: i_id[2],
            supplierid: $("#supplierid_" + i_id[1] + '_' + i_id[2]).val()
        }
    } else {
        data = {
            type: type,
            item_category: typeCat,
            index: i_id[2]
        }
    }
    $.ajax({
        type: "GET",
        url: base_url + '/merch/page-content-load',
        data: data,
        success: function (response) {
            // console.log(response)
            if (response !== 'error') {
                setTimeout(function () {
                    $("#content-result").html(response);
                    if (type === 'item') {
                        $('#uom-item').select2({
                            dropdownParent: $('#right_modal_item')
                        });
                    } else if (type === 'supplier') {
                        $('#country_id').select2({
                            dropdownParent: $('#right_modal_item')
                        });
                    } else {
                        $('#supplier').select2({
                            dropdownParent: $('#right_modal_item')
                        });
                    }


                }, 1000);

            } else {
                $.notify('Something Error! Please try again');
            }
        },
        error: function (reject) {
            console.log(reject);
        }
    });

});

$("body").on("keyup blur", ".changesNo", function () {
    changesNo($(this));
});

$("body").on("change", ".changesNo", function () {
    $("#change-flag").val('1');
});

function changesNo(e) {
    conid = e.attr('id');
    coid = conid.split("_");
    var consumption = $('#consumption_' + coid[1] + '_' + coid[2]).val();
    var extra = $('#extraper_' + coid[1] + '_' + coid[2]).val();
    var garment = $('#garmentqty_' + coid[1] + '_' + coid[2]).val();
    consumption = (consumption === '' ? 0 : consumption);
    extra = (extra === null ? 0 : extra);
    garment = (garment === null ? 0 : garment);
    var qty = parseFloat(((parseFloat(consumption) / 100) * parseFloat(extra))).toFixed(4);
    var total = (parseFloat(qty) + parseFloat(consumption)).toFixed(4);
    var requardqty = Math.ceil(total * garment);
    $('#extraqty_' + coid[1] + '_' + coid[2]).val(qty);
    $('#total_' + coid[1] + '_' + coid[2]).val(total);
    $('#req_qty_' + coid[1] + '_' + coid[2]).val(requardqty);
}


// article
$(document).on('change', '.articlechange', function () {
    var article = $(this).val();
    $(this).parent().find('.articleid').val(article);
});

// uom
$(document).on('change', '.uomchange', function () {
    var uom = $(this).val();
    $(this).parent().find('.uomname').val(uom);
});

// // thrade
// $(document).on('change', '.threadbrandchange', function(){
//     var uom = $(this).val();
//     $(this).parent().find('.threadbrand').val(threadbrand);
// });

$(document).on('click', '#itemBtn', function () {
    $("#app-loader").show();
    var curStep = jQuery(this).closest("#itemForm"),
        curInputs = curStep.find("input[type='text'],input[type='email'],input[type='hidden'],input[type='url'],input[type='date'],input[type='checkbox'],input[type='radio'],textarea,select"),
        isValid = true;
    var cati = $("#mcat_id").val();
    var itIndex = $("#item-index").val();
    var itemNa = $('#item_name').val();
    var clickType = $('#click-type').val();
    $(".form-group").removeClass("has-error");

    for (var i = 0; i < curInputs.length; i++) {
        if (!curInputs[i].validity.valid) {
            isValid = false;
            $(curInputs[i]).closest(".form-group").addClass("has-error");
        }
    }
    var url = base_url;
    if (clickType === 'item') {
        url += '/merch/setup/item_store_ajax';
    } else if (clickType === 'supplier') {
        url += '/merch/setup/ajax_save_supplier';
    } else if (clickType === 'article') {
        url += '/merch/setup/ajax_save_article';
    }

    if (isValid) {
        $.ajax({
            type: "POST",
            url: url,
            data: curInputs.serialize(), // serializes the form's elements.
            success: function (response) {
                $("#app-loader").hide();
                // console.log(response)
                $.notify(response.message, response.type);
                if (response.type === 'success') {
                    if (clickType === 'item') {
                        // $("#item_"+cati+'_'+itIndex).val(itemNa);
                    } else if (clickType === 'supplier') {
                        $("#supplierid_" + cati + '_' + itIndex).val(response.value.id);
                        // $('#supplier'+cati+'_'+itIndex).select2([{id: response.value.id, text: response.value.sup_name}]).trigger('change');
                        // var mySelect = $('#supplier'+cati+'_'+itIndex).append('<option value="104">Delhi</option>');
                        // mySelect.trigger("change");
                        // saveBOM('save');
                        setTimeout(function () {
                            window.location.href = response.url;
                        }, 500);

                    } else if (clickType === 'article') {
                        $("#articleid_" + cati + '_' + itIndex).val(response.value.id);
                        // saveBOM('save');
                        setTimeout(function () {
                            window.location.href = response.url;
                        }, 500);
                    }
                    setTimeout(function () {
                        $('.close').click();
                    }, 500);
                }
            },
            error: function (reject) {
                $("#app-loader").hide();
                if (reject.status === 400) {
                    var data = $.parseJSON(reject.responseText);
                    $.notify(data.message, data.type);
                } else if (reject.status === 422) {
                    var data = $.parseJSON(reject.responseText);
                    var errors = data.errors;
                    // console.log(errors);
                    for (var key in errors) {
                        var value = errors[key];
                        $.notify(value[0], 'error');
                    }

                }
            }
        });
    } else {
        $("#app-loader").hide();
        $.notify("Some field are required", 'error');
    }
});

$(document).on('change', '#country_id', function () {
    var countryName = $("#country_id option:selected").text();
    if (countryName === 'Bangladesh') {
        $('.local').prop('checked', true);
    } else if (countryName == "") {
        $('.local').prop('checked', false);
        $('.foreign').prop('checked', false);
    } else {
        $('.foreign').prop('checked', true);
    }
});

var sd = 1;
$(document).on('click', '.AddBtn_bu', function () {
    html = '<div class="row"><div class="col-10 pr-0"><div class="form-group has-float-label">';
    html += '<input type="text" id="contact' + sd + '" name="scp_details[]" placeholder="Enter Contact Person (Name, Cell No, Email)" class="form-control scp_details"/>';
    html += '<label for="contact' + sd + '"> Contact Person </label></div></div><div class="col-2">';
    html += '<button type="button" class="btn btn-sm btn-outline-danger RemoveBtn_bu">-</button></div></div>';

    $('#addAddress').append(html);
    $('#contact' + i).focus();
    i++;
});

$(document).on('click', '.RemoveBtn_bu', function () {
    $(this).parent().parent().remove();
});

// auto save
/*
$(document).on('blur','.changesNo',function(){
    if($("#change-flag").val() === '1'){
        $("#change-flag").val('0');
        setTimeout(function(){
            saveBOM('cost');
        }, 600)
    }
});
*/


// setInterval(function () {
//         var today = new Date();
//         var time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
//         console.log('Save: '+ time)
//     saveBOM('cost');
// }, 60000);

$(document).ready(function () {
    $(".dependsid").each(function () {
        dependsOnCheck($(this).attr('id'));
    });
});

function dependsOnCheck(thisid) {

    deidsp = thisid.split("_");
    if (deidsp.length > 2) {
        var deicheckid = deidsp[2],
            deicheckcid = deidsp[1];
        var checkdep = $("#" + thisid).val();
        checkdep = parseInt(checkdep);
        if (checkdep === 3 || checkdep === 2) {
            //size
            $('#dependenciessize_' + deicheckcid + '_' + deicheckid).prop("checked", true).trigger("change");
        }

        if (checkdep === 3 || checkdep === 1) {
            //color
            $('#dependenciescolor_' + deicheckcid + '_' + deicheckid).prop("checked", true).trigger("change");
        }

    }
}

$(document).on('change', '.depends_on:checkbox', function () {
    var cid = $(this).attr('id');
    cidsp = cid.split("_");
    if (cidsp.length > 2) {
        var depedid = cidsp[2],
            depedcid = cidsp[1];
        var sizeflag = 0, colorflag = 0, dependsOn = 0;
        // check size
        if ($('#dependenciessize_' + depedcid + '_' + depedid).is(":checked")) {
            sizeflag = 1;
        }

        // check color
        if ($('#dependenciescolor_' + depedcid + '_' + depedid).is(":checked")) {
            colorflag = 1;
        }

        if (sizeflag === 1 && colorflag === 1) {
            dependsOn = 3;
        } else {
            if (sizeflag === 1) {
                dependsOn = 2;
            }

            if (colorflag === 1) {
                dependsOn = 1;
            }
        }

        $("#dependson_" + depedcid + '_' + depedid).val(dependsOn);
        // setTimeout(function(){
        //     saveBOM('depends');
        // }, 600)
    }
});

$(document).on('contextmenu', 'input', function (event) {
    return false;
});

$(document).on('contextmenu', '.action-input', function (event) {
    $(".calc-wrapper").removeClass('out-of-network');

    var selectid = $(this).attr('id');
    $("#cal-input").val(selectid);
    return false;
});
$(document).on('click', '.close-cal', function (event) {
    $(".calc-wrapper").addClass('out-of-network');
    $(".calc-brown").click();
    $("#cal-input").val('');
});
$(document).on('click', '.ok-cal', function (event) {
    $(".calc-wrapper").addClass('out-of-network');
    var selectedid = $("#cal-input").val();
    var inputval = $(".calc-display span").html();
    var inputval = parseFloat(inputval).toFixed(6);
    inputval = (isNaN(inputval) || inputval == '') ? '0' : inputval;
    $('#' + selectedid).val(inputval);
    changesNo($('#' + selectedid));
    $("#cal-input").val('');
});
