<div class="panel">
    <div class="panel-body">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>PO</th>
                <th>Color</th>
                <th>Country</th>
                <th>Port</th>
                <th>Quantity</th>
                <th>Exfactory Date</th>
                <th>Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach($polist as $po)
                <tr>
                    <td>{{$po->PO}}</td>
                    <td>{{$po->Color}}</td>
                    <td>{{$po->Country}}</td>
                    <td>{{$po->Port}}</td>
                    <td>{{$po->Quantity}}</td>
                    <td>{{$po->Exfactory_Date}}</td>
                    <td>
                        <center>
                            <div class="btn-group">
                                <a type="button" class="dropdown-toggle" data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false" title="Action">
                                    <i class="las la-cog"></i>
                                </a>
                                <div class="dropdown-menu">
                                    <a href="{{url('merch/po/').'/'.$po->id.'/edit'}}"
                                       class=" dropdown-item btn btn-xs btn-secondary" data-toggle="tooltip"
                                       title="PO Edit">
                                        <center>
                                            <i class=" ace-icon fa fa-pencil bigger-120"></i>
                                        </center>
                                    </a>
                                    <center>
                                                   <span class="las la-trash" onclick="event.preventDefault();
                                                       if(confirm('Are you really want to delete?')){
                                                       document.getElementById('form-delete-{{$po->id}}')
                                                       .submit()
                                                       }"></span>
                                    </center>

                                    <form style="display:none" id="{{'form-delete-'.$po->id}}" method="post"
                                          action="{{route('po.destroy',$po->id)}}">
                                        @csrf
                                        @method('delete')
                                    </form>
                                </div>
                            </div>
                        </center>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
</div>
