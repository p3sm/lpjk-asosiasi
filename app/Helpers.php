<?php // Code within app\Helpers\Helper.php

namespace App;

use Illuminate\Support\Facades\Auth;
use App\Permission;
use App\RolePermission;

class Helpers
{
    public static function checkPermission(string $module)
    {
      $permission = Permission::where('name',$module)->first();

      if($permission){
        $role = Auth::user()->role;

        $check_permission = RolePermission::where('role_id',$role->id)->where('permission_id',$permission->id)->first();
        if($check_permission){
           return true;
        }
      }

      return false;
    }
}