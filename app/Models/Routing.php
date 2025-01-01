<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $path the path used to proxy to service
 * @property string $endpoint target url to proxy to
 * @property int $skip_auth
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Routing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Routing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Routing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Routing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Routing whereEndpoint($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Routing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Routing wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Routing whereSkipAuth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Routing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Routing extends Model
{

}