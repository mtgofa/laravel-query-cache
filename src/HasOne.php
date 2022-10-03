<?php

namespace MTGofa\QueryCache;

use MTGofa\QueryCache\Contracts\EventDispatcher;
use MTGofa\QueryCache\Traits\HasEventDispatcher;
use MTGofa\QueryCache\Traits\HasOneOrManyMethods;
use Illuminate\Database\Eloquent\Relations\HasOne as HasOneBase;

/**
 * Class HasOne.
 */
class HasOne extends HasOneBase implements EventDispatcher
{
    use HasEventDispatcher;
    use HasOneOrManyMethods;
}
