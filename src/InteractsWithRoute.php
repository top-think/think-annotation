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
            $this->setClassAnnotations($refClass);
            $this->setMethodAnnotations($refClass);
        }
    }

    protected function setClassAnnotations(\ReflectionClass $refClass)
    {
        // 类
        $annotations = $this->reader->getClassAnnotations($refClass);
        $class = null;
        foreach ($annotations as $annotation) {
            $cls_name = basename(str_replace('\\', '/', get_class($annotation)));
            if (class_exists($this->think['annotation'] . $cls_name)) {
                $class = $this->think['handler'] . $cls_name;
            } elseif (class_exists($this->custom['annotation'] . $cls_name)) {
                $class = $this->custom['handler'] . $cls_name;
            } elseif (isset($this->annotation[get_class($annotation)])) {
                $class = $this->annotation[get_class($annotation)];
            }
            if ($object = new $class()){
                if (method_exists($object,'cls')){
                    $object->cls($refClass, $annotation, $this->route);
                }
            }
        }
    }

    protected function setMethodAnnotations(\ReflectionClass $refClass)
    {
        //方法
        $annotations = null;
        $class = null;
        foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
            $annotations = $this->reader->getMethodAnnotations($refMethod);
            if ($this->reader->getMethodAnnotation($refMethod, Route::class)) {
                foreach ($annotations as $annotation) {
                    $class = get_class($annotation);
                    $cls_name = basename(str_replace('\\', '/',$class));
                    if (class_exists($this->think['annotation'] . $cls_name)) {
                        $class = $this->think['handler'] . $cls_name;
                        (new $class)->func($refMethod, $annotation, $this->route);
                    } elseif (class_exists($this->custom['annotation'] . $cls_name)) {
                        $class = $this->custom['handler'] . $cls_name;
                        (new $class)->func($refMethod, $annotation, $this->route);
                    } elseif (isset($this->annotation[$class])) {
                        $class = $this->annotation[$class];
                        (new $class)->func($refMethod, $annotation, $this->route);
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
