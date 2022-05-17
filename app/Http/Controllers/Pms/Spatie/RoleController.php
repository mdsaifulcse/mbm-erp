<?php



namespace App\Http\Controllers\Pms\Spatie;


use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;

use Spatie\Permission\Models\Permission;

use Validator,DB,Yajra\DataTables\DataTables;


class RoleController extends Controller

{

    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */


    function __construct()

    {
    }


    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)

    {
        $title='Role';

        return view('pms.backend.pages.spatie.roles.index', compact('title'))

            ->with('i', ($request->input('page', 1) - 1) * 5);
    }


    public function rolesData()
    {
        $allData=Role::orderBy('id','DESC')->select('roles.*');

        return DataTables::of($allData)
            ->addIndexColumn()
            ->addColumn('DT_RowIndex','')

            ->addColumn('action','
	                                            <!-- delete section -->
	                                            {!! Form::open(array(\'route\'=> [\'pms.acl.roles.destroy\',$id],\'method\'=>\'DELETE\',\'class\'=>\'deleteForm\',\'id\'=>"deleteForm$id")) !!}
	                                                {{ Form::hidden(\'id\',$id)}}
	                                                <a href="javascript:void(0)" onclick="return showRoleWithPermission({{$id}})" class="btn btn-info btn-sm"><i class="la la-eye" title="Click to view details"></i> show</a>
	                                                
	                                                <a href="{{route(\'pms.acl.roles.edit\',$id)}}" class="btn btn-warning btn-sm"><i class="la la-pencil-square" title="Click to Edit"></i> Edit</a>
	                                                <button type="button" onclick=\'return deleteConfirm("deleteForm{{$id}}");\' class="btn btn-danger btn-sm">
	                                                <i class="la la-trash"></i> Delete
	                                                </button>
	                                            {!! Form::close() !!}
            ')
            ->rawColumns(['action'])
            ->toJson();
    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {
        $title='Role';
        $permissions = Permission::orderBy('id','DESC')->get();

        //dd($permissions);
        $permissions=collect($permissions);

        return view('pms.backend.pages.spatie.roles.create', compact('permissions','title'));
    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)

    {

        $this->validate($request, [

            'name' => 'required|unique:roles,name',

            'permission' => 'required',

        ]);

        DB::beginTransaction();
        try{


            $role = Role::create(['name' => $request->input('name')]);

            $role->syncPermissions($request->input('permission'));

            DB::commit();
            return $this->backWithSuccess('Role created successfully');
        }catch(Exception $e){
            DB::rollback();
            return $this->backWithError($e->getMessage());
        }


    }

    /**

     * Display the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function show($id)

    {

        $title = 'Role with Permission';
        $role = Role::find($id);

        $rolePermissions = Permission::join("role_has_permissions", "role_has_permissions.permission_id", "=", "permissions.id")

            ->where("role_has_permissions.role_id", $id)->get();

        return view('pms.backend.pages.spatie.roles.show', compact('title','role', 'rolePermissions'));
    }


    /**

     * Show the form for editing the specified resource.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function edit($id)

    {

        $role = Role::find($id);

        $permissions = Permission::orderBy('id','DESC')->get();

        $permissions=collect($permissions);

        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)

            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')->all();
        $title='Update Role and Permission';

        return view('pms.backend.pages.spatie.roles.edit', compact('role', 'permissions', 'rolePermissions','title'));
    }



    /**

     * Update the specified resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function update(Request $request, $id)

    {

        $this->validate($request, [

            'name' => 'required',

            'permission' => 'required',

        ]);


        $role = Role::findOrFail($id);

        DB::beginTransaction();
        try{

            $role->name = $request->input('name');

            $role->save();

            $role->syncPermissions($request->input('permission'));

            DB::commit();
            return $this->backWithSuccess('Role and Permission Update successfully');
        }catch(\Exception $e){
            DB::rollback();
            return $this->backWithError($e->getMessage());
        }


    }

    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {
        try{
            $result = Role::find($id);

            if($result == NULL) //check if no record found
            {
                return redirect()->back()->with('error','Something Error Found !, Please try again.');
            }

            if($result->name =='developer') // for admin and developer account
            {
                return $this->backWithError('This Role Can not be delete');
            }

            DB::table("roles")->where('id', $id)->delete();
            return $this->backWithSuccess('Permission created successfully');
        }catch(\Exception $e){

            return $this->backWithError($e->getMessage());
        }

    }
}
