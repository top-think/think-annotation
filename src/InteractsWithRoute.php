<?php

namespace think\annotation;

use Doctrine\Common\Annotations\Reader;
use ReflectionMethod;
use think\annotation\route\Resource;
use think\annotation\route\Route;
use think\App;
use think\event\RouteLoaded;
use think\Request;

/**
 * Trait InteractsWithRoute
 * @package think\annotation\traits
 * @property App $app
 * @property Reader $reader
 */
trait InteractsWithRoute
{

    /**
     * @var \think\Route
     */
    protected $route;

    /**
     * 注册注解路由
     */
    protected function registerAnnotationRoute()
    {
        if ($this->app->config->get('annotation.route.enable', true)) {
            $this->app->event->listen(RouteLoaded::class, function () {

                $this->route = $this->app->route;

                $dirs = [$this->app->getAppPath() . $this->app->config->get('route.controller_layer')]
                    + $this->app->config->get('annotation.route.controllers', []);

                foreach ($dirs as $dir) {
                    if (is_dir($dir)) {
                        $this->scanDir($dir);
                    }
                }
            });
        }
    }

    protected function scanDir($dir)
    {
        foreach ($this->findClasses($dir) as $class) {
            $refClass = new \ReflectionClass($class);
            $annotationName = null;

            foreach ($this->reader->getClassAnnotations($refClass) as $annotation) {
                $annotationName = basename(str_replace('\\', '/', get_class($annotation)));
                if (class_exists($this->think['annotation']. $annotationName)) {
                    (new $this->think['handler']. $annotationName())->cls($class, $annotation, $this->route);
                } elseif (class_exists($this->custom['annotation'] . $annotationName)) {
                    (new $this->custom['handler']. $annotationName())->cls($class, $annotation, $this->route);
                } elseif (isset($this->annotation[get_class($annotation)])) {
                    (new $this->annotation[get_class($annotation)]())->cls($class, $annotation, $this->route);
                }

            }

            //方法
            foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
                if ($this->reader->getMethodAnnotation($refMethod, Route::class)) {
                    foreach ($this->reader->getMethodAnnotations($refMethod) as $annotation) {
                        $annotationName = basename(str_replace('\\', '/', get_class($annotation)));
                        if (class_exists($this->think['annotation']. $annotationName)) {
                            (new $this->think['handler']. $annotationName())->func($class, $annotation, $this->route);
                        } elseif (class_exists($this->custom['annotation'] . $annotationName)) {
                            (new $this->custom['handler']. $annotationName())->func($class, $annotation, $this->route);
                        } elseif (isset($this->annotation[get_class($annotation)])) {
                            (new $this->annotation[get_class($annotation)]())->func($class, $annotation, $this->route);
                        }
                    }
                }
            }
        }
    }

    protected function getMethodAnnotations(ReflectionMethod $method, $annotationName)
    {
        $annotations = $this->reader->getMethodAnnotations($method);

        return array_filter($annotations, function ($annotation) use ($annotationName) {
            return $annotation instanceof $annotationName;
        });
    }

}
