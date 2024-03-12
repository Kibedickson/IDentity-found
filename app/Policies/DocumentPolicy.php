<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Document $document): bool
    {
        return in_array($user->id, [$document->user_id, $document->claim_user_id]);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Document $document): bool
    {
        return in_array($user->id, [$document->user_id, $document->claim_user_id]);
    }

    public function delete(User $user, Document $document): bool
    {
        return in_array($user->id, [$document->user_id, $document->claim_user_id]);
    }

    public function restore(User $user, Document $document): bool
    {
        return in_array($user->id, [$document->user_id, $document->claim_user_id]);
    }

    public function forceDelete(User $user, Document $document): bool
    {
        return in_array($user->id, [$document->user_id, $document->claim_user_id]);
    }
}
