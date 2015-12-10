<?php

namespace App\Components\ApiFormatter\Adapters;

abstract class AbstractAdapter
{
    abstract public function format($data);

    /**
     * @return bool
     */
    protected function isDebugEnabled()
    {
        return config('app.debug');
    }
}