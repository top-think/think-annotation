<?php

namespace topthink\annotation;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionObject;

class Service extends \think\Service
{
    public function register()
    {
        AnnotationReader::addGlobalIgnoredName('mixin');
        // TODO: this method is deprecated and will be removed in doctrine/annotations 2.0
        AnnotationRegistry::registerLoader('class_exists');
        $annotationReader = new AnnotationReader();

        $this->app->resolving(function ($object, $app) use ($annotationReader) {

            $reflectionObject = new ReflectionObject($object);

            foreach ($reflectionObject->getProperties() as $reflectionProperty) {

                $propertyAnnotations = $annotationReader->getPropertyAnnotations($reflectionProperty);
                if (!empty($propertyAnnotations)) {
                    foreach ($propertyAnnotations as $annotation) {
                        $annotation($app, $object, $reflectionProperty);
                    }
                }
            }
        });
    }
}
