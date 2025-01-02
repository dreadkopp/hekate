<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 *
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
class Client extends Model
{

}