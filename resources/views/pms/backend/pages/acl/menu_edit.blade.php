<div class="modal fade" id="editMenuModal" role="dialog">
<form action="{{ URL('/menu-update/'.$menu->menuid) }}" method="post">
  {{ csrf_field() }}    <!-- token -->
  <div class="modal-dialog" role="document">
     <div class="modal-content">
        <div class="modal-header" style="background-color: #A62B7F">
           <h4 class="modal-title"  style="color:white;" id="defaultModalLabel">Add Menu</h4>
        </div>
        <div class="modal-body">
           <div class="row clearfix">
              <div class="col-sm-12">
                 <div class="form-group">
                        <div class="form-line">

                        <select class="form-control show-tick" name="parent_menu">
                       	<option value="0" selected>--Main Menu--</option>
                       	@php
                       	$parentmenu = DB::table('tbl_menu')->where('parentid', 0)->get();
                       	@endphp
                        @foreach($parentmenu as $pmenu)
                        <option value="{{ $pmenu->menuid }}" @if($pmenu->menuid == $menu->parentid) selected @endif>{{ $pmenu->menu_name }}({{ $pmenu->menu_hints }})</option>
                         @endforeach
                        </select>

                        </div>
                    </div>
                 <div class="form-group">
                    <div class="form-line">
                       <input type="text" class="form-control" placeholder="Menu Name" name="menu_name"  value="{{$menu->menu_name}}" required="" />
                    </div>
                 </div>
                 <div class="form-group">
                    <div class="form-line">
                       <input type="text" class="form-control" placeholder="Menu URL" name="menu_url" value="{{$menu->menu_url}}" required="" />
                    </div>
                 </div>
                 <div class="form-group">
                    <div class="form-line">
                       <input type="text" class="form-control" placeholder="Menu Section" name="menu_hints" value="{{$menu->menu_hints}}" required="" />
                    </div>
                 </div>
                 <div class="form-group">
                    <div class="form-line">
                       <input type="text" class="form-control" placeholder="Sort" name="sort" required="" value="{{$menu->sort}}" />
                    </div>
                 </div>
                 <div class="form-group">
                      <div class="form-line"> 
                      <select class="form-control show-tick" name="status"> 
                        <option value="1" @if($menu->status==1){{'selected'}}@endif>Active</option>
                         <option value="0"@if($menu->status==0){{"selected"}}@endif>In-Active</option>
                     
                      </select>

                      </div>
                  </div>
              </div>
           </div>
        </div>
        <div class="modal-footer">
           <button type="submit" name="submit" class="btn btn-link waves-effect">SAVE</button>
           <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">CLOSE</button>
        </div>
</form>
</div>
