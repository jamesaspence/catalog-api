<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $email
 * @property ?string $password
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Collection $userIntegrations
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    private ?UserIntegration $authenticatedUserIntegration = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password'];

    public function setEmailAttribute(string $email): void
    {
        $this->attributes['email'] = strtolower($email);
    }

    public function userIntegrations(): HasMany
    {
        return $this->hasMany(UserIntegration::class);
    }

    /**
     * @param UserIntegration $authenticatedUserIntegration
     */
    public function setAuthenticatedUserIntegration(UserIntegration $authenticatedUserIntegration): void
    {
        $this->authenticatedUserIntegration = $authenticatedUserIntegration;
    }

    /**
     * @return ?UserIntegration
     */
    public function getAuthenticatedUserIntegration(): ?UserIntegration
    {
        return $this->authenticatedUserIntegration;
    }
}
