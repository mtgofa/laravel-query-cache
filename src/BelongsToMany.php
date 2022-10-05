<?php

namespace MTGofa\QueryCache;

use MTGofa\QueryCache\Contracts\EventDispatcher;
use MTGofa\QueryCache\Traits\HasEventDispatcher;
use Illuminate\Database\Eloquent\Relations\BelongsToMany as BelongsToManyBase;

/**
 * Class BelongsToMany.
 *
 *
 * @property-read \MTGofa\QueryCache\Concerns\HasBelongsToManyEvents $parent
 */
class BelongsToMany extends BelongsToManyBase implements EventDispatcher
{
    use HasEventDispatcher;

    protected static $relationEventName = 'belongsToMany';

    /**
     * Toggles a model (or models) from the parent.
     *
     * Each existing model is detached, and non existing ones are attached.
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|int|string $ids
     * @param bool                                                                                                                   $touch
     *
     * @return array
     */
    public function toggle($ids, $touch = true)
    {
        $this->parent->fireModelBelongsToManyEvent('toggling', get_class($this->related), $ids);

        $result = parent::toggle($ids, $touch);

        $this->parent->fireModelBelongsToManyEvent('toggled', get_class($this->related), $ids, [], false);

        return $result;
    }

    /**
     * Sync the intermediate tables with a list of IDs or collection of models.
     *
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|int|string $ids
     * @param bool                                                                                                                   $detaching
     *
     * @return array
     */
    public function sync($ids, $detaching = true)
    {
        //dd($this);
        $this->parent->fireModelBelongsToManyEvent('syncing', get_class($this->related), $ids);

        $result = parent::sync($ids, $detaching);

        $changes = count($result['attached']) + count($result['updated']) + count($result['detached']);
        if ($changes > 0) {
            $this->parent->fireModelBelongsToManyEvent('synced', get_class($this->related), $ids, [], false);
        }

        return $result;
    }

    /**
     * Update an existing pivot record on the table.
     *
     * @param mixed $id
     * @param array $attributes
     * @param bool  $touch
     *
     * @return int
     */
    public function updateExistingPivot($id, array $attributes, $touch = true)
    {
        $this->parent->fireModelBelongsToManyEvent('updatingExistingPivot', get_class($this->related), $id, $attributes);

        if ($result = parent::updateExistingPivot($id, $attributes, $touch)) {
            $this->parent->fireModelBelongsToManyEvent('updatedExistingPivot', get_class($this->related), $id, $attributes, false);
        }

        return $result;
    }

    /**
     * Attach a model to the parent.
     *
     * @param mixed $id
     * @param array $attributes
     * @param bool  $touch
     */
    public function attach($id, array $attributes = [], $touch = true)
    {
        $this->parent->fireModelBelongsToManyEvent('attaching', get_class($this->related), $id, $attributes);

        parent::attach($id, $attributes, $touch);

        $this->parent->fireModelBelongsToManyEvent('attached', get_class($this->related), $id, $attributes, false);
    }

    /**
     * Detach models from the relationship.
     *
     * @param mixed $ids
     * @param bool  $touch
     *
     * @return int
     */
    public function detach($ids = null, $touch = true)
    {
        // Get detached ids to pass them to event
        $ids = $ids ?? $this->parent->{$this->getRelationName()}->pluck($this->relatedKey);

        $this->parent->fireModelBelongsToManyEvent('detaching', get_class($this->related), $ids);

        if ($result = parent::detach($ids, $touch)) {
            // If records are detached fire detached event
            // Note: detached event will be fired even if one of all records have been detached
            $this->parent->fireModelBelongsToManyEvent('detached', get_class($this->related), $ids, [], false);
        }

        return $result;
    }
}
