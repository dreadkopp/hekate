<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 *
 *
 * @property int $id
 * @property string $path the path used to proxy to service
 * @property string $endpoint target url to proxy to
 * @property int $skip_auth
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder<static>|Routing newModelQuery()
 * @method static Builder<static>|Routing newQuery()
 * @method static Builder<static>|Routing query()
 * @method static Builder<static>|Routing whereCreatedAt($value)
 * @method static Builder<static>|Routing whereEndpoint($value)
 * @method static Builder<static>|Routing whereId($value)
 * @method static Builder<static>|Routing wherePath($value)
 * @method static Builder<static>|Routing whereSkipAuth($value)
 * @method static Builder<static>|Routing whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Routing extends Model
{

}