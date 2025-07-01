<?php

namespace App\Services;

use App\Models\UserGroup;
use Illuminate\Support\Facades\DB;

class UserGroupService
{
    public function save(array $validated, ?UserGroup $userGroup = null): UserGroup
    {
        return DB::transaction(function () use ($validated, $userGroup) {
            if ($userGroup) {
                $userGroup->update($validated);
            } else {
                $userGroup = UserGroup::create($validated);
            }

            return $userGroup;
        });
    }

    public function delete(UserGroup $userGroup): void
    {
     
        // Check if the user group is still used by any users
        if ($userGroup->users()->exists()) {
            // Throw generic exception with message
            throw new \Exception(__('general.data_is_still_used', ['name' => $userGroup->name]));
        }

        DB::transaction(function () use ($userGroup) {
            $userGroup->delete(); // soft delete
        });
    }
    
    public function setActive(UserGroup $userGroup, bool $active): UserGroup
    {
        $userGroup->is_active = $active;
        $userGroup->save();

        return $userGroup;
    }
}
