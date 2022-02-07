<?php

namespace App\Policies;

use App\Models\Upload;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class UploadPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Upload $upload
     * @return bool
     */
    public function view(User $user, Upload $upload): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Upload $upload
     * @return bool
     */
    public function update(User $user, Upload $upload): bool
    {
        return $this->owns($user, $upload);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Upload $upload
     * @return bool
     */
    public function delete(User $user, Upload $upload): bool
    {
        return $this->owns($user, $upload);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Upload $upload
     * @return bool
     */
    public function restore(User $user, Upload $upload): bool
    {
        return $this->owns($user, $upload);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Upload $upload
     * @return bool
     */
    public function forceDelete(User $user, Upload $upload): bool
    {
        return $this->owns($user, $upload);
    }

    private function owns(User $user, Upload $upload): bool
    {
        return $user->userIntegrations()
            ->where('id', '=', $upload->user_integration_id)
            ->exists();
    }
}
