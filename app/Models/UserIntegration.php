<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property string $external_id
 */
class UserIntegration extends Pivot
{
    protected $table = 'integration_user';
}
