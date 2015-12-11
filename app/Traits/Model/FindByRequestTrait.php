<?php

namespace App\Traits\Model;

use Illuminate\Database\Query\Builder;
use Input;

/**
 * Class FindByRequestTrait
 *
 * Searches for any input parameters that are allowed by $findable array property
 * of subject. Parameter can be of array or scalar type.
 *
 * Requires rtconner/laravel-tagging to support search by tags
 *
 * @author Michael Sverdlikovsky <xedelweiss@gmail.com>
 *
 * @method static $this findByRequest(array $request = NULL)
 */
trait FindByRequestTrait {

    /**
     * Find by conditions in request
     *
     * @param Builder $query
     * @param array|null $request
     * @return \Illuminate\Database\Query\Builder
     */
    public function scopeFindByRequest($query, $request = NULL)
    {
        if (is_null($request)) {
            $request = Input::all();
        }

        $findable = isset($this->findable) ? $this->findable : [];

        foreach ($request as $field => $value) {
            if (!in_array($field, $findable)) {
                continue;
            }

            if ($field == 'tag') {
                if (isset($request['tag_search']) && $request['tag_search'] == 'any') {
                    $query->withAnyTag($value);
                } else {
                    $query->withAllTags($value);
                }
                continue;
            }

            if (is_array($value)) {
                $query->whereIn($field, $value);
            } elseif (is_scalar($value)) {
                $query->where($field, '=', $value);
            }
        }
    }
}