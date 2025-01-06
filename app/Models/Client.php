<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * a device or application with a static token
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|Client newModelQuery()
 * @method static Builder<static>|Client newQuery()
 * @method static Builder<static>|Client query()
 * @method static Builder<static>|Client whereCreatedAt($value)
 * @method static Builder<static>|Client whereId($value)
 * @method static Builder<static>|Client whereName($value)
 * @method static Builder<static>|Client whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Client extends Model implements Authenticatable
{
    use HasApiTokens;

    public function getAuthIdentifierName()
    {
        return 'name';
    }

    public function getAuthIdentifier()
    {
       return 'name';
    }

    public function getAuthPasswordName()
    {
        return '';
    }

    public function getAuthPassword()
    {
        return '';
    }

    public function getRememberToken()
    {
        return '';
    }

    public function setRememberToken($value)
    {
        return;
    }

    public function getRememberTokenName()
    {
        return '';
    }
}