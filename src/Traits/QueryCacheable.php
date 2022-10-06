<?php

namespace MTGofa\QueryCache\Traits;


use MTGofa\QueryCache\Builders\EloquentBuilder;
use MTGofa\QueryCache\Builders\QueryBuilder;
use MTGofa\QueryCache\Concerns\HasBelongsToEvents;
use MTGofa\QueryCache\Concerns\HasBelongsToManyEvents;
use MTGofa\QueryCache\Concerns\HasManyEvents;
use MTGofa\QueryCache\Concerns\HasMorphedByManyEvents;
use MTGofa\QueryCache\Concerns\HasMorphOneEvents;
use MTGofa\QueryCache\Concerns\HasMorphToEvents;
use MTGofa\QueryCache\Concerns\HasMorphToManyEvents;
use MTGofa\QueryCache\Concerns\HasOneEvents;
use MTGofa\QueryCache\PerfectlyCache;

trait QueryCacheable
{
    use CacheGettersSetters;

    use HasOneEvents;
    use HasManyEvents;

    use HasBelongsToEvents;
    use HasBelongsToManyEvents;

    use HasMorphToEvents;
    use HasMorphOneEvents;
    use HasMorphedByManyEvents;
    //use HasMorphToManyEvents;

    protected function newBaseQueryBuilder()
    {
        $connection = $this->getConnection();
        $queryBuilder =  new QueryBuilder(
            $connection,
            $connection->getQueryGrammar(),
            $connection->getPostProcessor()
        );

        $queryBuilder->isCacheEnable = $this->isCacheEnable;

        $queryBuilder->cacheMinutes = $this->cacheMinutes ?: config('perfectly-cache.minutes', PerfectlyCache::$defaultCacheMinutes);

        return $queryBuilder;
    }

    public function newEloquentBuilder($query)
    {
        return new EloquentBuilder($query);
    }

    public function newModelQuery()
    {
        return $this->newEloquentBuilder(
            $this->newBaseQueryBuilder()
        )->setModel($this);
    }

    public function controlForCache(string $event)
    {
        // if (!in_array($event, ['booting', 'booted', 'retrieved']))
        //     error_log($event);
        $supportedEvents = config('perfectly-cache.clear_events', ['created', 'updated', 'deleted', 'restored']);
        if (in_array($event, $supportedEvents)) {
            PerfectlyCache::clearCacheByTable($this->getTable());
        }
    }


    protected function fireCustomModelEvent($event, $method)
    {
        $this->controlForCache($event);

        return parent::fireCustomModelEvent($event, $method);
    }

    public function reloadCache()
    {
        PerfectlyCache::clearCacheByTable($this->getTable());
    }

    protected static function boot()
    {

        /* Start hasMany Events */
        foreach ([
            'hasManyCreated',
            'hasManyUpdated',
            'hasManyDeleted',
            'hasManyRestored',
        ] as $event) {
            if (in_array($event, config('perfectly-cache.clear_events', ['created', 'updated', 'deleted', 'restored']))) {
                static::$event(function ($event, $related) {
                    error_log("$event:$related");
                    PerfectlyCache::clearCacheByTable(resolve($related)->getTable());
                });
            }
        }
        /* End hasMany Events */

        /* Start belongsTo / belongsToMany Events */
        foreach ([
            'belongsToUpdated',
            'belongsToAssociated',
            'belongsToDissociated',

            'belongsToManyCreated',
            'belongsToManyAttached',
            'belongsToManyDetached',
            'belongsToManySynced',
        ] as $event) {
            if (in_array($event, config('perfectly-cache.clear_events', ['created', 'updated', 'deleted', 'restored']))) {
                static::$event(function ($event, $parent, $related) {
                    error_log("$event:$parent,$related");
                    PerfectlyCache::clearCacheByTable(resolve($parent)->getTable(), resolve($related)->getTable());
                });
            }
        }
        /* End belongsTo / belongsToMany  Events */

        /* Start morphTo / morphToOne / morphToMany Events */
        foreach ([
            'morphToAttached',
            'morphToDetached',
            'morphToUpdated',

            'morphByManyAttached',
            'morphByManyDetached',
            'morphByManySynced',
            'morphByManyToggled',
            'morphByManyUpdatedExistingPivot',

            'morphToManyAttached',
            'morphToManyDetached',
            'morphToManySynced',
            'morphToManyToggled',
            'morphToManyUpdatedExistingPivot',
        ] as $event) {
            if (in_array($event, config('perfectly-cache.clear_events', ['created', 'updated', 'deleted', 'restored']))) {
                static::$event(function ($event, $parent, $related) {
                    error_log("$event:$parent,$related");
                    PerfectlyCache::clearCacheByTable(resolve($parent)->getTable(), resolve($related)->getTable());
                });
            }
        }
        /* End morphTo / morphToOne / morphToMany Events */
        parent::boot();
    }
}
