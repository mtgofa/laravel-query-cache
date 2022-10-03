<?php

namespace MTGofa\QueryCache\Traits;


use MTGofa\QueryCache\Builders\EloquentBuilder;
use MTGofa\QueryCache\Builders\QueryBuilder;
use MTGofa\QueryCache\Concerns\HasManyEvents;
use MTGofa\QueryCache\Events\ModelEvents;
use MTGofa\QueryCache\PerfectlyCache;

trait QueryCacheable
{
    use CacheGettersSetters;
    use HasManyEvents;
    use HasOneOrManyMethods;

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
}
