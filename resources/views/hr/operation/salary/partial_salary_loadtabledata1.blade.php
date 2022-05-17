<table class="table table-bordered">
                           {{--  <thead >
                                <tr  style="text-align:center;">
                                    <th>SL</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Salary <br>From Date</th>
                                    <th>Salary <br> To Date</th>
                                    <th>OT Pay <br> Status</th>
                                    <th>OT <br>From Date</th>
                                    <th>OT<br> To Date</th>
                                    <th>Salary <br>Below</th>
                                    <th>Location</th>
                                    <th>Area</th>
                                    <th>Edit</th>


                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $sl=0;
                                @endphp
                                @foreach($basic_employee222 as $basic)
                                @endforeach
                            </tbody>
                        </table> --}}
<input type="hidden" value="0" id="setFlug">
<div class="row">
    <div class="col h-min-400">
        <div id="result-process-bar" style="display: none;">
            <div class="iq-card">
                <div class="iq-card-body">
                    
                    <div class="" id="result-show">
                        <div class="progress">
                            <div class="progress-bar progress-bar-info progress-bar-striped active" id="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                              0%
                            </div>
                        </div>
                        
                            <h5>Do not close browser until process done</h5>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>



<script>
    
    $( document ).ready(function() {
        $("#result-process-bar").show();
        $('#setFlug').val(0);
        processbar(0);
        let insert = {!! json_encode($basic_employee222 ) !!}; // here $data import chunk controller compact data 
        const rules = {!! json_encode($find_emp_list ) !!}; // here $data import chunk controller compact data 
        console.log(insert);
        let promises = [];

        let ln = insert.length;
        var cnt = 0;
        console.log(ln)
        insert.forEach(setPromise);
        function setPromise(item){
            var request = $.ajax({
                type: "POST",
                url: '{{ url("hr/operation/partial-salary-process") }}',
                data :{
                    _token: '{{csrf_token()}}',
                    data: item,
                    rules: rules
                },
                success: function(res)
                {
                    cnt++;

                    if(cnt == ln){
                        $('#setFlug').val(1); 
                        processbar('success');
                         // $('.delete-'+id).remove();
                          $("#result-process-bar").hide();
                          callAjax(); 
                    }
                },
                error: function (reject) {                       }
            });

            promises.push(request);

        }

        /*$('#setFlug').val(1); 
        processbar('success');*/
    });




    var incValue = 1;
    
    function processbar(percentage) {
        var setFlug = $('#setFlug').val();
        if(parseInt(setFlug) === 1){
            var percentageVaule = 99;
            $('#progress-bar').html(percentageVaule+'%');
            $('#progress-bar').css({width: percentageVaule+'%'});
            $('#progress-bar').attr('aria-valuenow', percentageVaule+'%');
            setTimeout(() => {
                percentageVaule = 0;
                percentage = 0;
                $('#progress-bar').html(percentageVaule+'%');
                $('#progress-bar').css({width: percentageVaule+'%'});
                $('#progress-bar').attr('aria-valuenow', percentageVaule+'%');
                //$("#result-process-bar").css('display', 'none');
            }, 1000);
        }else if(parseInt(setFlug) === 2){
            console.log('error');
        }else{
            // set percentage in progress bar
            percentage = parseFloat(parseFloat(percentage) + parseFloat(incValue)).toFixed(2);
            $('#progress-bar').html(percentage+'%');
            $('#progress-bar').css({width: percentage+'%'});
            $('#progress-bar').attr('aria-valuenow', percentage+'%');
            if(percentage < 40 ){
                incValue = 1;
                // processbar(percentage);
            }else if(percentage < 60){
                incValue = 0.8;
            }else if(percentage < 75){
                incValue = 0.5;
            }else if(percentage < 85){
                incValue = 0.2;
            }else if(percentage < 98){
                incValue = 0.1;
            }else{
                return false;
            }
            setTimeout(() => {
                processbar(percentage);
            }, 1000);
        }

    }


</script>


