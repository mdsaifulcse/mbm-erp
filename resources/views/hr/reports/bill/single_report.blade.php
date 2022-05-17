
 <table class="table table-head" style="width:100%;border:1px solid #ccc;font-size:9px;color:lightseagreen" cellpadding="2" cellspacing="0" border="1" align="center">
    <thead>
        <tr style="color:hotpink">
            <th width="100" style="width: 225px;">নাম ও 
                <br/> যোগদানের তারিখ</th>
            <th width="200">পদবি  ও গ্রেড</th>
            <th width="120">ইআরপি আইডি</th>
            <th width="120">তারিখ ও টাকা </th>
            <th width="180">ইনটাইম - আউটটাইম</th>
            
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <p style="margin:0;padding:0;">{{ $employee->as_name }}</p>
                <p style="margin:0;padding:0;">
                    @php
                        $doj = date('d-m-Y', strtotime($employee->as_doj));
                    @endphp
                    {{ Custom::engToBnConvert($doj) }}
                </p>
            </td>
            <td>
                <p style="margin:0;padding:0;">
                    {{ $designation[$employee->as_designation_id]['hr_designation_name_bn']}}
                    @if($employee->as_ot == 0)
                    - {{ $section[$employee->as_section_id]['hr_section_name_bn']??''}}
                    @endif 
                </p>
                @if(isset($designation[$employee->as_designation_id]))
                    @if($designation[$employee->as_designation_id]['hr_designation_grade'] > 0 || $designation[$employee->as_designation_id]['hr_designation_grade'] != null)
                    <p style="margin:0;padding:0">গ্রেডঃ {{ eng_to_bn($designation[$employee->as_designation_id]['hr_designation_grade'])}}</p>
                    @endif
                @endif
            </td>
            <td>
                <p style="font-size:14px;margin:0;padding:0;color:blueviolet">
                    {{ $employee->associate_id }}
                </p>
                
            </td>
            <td>
                <div class="flex-content" style="display: block; height: 100%; border: 0;">
                    
                        <div class="flex-chunk1">
                        @foreach($value as $dateList)
                        <p style="margin:0;padding:0" >
                            <span style="text-align: left; width: 65%; float: left;  white-space: wrap;" >
                                @php
                                    $singDate = date('d-m-Y', strtotime($dateList->in_date));
                                @endphp
                                {{ Custom::engToBnConvert($singDate) }}
                            </span>
                            <span style ="text-align: right;width: 10%; float: left;white-space: wrap;" >=
                            </span>
                            <span style="text-align: right;width: 25%; float: right;  white-space: wrap;" >
                                <font > {{ Custom::engToBnConvert($dateList->amount) }}</font>
                            </span>

                        </p>
                        @endforeach
                        </div>
                    
                </div>
            </td>
            <td>
                <div class="flex-content" style="display: block; height: 100%; border: 0;">
                    
                    @foreach($value as $att)
                        
                        <p style="margin:0;padding:0" >
                            
                            <span style="width: 40%; text-align: left; white-space: wrap; float: left;" >
                                <font >
                                    @if($att->remarks != 'DSI') 
                                    {{ $att->in_time == null?'null':Custom::engToBnConvert(date('H:i',strtotime($att->in_time))) }}
                                    @else
                                    null
                                    @endif
                                </font>
                            </span>
                            <span style ="width: 20%; text-align: left; white-space: wrap; float: left;" > -
                            </span>
                            <span style="width: 40%; text-align: left; float: left; white-space: wrap;" >
                                <font > {{ $att->out_time == null?'null':Custom::engToBnConvert(date('H:i',strtotime($att->out_time))) }}</font>
                            </span>
                        </p>
                        
                    @endforeach
                    
                </div>
            </td>
            
            
        </tr>
        <tr>
            <td colspan="3" class="text-right">মোট বিল </td>
            <td class="text-right">{{ Custom::engToBnConvert($totalAmount) }}</td>
            <td></td>
        </tr>
    </tbody>
</table>