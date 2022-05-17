@extends('merch.layout')
@section('title', 'Contract Sales List')
@section('main-content')
    @push('css')
        <style>
            a[href]:after {
                content: none !important;
            }

            thead {
                display: table-header-group;
            }

            th {
                font-size: 12px;
                font-weight: bold;
            }

            #example th:nth-child(2) input {
                width: 100px !important;
            }

            #example th:nth-child(3) input {
                width: 90px !important;
            }

            #example th:nth-child(5) select {
                width: 80px !important;
            }

            #example th:nth-child(6) select {
                width: 80px !important;
            }

            /*#example th:nth-child(7) select{
              width: 80px !important;
            }*/
            #example th:nth-child(7) input {
                width: 110px !important;
            }

            #example th:nth-child(8) input {
                width: 70px !important;
            }

            .text-warning {
                color: #c49090 !important;
            }

            table.dataTable thead > tr > td.sorting, table.dataTable thead > tr > td.sorting_asc, table.dataTable thead > tr > td.sorting_desc, table.dataTable thead > tr > th.sorting, table.dataTable thead > tr > th.sorting_asc, table.dataTable thead > tr > th.sorting_desc {
                padding-right: 16px;
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

                    <li class="active">Contract Sales List</li>
                </ul><!-- /.breadcrumb -->
            </div>


            <div class="page-content">
                <div class="">
                    <div class="panel panel-info">
                        <div class="panel-body">
                            @include('inc/message')
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table id="dataTables" class="table table-striped table-bordered"
                                           style="display: block; overflow-x: auto; width: 100%;">
                                        <thead>
                                        <tr>
                                            <th width="15%">Action</th>
                                            <th width="15%">SL</th>
                                            <th width="15%">Export LC / Contract No</th>
                                            <th width="15%">Buyer</th>
                                            <th width="15%">Unit</th>
                                            <th width="15%">Contract Number By</th>
                                            <th width="15%">Contract Qty</th>
                                            <th width="15%">ELC Date</th>
                                            <th width="15%">LC Type</th>
                                        </thead>
                                    </table>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                        </div><!-- /.page-content -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function () {

            var searchable = [1, 4, 5, 6];
            var selectable = [2, 3, 7];

            var dropdownList = {

                '2': [@foreach($buyer as $e) <?php echo "\"$e\"," ?> @endforeach],
                '3': [@foreach($unit as $e) <?php echo "\"$e\"," ?> @endforeach],
                '7': ['ELC', 'Contract'],
            };

            $('#dataTables').DataTable({
                order: [], //reset auto order
                processing: true,
                responsive: false,
                serverSide: true,
                pagingType: "full_numbers",
                language: {
                    processing: '<i class="fa fa-spinner fa-spin f-60" style="font-size:60px;margin-top:50px;z-index:100;"></i>'
                },
                dom: "lBftrip",
                ajax: '{!! url("merch/sales_contract/sales_contract_get_data") !!}',
                columns: [
                    {data: 'action', name: 'action', orderable: false, searchable: false},
                    {data: 'serial_no', name: 'serial_no' },
                    {data: 'lc_contract_no', name: 'lc_contract_no'},
                    {data: 'b_name', name: 'b_name'},
                    {data: 'hr_unit_name', name: 'hr_unit_name'},
                    {data: 'contract_no_by', name: 'contract_no_by'},
                    {data: 'contract_qty', name: 'contract_qty'},
                    {data: 'elc_date', name: 'elc_date'},
                    {data: 'lc_contract_type', name: 'lc_contract_type'},
                ],
                buttons: [
                    {
                        extend: 'copy',
                        className: 'btn-sm btn-info',
                        exportOptions: {
                            columns: ':visible'
                        },
                        header: false,
                        footer: true
                    },
                    {
                        extend: 'csv',
                        className: 'btn-sm btn-success',
                        exportOptions: {
                            columns: ':visible'
                        },
                        header: false,
                        footer: true
                    },
                    {
                        extend: 'excel',
                        className: 'btn-sm btn-warning',
                        exportOptions: {
                            columns: ':visible'
                        },
                        header: false,
                        footer: true
                    },
                    {
                        extend: 'pdf',
                        className: 'btn-sm btn-primary',
                        exportOptions: {
                            columns: ':visible'
                        },
                        header: false,
                        footer: true
                    },
                    {
                        extend: 'print',
                        className: 'btn-sm btn-default',
                        exportOptions: {
                            columns: ':visible'
                        },
                        header: false,
                        footer: true
                    }
                ],
                initComplete: function () {
                    var api = this.api();

                    // Apply the search
                    api.columns(searchable).every(function () {
                        var column = this;
                        var input = document.createElement("input");
                        input.setAttribute('placeholder', $(column.header()).text());

                        $(input).appendTo($(column.header()).empty())
                            .on('keyup', function () {
                                column.search($(this).val(), false, false, true).draw();
                            });

                        $('input', this.column(column).header()).on('click', function (e) {
                            e.stopPropagation();
                        });
                    });

                    // each column select list
                    api.columns(selectable).every(function (i, x) {
                        var column = this;

                        var select = $('<select><option value="">' + $(column.header()).text() + '</option></select>')
                            .appendTo($(column.header()).empty())
                            .on('change', function (e) {
                                var val = $.fn.dataTable.util.escapeRegex(
                                    $(this).val()
                                );
                                column.search(val ? val : '', true, false).draw();
                                e.stopPropagation();
                            });

                        $.each(dropdownList[i], function (j, v) {
                            select.append('<option value="' + v + '">' + v + '</option>')
                        });
                    });
                }
            });
        });


    </script>
@endsection
