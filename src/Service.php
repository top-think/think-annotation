<?php

namespace think\annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use think\annotation\model\InteractsWithAnnotations;
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
use think\App;
use think\ide\ModelGenerator;
use think\model\Collection;

class Service extends \think\Service
{
    use InteractsWithRoute, InteractsWithInject;

    /** @var Reader */
    protected $reader;

    public function register()
    {
        AnnotationReader::addGlobalIgnoredName('mixin');

        // TODO: this method is deprecated and will be removed in doctrine/annotations 2.0
        AnnotationRegistry::registerLoader('class_exists');

        $this->app->bind(Reader::class, function (App $app) {
            return new CachedReader(new AnnotationReader(), $app);
        });
    }

    public function boot(Reader $reader)
    {
        $this->reader = $reader;

        //注解路由
        $this->registerAnnotationRoute();

        //自动注入
        $this->autoInject();

        //模型注解方法提示
        $this->app->event->listen(ModelGenerator::class, function (ModelGenerator $generator) use ($reader) {

            $className = $generator->getReflection()->getName();

            if (in_array(InteractsWithAnnotations::class, class_uses_recursive($className))) {
                $annotations = $reader->getClassAnnotations($generator->getReflection());

                foreach ($annotations as $annotation) {
                    switch (true) {
                        case $annotation instanceof HasOne:
                            $generator->addMethod($annotation->value, \think\model\relation\HasOne::class, [], '');
                            $generator->addProperty($annotation->value, $annotation->model, true);
                            break;
                        case $annotation instanceof BelongsTo:
                            $generator->addMethod($annotation->value, \think\model\relation\BelongsTo::class, [], '');
                            $generator->addProperty($annotation->value, $annotation->model, true);
                            break;
                        case $annotation instanceof HasMany:
                            $generator->addMethod($annotation->value, \think\model\relation\HasMany::class, [], '');
                            $generator->addProperty($annotation->value, $annotation->model . '[]', true);
                            break;
                        case $annotation instanceof HasManyThrough:
                            $generator->addMethod($annotation->value, \think\model\relation\HasManyThrough::class, [], '');
                            $generator->addProperty($annotation->value, $annotation->model . '[]', true);
                            break;
                        case $annotation instanceof HasOneThrough:
                            $generator->addMethod($annotation->value, \think\model\relation\HasOneThrough::class, [], '');
                            $generator->addProperty($annotation->value, $annotation->model, true);
                            break;
                        case $annotation instanceof BelongsToMany:
                            $generator->addMethod($annotation->value, \think\model\relation\BelongsToMany::class, [], '');
                            $generator->addProperty($annotation->value, $annotation->model . '[]', true);
                            break;
                        case $annotation instanceof MorphOne:
                            $generator->addMethod($annotation->value, \think\model\relation\MorphOne::class, [], '');
                            $generator->addProperty($annotation->value, $annotation->model, true);
                            break;
                        case $annotation instanceof MorphMany:
                            $generator->addMethod($annotation->value, \think\model\relation\MorphMany::class, [], '');
                            $generator->addProperty($annotation->value, 'mixed', true);
                            break;
                        case $annotation instanceof MorphTo:
                            $generator->addMethod($annotation->value, \think\model\relation\MorphTo::class, [], '');
                            $generator->addProperty($annotation->value, 'mixed', true);
                            break;
                        case $annotation instanceof MorphToMany:
                        case $annotation instanceof MorphByMany:
                            $generator->addMethod($annotation->value, \think\model\relation\MorphToMany::class, [], '');
                            $generator->addProperty($annotation->value, Collection::class, true);
                            break;
                    }
                }
            }
        });
    }

}
