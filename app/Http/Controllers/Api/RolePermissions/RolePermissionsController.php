<?php

namespace App\Http\Controllers\Api\RolePermissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Role;
use App\Permission;

class RolePermissionsController extends Controller
{
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Role $role, $permission)
    {
        //

        $permissionName = Permission::find($permission)->name;
        $role->givePermissionTo($permissionName);
        
        return $this->successResponse(['data' => $role, 'message' => 'Role Permissions is Updated'],201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role, $permission)
    {

        $permissionName = Permission::find($permission)->name;

        $role->revokePermissionTo($permissionName);
     
        return $this->successResponse(['data' => $role, 'message' => 'Role Permissions is Deleted'], 201);
    }
}
