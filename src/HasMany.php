<?php

namespace MTGofa\QueryCache;

use MTGofa\QueryCache\Contracts\EventDispatcher;
use MTGofa\QueryCache\Traits\HasEventDispatcher;
use MTGofa\QueryCache\Traits\HasOneOrManyMethods;
use Illuminate\Database\Eloquent\Relations\HasMany as HasManyBase;

/**
 * Class HasMany.
 */
class HasMany extends HasManyBase implements EventDispatcher
{
    use HasEventDispatcher;
    use HasOneOrManyMethods;
}
