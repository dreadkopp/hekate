<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * 
 *
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property string $name
 * @property string $token
 * @property array<array-key, mixed>|null $abilities
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $tokenable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken whereAbilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken whereTokenableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken whereTokenableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccessToken whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccessToken extends SanctumPersonalAccessToken
{
    protected $table = 'personal_access_tokens';

    /**
     * Limit saving of PersonalAccessToken records
     *
     * We only want to actually save when there is something other than
     * the last_used_at column that has changed. It prevents extra DB writes
     * since we aren't going to use that column for anything.
     *
     */
    public function save(array $options = []): bool
    {
        $changes = $this->getDirty();
        // Check for 2 changed values because one is always the updated_at column
        if (! array_key_exists('last_used_at', $changes) || count($changes) > 2) {
            parent::save();
        }
        return false;
    }
}