<?php

namespace StitchDigital\Simpro\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $tenant_id
 * @property string $api_key
 */
class SimproCredentials extends Model
{
    protected $fillable = ['tenant_id', 'access_token'];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(config('simpro.multi_tenancy.tenant_model'));
    }
}
