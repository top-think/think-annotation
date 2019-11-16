<?php

namespace think\annotation;

use Doctrine\Common\Annotations\Reader;
use think\annotation\route\Route;
use think\App;
use think\event\RouteLoaded;

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
            foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
                if ($route = $this->reader->getMethodAnnotation($refMethod, Route::class)) {
                    $rule = $this->route->addRule($route->value, "{$class}@{$refMethod->getName()}", $route->method);
                    $rule->option($route->getOptions());
                    $annotations = $this->reader->getMethodAnnotations($refMethod);
                    $this->setMethodAnnotations($refMethod,$annotations,$rule);
                }
            }
        }
    }

    protected function setClassAnnotations(\ReflectionClass $refClass)
    {
        // 类
        $annotations = $this->reader->getClassAnnotations($refClass);
        foreach ($annotations as $annotation) {
            $cls_name = basename(str_replace('\\', '/', get_class($annotation)));
            if (class_exists($this->think['annotation'] . $cls_name)) {
                $class = $this->think['handler'] . $cls_name;
            } elseif (class_exists($this->custom['annotation'] . $cls_name)) {
                $class = $this->custom['handler'] . $cls_name;
            } elseif (isset($this->annotation[get_class($annotation)])) {
                $class = $this->annotation[get_class($annotation)];
            }else{
                return ;
            }
            (new $class())->cls($refClass, $annotation, $this->route);
        }
    }

    protected function setMethodAnnotations($refMethod,$annotations,$rule)
    {
        //方法
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Route) continue ;
            $class = get_class($annotation);
            $cls_name = basename(str_replace('\\', '/',$class));
            if (class_exists($this->think['annotation'] . $cls_name)) {
                $class = $this->think['handler'] . $cls_name;
            } elseif (class_exists($this->custom['annotation'] . $cls_name)) {
                $class = $this->custom['handler'] . $cls_name;
            } elseif (isset($this->annotation[$class])) {
                $class = $this->annotation[$class];
            }else{
                return ;
            }
            (new $class())->func($refMethod, $annotation, $rule);
        }
    }

}
