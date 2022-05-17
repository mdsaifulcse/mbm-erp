<?php

namespace App\Http\Controllers\Pms;

use App\Http\Controllers\Controller;
use App\Models\PmsModels\ApprovalRangeSetup;
use App\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class ApprovalRangeSetupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $title = 'Approval Range Setup';
            $ranges = ApprovalRangeSetup::all();
            $roles = Role::all();

            return view('pms.backend.pages.approval-range-setup.index', compact('title', 'ranges', 'roles'));
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = User::all();
        $users = [];
        foreach ($data as $item){
            if ($item->hasRole($request->name)){
                $users[] = $item;
            }
        }
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $range = new ApprovalRangeSetup();
            $range->min_amount = $request->min_amount;
            $range->max_amount = $request->max_amount;
            $range->save();
            $range->relUsers()->sync($request->users);
            return $this->backWithSuccess('Saved Successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ApprovalRangeSetup $rangeSetup)
    {
        $rangeSetup->users = [];
        $role = [];
        foreach ($rangeSetup->relUsers as $user){
            foreach ($user->roles->pluck('name') as $item){
                array_push($role,$item);
            }
        }
        $rangeSetup->role = array_unique($role)[0];
        $output = $this->editForm($rangeSetup);
        return response()->json($output);
    }

    protected function editForm($rangeSetup)
    {
        $roleRow = [];
        $sRole = '';
        foreach (Role::all() as $role){
            if ($role->name === $rangeSetup->role){
                $sRole = $role;
                array_push($roleRow,'<option selected>'.$role->name.'</option>');
            }else{
                array_push($roleRow,'<option>'.$role->name.'</option>');
            }

        }

        $opt = [];
        foreach ($rangeSetup->relUsers as $relUser){
            $opt [] = $relUser->id;
        }

        $data = User::all();
        $users = [];
        foreach ($data as $item){
            if ($item->hasRole($sRole->name)){
                $users[] = '<option '.(in_array($item->id,$opt)?'selected':'').' value="'.$item->id.'">'.$item->name.'</option>';
            }
        }

        $output = '<form action="'.route('pms.range-setup.update',$rangeSetup->id).'" method="post">
                    <input type="hidden" name="_token" value="'.csrf_token().'">
                    <input type="hidden" name="_method" value="put">
                    <p class="mb-1 font-weight-bold"><label for="minAmount">'.__('From Amount').':</label></p>
                    <div class="input-group input-group-lg mb-3 d-">
                        <input type="number" name="min_amount" id="minAmount" class="form-control rounded" aria-label="Large" placeholder="'.__('Start from ').'" aria-describedby="inputGroup-sizing-sm" required value="'.$rangeSetup->min_amount.'">

                    </div>

                    <p class="mb-1 font-weight-bold"><label for="maxAmount">'.__('To Amount').':</label> </p>
                    <div class="input-group input-group-lg mb-3 d-">
                        <input type="number" name="max_amount" id="maxAmount" class="form-control rounded" aria-label="Large" placeholder="'.__('End to').'" aria-describedby="inputGroup-sizing-sm" required value="'.$rangeSetup->max_amount.'">

                    </div>

                    <p class="mb-1 font-weight-bold"><label for="role">'.__('Roles').':</label></p>
                    <div class="select-search-group input-group input-group-lg mb-3 d-">
                        <select name="role" id="role" class="form-control" required>
                                <option>'.__('Select One').'</option>
                            '.implode(',',$roleRow).'
                        </select>
                    </div>

                    <p class="mb-1 font-weight-bold"><label for="users">'.__('Users').':</label> </p>
                    <div class="select-search-group input-group input-group-lg mb-3 d-">
                        <select name="users[]" id="users" class="form-control" required multiple>'.implode(',', $users).'</select>
                    </div>
                </form>';
        return $output;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ApprovalRangeSetup $rangeSetup)
    {
        try {
            $range = $rangeSetup;
            $range->min_amount = $request->min_amount;
            $range->max_amount = $request->max_amount;
            $range->save();
            $range->relUsers()->sync($request->users);
            return $this->backWithSuccess('Updated Successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ApprovalRangeSetup $rangeSetup)
    {
        try {
            $rangeSetup->relUsers()->sync([]);
            $rangeSetup->delete();
            return $this->backWithSuccess('Deleted Successfully');
        }catch (\Throwable $th){
            return $this->backWithError($th->getMessage());
        }
    }
}
