<?php

namespace think\annotation\model;

use Doctrine\Common\Annotations\Reader;
use think\annotation\model\relation\BelongsTo;
use think\annotation\model\relation\BelongsToMany;
use think\annotation\model\relation\HasMany;
use think\annotation\model\relation\HasManyThrough;
use think\annotation\model\relation\HasOne;
use think\annotation\model\relation\HasOneThrough;
use think\annotation\model\relation\MorphByMany;
use think\annotation\model\relation\MorphMany;
use think\annotation\model\relation\MorphOne;
use think\annotation\model\relation\MorphTo;
use think\annotation\model\relation\MorphToMany;
use think\Model;

/**
 * Trait InteractsWithAnnotations
 * @package think\annotation\model
 * @mixin Model
 */
trait InteractsWithAnnotations
{
    static protected $annotationRelations = [];

    protected function prepareAnnotationRelations()
    {
        if (empty(self::$annotationRelations)) {

            $this->invoke(function (Reader $reader) {
                $annotations = $reader->getClassAnnotations(new \ReflectionClass($this));

                foreach ($annotations as $annotation) {
                    switch (true) {
                        case $annotation instanceof HasOne:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->hasOneThrough(
                                    $annotation->model,
                                    $annotation->foreignKey,
                                    $annotation->localKey
                                );
                            };
                            break;
                        case $annotation instanceof BelongsTo:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->belongsTo(
                                    $annotation->model,
                                    $annotation->foreignKey,
                                    $annotation->localKey
                                );
                            };
                            break;
                        case $annotation instanceof HasMany:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->hasMany(
                                    $annotation->model,
                                    $annotation->foreignKey,
                                    $annotation->localKey
                                );
                            };
                            break;
                        case $annotation instanceof HasManyThrough:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->hasManyThrough(
                                    $annotation->model,
                                    $annotation->through,
                                    $annotation->foreignKey,
                                    $annotation->throughKey,
                                    $annotation->localKey,
                                    $annotation->throughPk
                                );
                            };
                            break;
                        case $annotation instanceof HasOneThrough:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->hasOneThrough(
                                    $annotation->model,
                                    $annotation->through,
                                    $annotation->foreignKey,
                                    $annotation->throughKey,
                                    $annotation->localKey,
                                    $annotation->throughPk
                                );
                            };
                            break;
                        case $annotation instanceof BelongsToMany:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->belongsToMany($annotation->model,
                                    $annotation->middle,
                                    $annotation->foreignKey,
                                    $annotation->localKey
                                );
                            };
                            break;
                        case $annotation instanceof MorphOne:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->morphOne(
                                    $annotation->model,
                                    $annotation->morph ?: $annotation->value,
                                    $annotation->type
                                );
                            };
                            break;
                        case $annotation instanceof MorphMany:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->morphMany(
                                    $annotation->model,
                                    $annotation->morph ?: $annotation->value,
                                    $annotation->type
                                );
                            };
                            break;
                        case $annotation instanceof MorphTo:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->morphTo($annotation->morph ?: $annotation->value, $annotation->alias);
                            };
                            break;
                        case $annotation instanceof MorphToMany:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->morphToMany(
                                    $annotation->model,
                                    $annotation->middle,
                                    $annotation->morph,
                                    $annotation->localKey
                                );
                            };
                            break;
                        case $annotation instanceof MorphByMany:
                            $relation = function (Model $model) use ($annotation) {
                                return $model->morphByMany(
                                    $annotation->model,
                                    $annotation->middle,
                                    $annotation->morph,
                                    $annotation->foreignKey
                                );
                            };
                            break;
                    }

                    if (!empty($relation)) {
                        static::$annotationRelations[$annotation->value] = $relation;
                    }
                }
            });
        }
    }

    protected function isAnnotationRelationMethod($method)
    {
        return array_key_exists($method, static::$annotationRelations);
    }

    protected function getAnnotationRelation($method)
    {
        return (static::$annotationRelations[$method])($this);
    }

    public function __call($method, $args)
    {
        $this->prepareAnnotationRelations();
        if ($this->isAnnotationRelationMethod($method)) {
            return $this->getAnnotationRelation($method);
        }

        return parent::__call($method, $args);
    }
}
