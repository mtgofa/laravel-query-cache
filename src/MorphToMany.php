<?php

namespace MTGofa\QueryCache;

use MTGofa\QueryCache\Contracts\EventDispatcher;
use MTGofa\QueryCache\Traits\HasEventDispatcher;
use Illuminate\Database\Eloquent\Relations\MorphToMany as MorphToManyBase;

/**
 * Class MorphToMany.
 *
 *
 * @property-read \MTGofa\QueryCache\Concerns\HasMorphToManyEvents $parent
 */
class MorphToMany extends MorphToManyBase implements EventDispatcher
{
    use HasEventDispatcher;

    /**
     * Toggles a model (or models) from the parent.
     *
     * Each existing model is detached, and non existing ones are attached.
     *
     * @param mixed $ids
     * @param bool  $touch
     *
     * @return array
     */
    public function toggle($ids, $touch = true)
    {
        $this->parent->fireModelMorphToManyEvent('toggling', get_class($this->related), $ids);

        $result = parent::toggle($ids, $touch);

        $this->parent->fireModelMorphToManyEvent('toggled', get_class($this->related), $ids, [], false);

        return $result;
    }

    /**
     * Sync the intermediate tables with a list of IDs or collection of models.
     *
     * @param \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|array $ids
     * @param bool                                                                          $detaching
     *
     * @return array
     */
    public function sync($ids, $detaching = true)
    {
        $this->parent->fireModelMorphToManyEvent('syncing', get_class($this->related), $ids);

        $result = parent::sync($ids, $detaching);

        $this->parent->fireModelMorphToManyEvent('synced', get_class($this->related), $ids, [], false);

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
        $this->parent->fireModelMorphToManyEvent('updatingExistingPivot', get_class($this->related), $id, $attributes);

        if ($result = parent::updateExistingPivot($id, $attributes, $touch)) {
            $this->parent->fireModelMorphToManyEvent('updatedExistingPivot', get_class($this->related), $id, $attributes, false);
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
        $this->parent->fireModelMorphToManyEvent('attaching', get_class($this->related), $id, $attributes);

        parent::attach($id, $attributes, $touch);

        $this->parent->fireModelMorphToManyEvent('attached', get_class($this->related), $id, $attributes, false);
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

        $this->parent->fireModelMorphToManyEvent('detaching', get_class($this->related), $ids);

        if ($result = parent::detach($ids, $touch)) {
            // If records are detached fire detached event
            // Note: detached event will be fired even if one of all records have been detached
            $this->parent->fireModelMorphToManyEvent('detached', get_class($this->related), $ids, [], false);
        }

        return $result;
    }
}
