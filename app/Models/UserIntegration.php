<?php


namespace App\Models;


use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $integration_id
 * @property int $user_id
 * @property string $external_id
 * @property Integration $integration
 * @property User $user
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserIntegration extends Model
{
    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
