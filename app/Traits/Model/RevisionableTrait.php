<?php

namespace App\Traits\Model;

use Illuminate\Database\Eloquent\Model;
use PhpSpec\Exception\Exception;

/**
 * Class RevisionableTrait
 *
 * @author Michael Sverdlikovsky <xedelweiss@gmail.com>
 *
 * @method void saved($callback, $priority = 0)
 * @method void saving($callback, $priority = 0)
 * @method void setRawAttributes(array $attributes, $sync = false)
 *
 * @property $revisions
 */
trait RevisionableTrait {

    /**
     * Set dispatcher for saved event
     */
    public static function bootRevisionableTrait()
    {
        // save dump to revisions
        static::saved(function(Model $model){
            if (!$model->isDirty() || !$model->isRevisioningEnabled()) {
                return;
            }

            $attributes = $model->getAttributes();
            $attributes['base_id'] = $attributes['id'];
            unset($attributes['id']);

            $revisionClass = $model->getRevisionsClass();

            /** @var Model $revision */
            $revision = new $revisionClass;

            $revision->unguard();
            $revision->fill($attributes);
            $revision->reguard();

            $revision->save();
        });
    }

    /**
     * @return $this
     */
    public function getLastRevision()
    {
        return $this->revisions()
            ->latest('id')
            ->first();
    }

    /**
     * Get this models's revisions. Latest first.
     *
     * @return mixed
     * @throws Exception
     */
    public function revisions()
    {
        /** @var Model $revisionClass */
        $revisionClass = $this->getRevisionsClass();

        return $this->hasMany($revisionClass, 'base_id')->latest('id');
    }

    /**
     * @return bool
     */
    public function isRevisioningEnabled()
    {
        return (!isset($this->skipRevisioning) || !$this->skipRevisioning);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getRevisionsClass()
    {
        if (isset($this->revisionsModel) && !is_null($this->revisionsModel)) {
            return $this->revisionsModel;
        }

        $revisionClass = __CLASS__.'Revision';

        if (!class_exists($revisionClass)) {
            throw new Exception('Could not get revisions class for ' . __CLASS__.'. Set $revisionsModel property or create ' . __CLASS__ . 'Revision model');
        }

        return $revisionClass;
    }
}