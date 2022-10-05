<?php

namespace MTGofa\QueryCache;

use MTGofa\QueryCache\Contracts\EventDispatcher;
use MTGofa\QueryCache\Traits\HasEventDispatcher;
use Illuminate\Database\Eloquent\Relations\MorphTo as MorphToBase;

/**
 * Class MorphTo.
 *
 *
 * @property-read \MTGofa\QueryCache\Concerns\HasMorphToEvents $parent
 */
class MorphTo extends MorphToBase implements EventDispatcher
{
    use HasEventDispatcher;

    /**
     * Associate the model instance to the given parent.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function associate($model)
    {
        $this->parent->fireModelMorphToEvent('associating', get_class($this->related), $model);

        $result = parent::associate($model);

        $this->parent->fireModelMorphToEvent('associated', get_class($this->related), $model);

        return $result;
    }

    /**
     * Dissociate previously associated model from the given parent.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function dissociate()
    {
        $parent = $this->getResults();

        $this->parent->fireModelMorphToEvent('dissociating', get_class($this->related), $parent);

        $result = parent::dissociate();

        if (!is_null($parent)) {
            $this->parent->fireModelMorphToEvent('dissociated', get_class($this->related), $parent);
        }

        return $result;
    }

    /**
     * Update the parent model on the relationship.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function update(array $attributes)
    {
        $related = $this->getResults();

        $this->parent->fireModelMorphToEvent('updating', get_class($this->related), $related);

        $result = $related->fill($attributes)->save();
        if ($related && $result) {
            $this->parent->fireModelMorphToEvent('updated', get_class($this->related), $related);
        }

        return $result;
    }
}
