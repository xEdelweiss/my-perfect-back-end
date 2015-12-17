<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ScopeInterface;

/**
 * Class VisibilityTrait
 * @package App\Traits\Model
 *
 * @author Michael Sverdlikovsky <michael.sverdlikovsky@ab-soft.net>
 * @copyright Copyright (c) 2015, RingCentral, Inc (http://www.ringcentral.com)
 *
 * Required fields:
 * - is_draft
 * - is_private
 * - author_id
 */
trait VisibilityTrait {

    public static function bootVisibilityTrait()
    {
        static::addGlobalScope(new class implements ScopeInterface
        {
            public function apply(Builder $builder, Model $model)
            {
                $user = \Auth::user();

                $builder->where(function ($query) use ($user, $model) {
                    $query->where(function ($query) use ($model) {
                        $query
                            ->where('is_draft', 0)
                            ->where('is_private', 0);
                    });

                    if (!$user) {
                        return;
                    }

                    $query->orWhere('author_id', $user->id);
                });
            }

            public function remove(Builder $builder, Model $model)
            {
                throw new \Exception('Not implemented');
            }

        });
    }

}