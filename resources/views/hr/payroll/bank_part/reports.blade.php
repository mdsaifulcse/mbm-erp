<div class="panel">
	<div class="panel-body">
		@if(!isset($input['export']))
		@php
			$urldata = http_build_query($input) . "\n";
		@endphp

		<a href='{{ url("hr/reports/bank-part-salary-report?$urldata&export=excel")}}' target="_blank" class="btn btn-sm btn-info hidden-print" id="excel" data-toggle="tooltip" data-placement="top" title="" data-original-title="Excel Download" style="position: absolute; top: 21px; left: 65px;"><i class="fa fa-file-excel-o"></i></a>
		@endif
		<div id="report_section" class="report_section">
			@if(!isset($input['export']))
			<style type="text/css">
				@media print {

		            .pagebreak {
		                page-break-before: always !important;
		            }
		            .disburse-button{
		                display: none;
		            }
		        }
              .table{
                width: 100%;
              }
              a{text-decoration: none;}
              .table-bordered {
                  border-collapse: collapse;
              }
              .table-bordered th,
              .table-bordered td {
                border: 1px solid #777 !important;
                padding:5px;
              }
              .no-border td, .no-border th{
                border:0 !important;
                vertical-align: top;
              }
              .f-14 th, .f-14 td, .f-14 td b{
                font-size: 14px !important;
              }
      </style>
      @endif
			<div class="top_summery_section">
				<div class="page-header">
		            <h2 style="margin:4px 10px; font-weight: bold; text-align: center;">Salary Report - {{ $input['pay_status']}} </h2>
		            <h4  style="text-align: center;">Month : {{ date('M Y', strtotime($input['year_month'])) }} </h4>

		        </div>

			</div>

			<div class="content_list_section">

				<table class="table table-bordered table-hover table-head" style="width:100%;border:1px solid #ccc;margin-bottom:0;font-size:14px;text-align:left" border="1" cellpadding="5">
					<thead>
		                <tr>
		                    <th>SL</th>
		                    <th>Oracle ID</th>
		                    <th>Associate ID</th>
		                    <th>Name</th>
		                    <th>Pay Amount</th>
		                    <th>TDS</th>
		                    <th class="capitalize">{{ $input['pay_status']}} Account No.</th>
		                    <th>Unit</th>
		                    <th>Location</th>
		                    <th>Department</th>
		                    <th>Routing Number</th>
		                </tr>
		            </thead>
		            <tbody>
		            @php $i = 0; $salarySum=0; $tds = 0; @endphp
		            @if(count($getEmployee) > 0)
			            @foreach($getEmployee as $employee)
			            	<tr>
			            		<td>{{ ++$i }}</td>
				            	<td>{{ $employee->as_oracle_code }}</td>
				            	<td>{{ $employee->as_id }}</td>
				            	<td> <b>{{ $employee->as_name }}</b> </td>
				            	<td style="text-align:right;">{{ $employee->bank_payable }}</td>
					            <td style="text-align:right;">{{ $employee->tds }}</td>
				            	<td>
				            		{{ $employee->bank_no }}
				            		@php
				            			$salarySum = $salarySum + $employee->bank_payable;
				            			$tds = $tds + $employee->tds;
				            		@endphp
				            	</td>
				            	<td>{{ $unit[$employee->as_unit_id]['hr_unit_short_name']??'' }}</td>
				            	<td>{{ $location[$employee->as_location]['hr_location_short_name']??'' }}</td>
				            	<td>{{ $department[$employee->as_department_id]['hr_department_name']??'' }}</td>
				            	<td>
				            		@php
				            			// $accNoExplode = explode('.', $employee->bank_no);
				            			// $branchCode = $accNoExplode[0];
				            			$branchCode = substr($employee->bank_no,0,3);
				            		@endphp
				            		{{ $routing[$branchCode]->routing_no??'' }}
				            	</td>
			            	</tr>
			            @endforeach
			            	<tr>
			            		<td colspan="4" class="text-right" style="text-align:right;"><b>Total Amount</b></td>

			            		<td style="text-align:right;"><b>{{ ($salarySum) }}</b></td>
			            		<td style="text-align:right;"><b>{{ ($tds) }}</b></td>
			            		<td colspan="5"> &nbsp;</td>
			            	</tr>
		            @else
			            <tr>
			            	<td colspan="11" class="text-center">No Employee Found!</td>
			            </tr>
		            @endif
		            </tbody>
				</table>

			</div>
		</div>
	</div>
</div>
