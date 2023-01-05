<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Request;

class PermissionsController extends Controller
{
    //Get all Rols
    public function getRols()
    {
        $permissions = Permission::all();

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Success',
                'rols' =>  Role::with('permissions')->get(),
                'permissions' => $permissions
            ]
        );
    }

    public function deleteRoleForStaff(Request $request)
    {
        $user = User::whereEmail($request->userEmail)->first();
        if ($user) {
         $user =    $user->removeRole($request->rol);
            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Success',


                ]
            );
        } else {
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Error',

                ]
            );
        }
    }

    //Get All Users with permissions and rols
    public function getStaff(Request $request)
    {

        $users = User::whereHas('roles', function ($q) {
            $q->where('name', '!=', '');
        })->get();
       
        $users = UserResource::collection($users);
       
        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Success',
                'users' =>  $users,
            ]
        );
    }

    public function addUserToRol(Request $request)
    {
        $user = User::whereEmail($request->userEmail)->first();
        $rol = Role::find($request->rolId);

        //   return response()->json($rol);

        if ($user && $rol) {
            $user->assignRole($rol->name);
            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Success',
                    'rols' =>  Role::with('permissions')->get(),
                ]
            );
        } else {
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Error',
                    'rols' =>  Role::with('permissions')->get(),
                ]
            );
        }
    }


    public function deletePermissionToEdit(Request $request)
    {
        $rol = Role::find($request->rolId);
        $rol->revokePermissionTo($request->permissionId);
        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Success',
                'rols' =>  Role::with('permissions')->get(),
               'rol' => $rol
            ]
        );
    }




    //Add an permission To Rol
    public function addPermissionToRol(Request $request)
    {
        $rol = Role::find($request->rolId);
        $rol->givePermissionTo($request->permissionId);
        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Success',
                'rols' =>  Role::with('permissions')->get(),
                'rol' => $rol
            ]
        );
    }

    //Update An Role
    public function updateRol(Request $request, $id)
    {
        $rol = Role::find($id);
        $rol->name = $request->name;
        $rol->save();
        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Success',
                'rols' =>  Role::with('permissions')->get(),
            ]
        );
    }
    //Delete An Role
    public function deleteRol(Request $request)
    {
        $rol = Role::find($request->rolId);
        if ($rol) {

            $rol->delete();
            return response()->json(
                [
                    'code' => 'ok',
                    'message' => 'Success',
                    'rols' =>  Role::with('permissions')->get(),
                ]
            );
        } else {
            return response()->json(
                [
                    'code' => 'error',
                    'message' => 'Error',
                    'rols' =>  Role::with('permissions')->get(),
                ]
            );
        }
    }

    //Add new Rol
    public function addRol(Request $request)
    {
        $rol = Role::create(['guard_name' => 'api', 'name' => $request->name]);

        return response()->json(
            [
                'code' => 'ok',
                'message' => 'Success',
                'rols' =>  Role::with('permissions')->get(),
            ]
        );
    }
}
