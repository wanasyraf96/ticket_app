<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\RoleUser;

trait UserRolesTrait
{
    /**
     * @param int $user_id
     * @return bool
     */
    public function isStaff($user_id): bool
    {
        $staff_role_id = Role::where('name', 'staff')->first()->id;
        $isStaff = RoleUser::where('role_id', $staff_role_id)->where('user_id', $user_id)->first();
        if (!$isStaff) return false;
        return true;
    }
}
