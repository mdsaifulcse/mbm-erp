@foreach($subcategory as $subcategory1)
    <ul class="aminul"  >
        <li class="list-group-item first-child">&nbsp; <i class="fa fa-angle-right" aria-hidden="true"></i> 
      

                <a style="cursor: pointer;color:#58C0D2;" data-id="{{$subcategory1->hr_designation_id}}"   id="viewletter"  data-toggle="tooltip" data-placement="top" title="Double Click For Update" >
                             &nbsp; {{$subcategory1->hr_designation_name}}   
                             {{$employee_count[$subcategory1->hr_designation_id]??0}} 
            {{-- <a href="https://www.w3schools.com">&nbsp;{{$subcategory1->hr_designation_name}}</a> --}}
        </li>
        @if(count($subcategory1->subcategory))
            @include('hr.setup.designation_tree_include',['subcategory' => $subcategory1->subcategory])
        @endif
    </ul>
@endforeach