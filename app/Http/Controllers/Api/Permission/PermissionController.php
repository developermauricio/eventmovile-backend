<?php

namespace App\Http\Controllers\Api\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Permission;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $permissions = Permission::all();

        return $this->showAll($permissions,200);
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
        $rules = [
            'name'              => 'required|min:6',
            'guard_name'        => 'required|min:6'
        ];

        $this->validate($request, $rules);

        $permission = Permission::create([
            'name'      => $request->name,
            'guard_name'     => $request->guard_name
        ]);

        return $this->successResponse(['data'=> $permission, 'message'=>'Permission Created'], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Permission $permission)
    {
        //
        return $this->showOne($permission);
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
    public function update(Request $request, Permission $permission)
    {
        //
        $rules = [
            'name'          => 'min:6',
            'guard_name'    => 'min:6',
        ];

        $this->validate($request, $rules);
        
        $permission->fill($request->all());
        
        if ($permission->isClean()) {
            return $this->successResponse(['data' => $permission, 'message' => 'At least one different value must be specified to update'],201);
        }
        
        $permission->save();

        return $this->successResponse(['data' => $permission, 'message' => 'Permission Updated'],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Permission $permission)
    {
        //
        $permission->delete();   
        return $this->successResponse(['data' => $permission, 'message' => 'Permission Deleted'], 201);
    }
}
