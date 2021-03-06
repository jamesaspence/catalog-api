<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $integration_id
 * @property string $client_id
 * @property string $token
 * @property string $name
 * @property ?string $description
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property Integration $integration
 */
class ApiToken extends Model
{
    use SoftDeletes;

    protected $hidden = ['token', 'client_id'];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}
