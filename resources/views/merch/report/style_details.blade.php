@extends('merch.layout')
@section('title', 'Style Details')
@section('main-content')
@push('css')
<style type="text/css">
	#dataTables tr td:nth-child(2){
		display: block;
	    width: 70px !important;
	}
	#dataTables tr th:nth-child(3) input{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(6) input{
		width: 70px !important;
	}
	#dataTables tr th:nth-child(7) input{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(8) input{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(9) select{
		width: 70px !important;
	}
	#dataTables tr th:nth-child(10) input{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(11) select{
		width: 70px !important;
	}
	#dataTables tr th:nth-child(12) select{
		width: 60px !important;
	}
	#dataTables tr th:nth-child(13) select{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(14) select{
		width: 120px !important;
	}
	#dataTables tr th:nth-child(15) select{
		width: 180px !important;
	}
	#dataTables tr th:nth-child(16) select{
		width: 80px !important;
	}
	#dataTables tr th:nth-child(17) select{
		width: 60px !important;
	}
	#dataTables tr th:nth-child(19) select{
		width: 60px !important;
	}
	#dataTables tr th:nth-child(18) input{
		width: 100px !important;
	}
	#dataTables tr th:nth-child(20) input{
		width: 50px !important;
	}
	#dataTables tr th:nth-child(21) input{
		width: 150px !important;
	}

    
</style>
@endpush
<div class="main-content">
	<div class="main-content-inner">
		<div class="breadcrumbs ace-save-state" id="breadcrumbs">
			<ul class="breadcrumb">
				<li>
          <i class="ace-icon fa fa-home home-icon"></i>
          <a href="#">Merchandising</a>
      </li>
      <li>
          <a href="#">Style</a>
      </li>
      <li class="active">Style Details</li>
      
			</ul><!-- /.breadcrumb -->




		</div>

        @include('inc/message')
		<div class="page-content">
 			<div class="panel ">
                
                <div class="panel-body pb-0">
			 <!-- Display Erro/Success Message -->
					<form class="mb-2" role="form" id="empFilter" method="get" action="#">
						<div class="row">

	                        <div class="col-3">
	                            <div class="form-group has-float-label has-required select-search-group">
	                                {{ Form::select('buyer', $buyerList, null, ['placeholder'=>'Select Buyer', 'id'=>'buyer',  'class'=>'form-control']) }}
	                                <label  for="buyer"> Buyer </label>
	                            </div>
	                        </div>

	                        <div class="col-3">
                            <div class="form-group has-float-label select-search-group">
                                {{ Form::select('season', $seasonList, null, ['placeholder'=>'Select Season', 'id'=>'season',  'class'=>'form-control']) }}
                                <label  for="season"> Season </label>
                            </div>
                        </div>

	                        <div class="col-3">
	                            <div class="form-group has-float-label select-search-group">
	                                {{ Form::select('productType', $productTypeList, null, ['placeholder'=>'Select Product Type', 'id'=>'productType',  'class'=>'form-control']) }}
	                                <label  for="productType"> Product Type </label>
	                            </div>
	                        </div>
                            <div class="col-3">

								<div class="form-group has-float-label select-search-group">
									<select name="gender" class="form-control capitalize select-search" id="gender">
										<option selected="" value="">Choose...</option>
										
										<option value="Male">Male</option>
										<option value="Female">Female</option>
										
									</select>
									<label for="gender">Gender</label>
								</div>
                            
							</div>
                          
	                        
	                        
	                        
	                </div>
	                    
	                


		            </form>
		        </div>
		    </div>
		    

			
			<div class="panel">
				<div class="panel-heading"><h6>Style Details</h6></div> 
                <div class="panel-body">
                  <div class="style_section">
                    <div id="printDiv" class="col-xs-12 table-responsive worker-list">
                        <table id="example" class="table table-striped table-bordered table-head" style="display: block;overflow-x: auto;width: 100%;" border="1">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Buyer</th>
									<th>Style Reference 1</th>
									<th>Style Description</th>
									<th>Style Reference 2</th>
									<th>Season</th>
                                    <th>Brand</th>
									<th>Product Type</th>
									<th>Germent Type</th>
									<th>Gender</th>
									<th>Sample Type</th>
									<th>Operation</th>
									<th>Special Machine</th>
                                    <th>Sewing SMV</th>
									<th>Image</th>
                            </thead>

                        </table>
                    </div><!-- /.col -->
                  </div>
                </div>
              </div>

		</div><!-- /.page-content -->
	</div>
</div>

{{-- 	include summary --}}

@push('js')
<script type="text/javascript">

$(document).ready(function(){ 
    var searchable = [2,3,4,6,7,8];
    var selectable = [];
    var exportColName = ['Sl','Buyer','Style Reference 1', 'Style Description', 'Style Reference 2','Season','Brand','Product Type','Garment Type','Gender', 'Sample Type','Operation','Special Machine','SMV'];
    var dropdownList = {

        // '5' :[@foreach($buyerList as $e) <?php echo "\"$e\"," ?> @endforeach],
        // '5' :[@foreach($seasonList as $e) <?php echo "\"$e\"," ?> @endforeach],
        // '5' :[@foreach($seasonList as $e) <?php echo "'$e'," ?> @endforeach]
    };
    var exportCol = [0,1,2,3,4,5,6,7,8,9,11,12,13];
    var dt = $('#example').DataTable({
          order: [], //reset auto order
          processing: true,
          language: {
              processing: '<i class="fa fa-spinner fa-spin orange bigger-500" style="font-size:60px;margin-top:50px;z-index:100;"></i>'
          },
          responsive: true,
          serverSide: true,
          pagingType: "full_numbers", 
          dom: "lBftrip",
          ajax: {
               url: '{!! url("merch/reports/style_details_data") !!}',
               type: "get",
			   data: function (d) {
	                d.gender  = $('#gender').val(),
					d.buyer  = $('#buyer').val(),
					d.season  = $('#season').val(),
					d.productType  = $('#productType').val()
	                
	            },
               headers: {
                     'X-CSRF-TOKEN': '{{ csrf_token() }}'
               } 
          }, 
          
          
          columns: [  
                { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                {data: 'b_name', name: 'b_name'},
				{data: 'stl_no', name: 'stl_no'},
				{data: 'stl_description',  name: 'stl_description'},
				{data: 'stl_product_name',  name: 'stl_product_name'},
                
                
                
                {data: 'se_name', name: 'se_name'},
                
                {data: 'br_name', name: 'br_name'},
				{data: 'prd_type_name',  name: 'prd_type_name'},
				{data: 'gmt_name',  name: 'gmt_name'},
				{data: 'gender',  name: 'gender'},
				{data: 'sample',  name: 'sample'},
				{data: 'operation',  name: 'Operation'},
				{data: 'spmachine_name',  name: 'spmachine_name'},
				
                
                {data: 'stl_smv', name: 'stl_smv'},
               
                
				{data: 'stl_img_link', name: 'stl_img_link'}
            ],
            buttons: [   
              {
                  extend: 'csv', 
                  className: 'btn btn-sm btn-success',
                  title: 'Style Details',
                  header: true,
                  footer: false,
                  exportOptions: {
                      columns: [0,1,2,3,4,5,6,7,8,9,13],
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                  },
                  "action": allExport,
                  messageTop: ''
              }, 
              {
                  extend: 'excel', 
                  className: 'btn btn-sm btn-warning',
                  title: 'Style Details',
                  header: true,
                  footer: false,
                  exportOptions: {
                      columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                  },
                  "action": allExport,
                  messageTop: ''
              }, 
            //   {
            //       extend: 'pdfHtml5', 
            //       className: 'btn btn-sm btn-primary', 
            //       title: 'Style Details',
            //       header: true,
            //       footer: false,
            //       exportOptions: {
            //           columns: exportCol,
            //           format: {
            //               header: function ( data, columnIdx ) {
            //                   return exportColName[columnIdx];
            //               }
            //           }
                      
            //       },
            //       orientation: 'landscape',
            //   pageSize: "LEGAL",
            //       "action": allExport,
            //       messageTop: ''
            //   }, 

            // { extend: 'print',text: '<i class="fa fa-plus" aria-hidden="true"><span class="{{ App::isLocale('ar')? 'font-ar' : '' }}">@lang('master.print')</span></i>',
            //             title: '@lang('invoices.products')',
                        
            //             messageTop: '{{ Auth::user()->name }}',
            //             messageTop: '{{ Auth::user()->name }}',
            //             className: 'btn btn-default',
            //             autoPrint: true,
 
            //             customize: function (win) {
            //                 $(win.document.body).find('th').addClass('display').css('text-align', 'center');
            //                 $(win.document.body).find('table').addClass('display').css('font-size', '16px');
            //                 $(win.document.body).find('table').addClass('display').css('text-align', 'center');
            //                 $(win.document.body).find('tr:nth-child(odd) td').each(function (index) {
            //                     $(this).css('background-color', '#D0D0D0');
            //                 });
            //                 $(win.document.body).find('h1').css('text-align', 'center');
 
            //             }},

        
              
              {
                  extend: 'print', 
                  className: 'btn btn-sm btn-primary',
                  title: '',
                  header: true,
                  footer: false,
                  exportOptions: {
                      columns: exportCol,
                      format: {
                          header: function ( data, columnIdx ) {
                              return exportColName[columnIdx];
                          }
                      }
                  },
                  orientation: 'landscape',
              pageSize: "LEGAL",
                  "action": allExport,
                  messageTop: customReportHeader('Style Details', { })
              } 
          ],

          initComplete: function () {   
                var api =  this.api();

                // Apply the search 
                api.columns(searchable).every(function () {
                    var column = this; 
                    var input = document.createElement("input"); 
                    input.setAttribute('placeholder', $(column.header()).text());
                    input.setAttribute('style', 'width: 120px; height:25px; border:1px solid whitesmoke;');

                    $(input).appendTo($(column.header()).empty())
                    .on('keyup', function () {
                        column.search($(this).val(), false, false, true).draw();
                    });

                    $('input', this.column(column).header()).on('click', function(e) {
                        e.stopPropagation();
                    });
                });
                api.columns(selectable).every( function (i, x) {
                    var column = this;

                    var select = $('<select style="width: 140px; height:25px; border:1px solid whitesmoke; font-size: 12px; font-weight:bold;"><option value="">'+$(column.header()).text()+'</option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function(e){
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            
                            column.search(val ? '^'+val+'$' : '', true, false ).draw();
                            column.search(val ? val.toUpperCase().replace("'S","").replace( /&/g, '&amp;' ): '', true, false ).draw();
                            e.stopPropagation();
                        });

                    $.each(dropdownList[i], function(j, v) {
                        select.append('<option value="'+v+'">'+v+'</option>')
                    });
                // }, 1000);
                });
             }
       });
	   $(document).on("change",'#gender,#buyer,#season,#productType', function(e){
		e.preventDefault();
		dt.draw();
	}); 

}); 

</script>
@endpush
@endsection
