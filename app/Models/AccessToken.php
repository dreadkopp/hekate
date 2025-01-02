<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
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
 * @property Carbon|null $last_used_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|\Eloquent $tokenable
 * @method static Builder<static>|AccessToken newModelQuery()
 * @method static Builder<static>|AccessToken newQuery()
 * @method static Builder<static>|AccessToken query()
 * @method static Builder<static>|AccessToken whereAbilities($value)
 * @method static Builder<static>|AccessToken whereCreatedAt($value)
 * @method static Builder<static>|AccessToken whereExpiresAt($value)
 * @method static Builder<static>|AccessToken whereId($value)
 * @method static Builder<static>|AccessToken whereLastUsedAt($value)
 * @method static Builder<static>|AccessToken whereName($value)
 * @method static Builder<static>|AccessToken whereToken($value)
 * @method static Builder<static>|AccessToken whereTokenableId($value)
 * @method static Builder<static>|AccessToken whereTokenableType($value)
 * @method static Builder<static>|AccessToken whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AccessToken extends SanctumPersonalAccessToken
{
    protected $table = 'personal_access_tokens';

    public static function findToken($token)
    {
        $id = Str::before('|', $token);
        $key = 'auth:token:' . $id;
        return
            Cache::store('apc')
                ->remember(
                    $key,
                    300,
                    fn() => Cache::remember(
                        $key,
                        3600,
                        function () use ($token) {
                            $token = parent::findToken($token);
                            $token->load(['tokenable']);
                            return $token;
                        }
                    )
                );
    }

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
        if (!array_key_exists('last_used_at', $changes) || count($changes) > 2) {
            parent::save();
        }
        return false;
    }


}