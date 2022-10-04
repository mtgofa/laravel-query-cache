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
    use HasManyEvents;
    use HasBelongsToEvents;
    use HasBelongsToManyEvents;
    use HasMorphedByManyEvents;
    use HasMorphOneEvents;
    //use HasMorphToManyEvents;
    use HasMorphToEvents;
    use HasOneEvents;

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
        if (!in_array($event, ['booting', 'booted', 'retrieved']))
            error_log($event);
        $supportedEvents = config('perfectly-cache.clear_events', ['created', 'updated', 'deleted']);
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
        parent::boot();
        static::hasManyCreated(function ($parent, $related) {
            error_log("hasManyCreated:$related");
            //PerfectlyCache::clearCacheByTable(resolve($related)->getTable());
        });
        static::hasManyUpdated(function ($parent, $related) {
            error_log("hasManyUpdated:$related");
            PerfectlyCache::clearCacheByTable(resolve($related)->getTable());
        });
        static::hasManyDeleted(function ($parent, $related) {
            error_log("hasManyDeleted:$related");
            PerfectlyCache::clearCacheByTable(resolve($related)->getTable());
        });
        static::hasManyRestored(function ($parent, $related) {
            error_log("hasManyRestored:$related");
            PerfectlyCache::clearCacheByTable(resolve($related)->getTable());
        });

        /* Start belongsToMany Events */
        static::belongsToManyCreated(function ($parent, $related) {
            error_log("belongsToManyCreated:$parent,$related");
            //PerfectlyCache::clearCacheByTable(resolve($parent)->getTable(), resolve($related)->getTable());
        });
        static::belongsToManyAttached(function ($parent, $related) {
            error_log("belongsToManyAttached:$parent,$related");
            PerfectlyCache::clearCacheByTable(resolve($parent)->getTable(), resolve($related)->getTable());
        });
        static::belongsToManyDetached(function ($parent, $related) {
            error_log("belongsToManyDetached:$parent");
            PerfectlyCache::clearCacheByTable(resolve($parent)->getTable(), resolve($related)->getTable());
        });
        static::belongsToManySynced(function ($parent, $related) {
            error_log("belongsToManySynced:$parent,$related");
            PerfectlyCache::clearCacheByTable(resolve($parent)->getTable(), resolve($related)->getTable());
        });
        /* End belongsToMany Events */

        /* Start belongsTo Events */
        static::belongsToUpdated(function ($parent, $related) {
            error_log("belongsToUpdated:$parent,$related");
            //PerfectlyCache::clearCacheByTable(resolve($parent)->getTable(), resolve($related)->getTable());
        });
        static::belongsToAssociated(function ($parent, $related) {
            error_log("belongsToAssociated:$parent,$related");
            //PerfectlyCache::clearCacheByTable(resolve($parent)->getTable(), resolve($related)->getTable());
        });
        static::belongsToDissociated(function ($parent, $related) {
            error_log("belongsToDissociated:$parent,$related");
            //PerfectlyCache::clearCacheByTable(resolve($parent)->getTable(), resolve($related)->getTable());
        });
        /* End belongsTo Events */
    }
}
