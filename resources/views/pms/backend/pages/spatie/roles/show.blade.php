<div class="form-group row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped table-hover">
            <tr>
                <th>
                    Role
                </th>
                <th>
                    Permissions
                </th>
            </tr>

            <tbody>
            <tr>
                <td>
                    {{$role->name}}
                </td>

                <td>

                    @if(!empty($rolePermissions))

                        @foreach($rolePermissions as $v)

                            <label class="label label-success">{{ $v->name }},</label>

                        @endforeach

                    @endif
                </td>

            </tr>
            </tbody>

        </table>
    </div>
</div>