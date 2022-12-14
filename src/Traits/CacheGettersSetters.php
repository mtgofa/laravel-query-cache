<?php

namespace MTGofa\QueryCache\Traits;


trait CacheGettersSetters
{
    protected $isCacheEnable = true;
    /**
     * @return bool
     */
    public function getIsCacheEnabled()
    {
        return $this->isCacheEnable;
    }

    /**
     * @param bool|null $bool
     * @return $this
     */
    public function setIsCacheEnabled(?bool $bool = true)
    {
        $this->isCacheEnable = $bool;
        return $this;
    }
}
