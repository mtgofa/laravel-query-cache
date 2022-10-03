<?php

namespace MTGofa\QueryCache;

use MTGofa\QueryCache\Contracts\EventDispatcher;
use MTGofa\QueryCache\Traits\HasEventDispatcher;
use MTGofa\QueryCache\Traits\HasOneOrManyMethods;
use Illuminate\Database\Eloquent\Relations\MorphOne as MorphOneBase;

/**
 * Class MorphOne.
 */
class MorphOne extends MorphOneBase implements EventDispatcher
{
    use HasEventDispatcher;
    use HasOneOrManyMethods;
}
