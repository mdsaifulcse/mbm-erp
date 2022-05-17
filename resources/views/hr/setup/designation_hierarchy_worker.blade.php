<div class="panel-heading"><h6> Worker</h6></div>  
<div class="panel-body"> 
    <div class="hidden output"></div>
    @foreach($designation_for_worker as $taxonomyw)
    <ul class="list-group ">
        <li class="list-group-item" > &nbsp; <i class="fa fa-angle-right" aria-hidden="true"></i>  <a style="cursor: pointer;color:#58C0D2;" data-id="{{$taxonomyw->hr_designation_id}}"   id="viewletter"  data-toggle="tooltip" data-placement="top" title="Double Click For Update" >
            &nbsp; {{$taxonomyw->hr_designation_name}}</li>
            @if(count($taxonomyw->subcategory))
            @include('hr.setup.designation_tree_include',['subcategory' => $taxonomyw->subcategory])
            @endif
        </ul>
        @endforeach

    </div>