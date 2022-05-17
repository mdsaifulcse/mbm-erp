@extends('pms.backend.layouts.master-layout')
@section('main-content')
<div class="main-content">
    <div class="main-content-inner">

        <div class="block-header">
            <div class="row">
                <div class="col-lg-12">
                    <h2>
                        {{ $pageTitle}}
                        <small>
                         <a href="{{ URL('/dashboard') }}"> Dashboard </a> / Menu List
                        </small>
                    </h2>
                </div>

            </div>

        </div>
        

        @if(Session::has('success'))
            <div class="alert alert-success">
            {{ Session::get('success') }}
            </div>
        @endif
        @if(Session::has('error'))
            <div class="alert alert-danger">
            {{ Session::get('error') }}
            </div>
        @endif

        <div class="row clearfix">
      <!-- #END# Exportable Table -->
      <div class="card">
         <div class="header">
            <h2>
               Menu Set Up
               <button type="button" class="btn btn-default waves-effect m-r-20 pull-right" data-toggle="modal" data-target="#defaultModal">Add Menu</button>
            </h2>
         </div>
         <div class="body">
            <br>
            <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
               <form action="{{ URL('/menu-save') }}" method="post">
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
                                        <option value="{{ $pmenu->menuid }}">{{ $pmenu->menu_name }} ({{$pmenu->menu_hints}})</option>
                                         @endforeach 
                                        </select>

                                        </div>
                                    </div>
                                 <div class="form-group">
                                    <div class="form-line">
                                       <input type="text" class="form-control" placeholder="Menu Name" name="menu_name" required="" />
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <div class="form-line">
                                       <input type="text" class="form-control" placeholder="Menu URL" name="menu_url" required="" />
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <div class="form-line">
                                       <input type="text" class="form-control" placeholder="Menu Section" name="menu_hints" required="" />
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <div class="form-line">
                                       <input type="text" class="form-control" placeholder="Sort" name="sort" required="" />
                                    </div>
                                 </div>
                                 <div class="form-group">
                                    <div class="form-line">

                                    <select class="form-control show-tick" name="status">
                                    <option value="1" selected>Active</option>
                                    <option value="0" >In Active</option>
                                   
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
               </div>
            </div>
            <br>
            <div class="table-responsive">
               <table class="table table-bordered table-striped table-hover dataTable js-exportable ">
                  <thead>
                     <tr>
                        <th>SL</th>
                        <th>Parent Menu</th>
                        <th>Menu Name</th>
                        <th>URL</th>
                        <th>Menu Section</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tfoot>
                     <tr>
                        <th>SL</th>
                        <th>Parent Menu</th>
                        <th>Menu Name</th>
                        <th>URL</th>
                        <th>Menu Section</th>
                        <th>Sort</th>
                        <th>Status</th>
                        <th>Action</th>
                     </tr>
                  </tfoot>
                  <tbody>
                     @if(sizeof($menulist) > 0)   
                     @php
                     $serial =1;
                     @endphp
                     @foreach($menulist as $menu) 
                     <tr>
                        <th>{{$serial++ }}</th>
                        <th>{{($menu->parentmenu != '' ? $menu->parentmenu : 'Main Menu') }} ({{$menu->menu_hints}})</th>
                        <th>{{$menu->menu_name }}</th>
                        <th>{{$menu->menu_url}}</th>
                        <th>{{$menu->menu_hints}}</th>
                        <th>{{$menu->sort}}</th>
                        <th>{{($menu->status==1? 'Active' : 'Inactive')}}</th>
                        <th>
                        	<input type="button" name="company_edit" id="edit" value="Edit" class="btn bg-red btn-block btn-sm waves-effect" data-toggle="modal" onclick="editMenu('{{ $menu->menuid }}')" style="width: 70px;"/>
                        	<a class="btn bg-red btn-block btn-sm waves-effect" href="/menu-delete/{{$menu->menuid}}">Delete</a>
                    	</th>
                     </tr>
                    
                     @endforeach
                     @else
                     <tr>
                        <th colspan="8">No record found.</th>
                     </tr>
                     @endif     
                  </tbody>
               </table>
            </div>
         </div>
      </div>
      <!-- #END# Exportable Table -->
   </div>


            
    </div>
    <div id="editMenuModalDiv"></div>

</div>
@endsection
