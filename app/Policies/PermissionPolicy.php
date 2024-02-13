<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Permission;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin();
    }

    public function view(User $user, Permission $permission): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Permission $permission): bool
    {
    }

    public function delete(User $user, Permission $permission): bool
    {
    }

    public function restore(User $user, Permission $permission): bool
    {
    }

    public function forceDelete(User $user, Permission $permission): bool
    {
    }
}
