<?php

namespace App\Services;

use App\Models\Role;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function save(array $validated, ?Role $role = null): Role
    {
        return DB::transaction(function () use ($validated, $role) {
            if ($role) {
                $role->update($validated);
            } else {
                $role = Role::create($validated);
            }

            return $role;
        });
    }

    public function delete(Role $role): void
    {
     
        // Check if the user group is still used by any users
        if ($role->users()->exists()) {
            // Throw generic exception with message
            throw new \Exception(__('general.data_is_still_used', ['name' => $role->name]));
        }

        DB::transaction(function () use ($role) {
            $role->delete(); // soft delete
        });
    }
    
    public function setActive(Role $role, bool $active): Role
    {
        $role->is_active = $active;
        $role->save();

        return $role;
    }
}
