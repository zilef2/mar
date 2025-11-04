<?php

namespace App\helpers;

class MyModels {
    public static function getPermissionToNumber($permissions): int{
        
        if ($permissions === 'empleado') return 1;
        if ($permissions === 'administrativo') return 2;
        if ($permissions === 'supervisor') return 3;
        if ($permissions === 'admin') return 9;
        if ($permissions === 'superadmin') return 10;
        return 0;
    }
    public static function getPermissiToLog($permissions): string{
        
        if ($permissions === 'empleado') return 'single';
        if ($permissions === 'administrativo') return 'soloadministrativo';
        if ($permissions === 'supervisor') return 'issupervisor';
        if ($permissions === 'admin') return 'soloadmin';
        if ($permissions === 'superadmin') return 'solosuper';
        return 'emergency';
    }
}
