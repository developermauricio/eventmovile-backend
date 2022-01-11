<?php

namespace App\Http\Controllers\Api\Role;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        $roles = Role::all();

        return $this->showAll($roles,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
        return $this->showOne($role);
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
    public function update(Request $request, Role $role)
    {
        //
        $rules = [
            'name'        => 'min:6'
        ];

        $this->validate($request, $rules);

        
        $role->fill($request->all());
        
        if ($role->isClean()) {
            return $this->successResponse(['data' => $role, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $role->givePermissionTo('users.index');
        $role->save();

        return $this->successResponse(['data' => $role, 'message' => 'Role Updated'],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();   
        return $this->successResponse(['data' => $role, 'message' => 'Role Deleted'], 201);
    }
}
