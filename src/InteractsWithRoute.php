<?php

namespace think\annotation;

use Doctrine\Common\Annotations\Reader;
use think\annotation\route\Group;
use think\annotation\route\Middleware;
use think\annotation\route\Resource;
use think\App;
use think\event\RouteLoaded;

/**
 * Trait InteractsWithRoute
 * @package think\annotation\traits
 * @property App    $app
 * @property Reader $reader
 */
trait InteractsWithRoute
{
    /**
     * 注册注解路由
     */
    protected function registerAnnotationRoute()
    {
        if ($this->app->config->get('annotation.route.enable', true)) {
            $this->app->event->listen(RouteLoaded::class, function () {
                $dirs = [$this->app->getAppPath() . $this->app->config->get('route.controller_layer')]
                    + $this->app->config->get('annotation.route.controllers', []);

                foreach ($dirs as $dir) {
                    $this->scanDir($dir);
                }
            });
        }
    }

    protected function scanDir($dir)
    {
        foreach ($this->findClasses($dir) as $class) {
            $refClass        = new \ReflectionClass($class);
            $routeGroup      = false;
            $routeMiddleware = [];
            $callback        = null;

            //类
            if ($resource = $this->reader->getClassAnnotation($refClass, Resource::class)) {
                //资源路由
                $callback = function () use ($class, $resource) {
                    $this->app->route->resource($resource->value, $class)->middleware($resource->middleware);
                };
            }

            if ($middleware = $this->reader->getClassAnnotation($refClass, Middleware::class)) {
                $routeGroup      = '';
                $routeMiddleware = $middleware->value;
            }

            if ($group = $this->reader->getClassAnnotation($refClass, Group::class)) {
                $routeGroup = $group->value;
            }

            if ($routeGroup !== false) {
                $routeGroup = $this->app->route->group($routeGroup, $callback)->middleware($routeMiddleware);
            } else {
                if ($callback) {
                    $callback();
                }
                $routeGroup = $this->app->route->getGroup();
            }

            //方法
            foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {
                if ($route = $this->reader->getMethodAnnotation($refMethod, Route::class)) {
                    //注册路由
                    $rule = $routeGroup->addRule($route->value, "{$class}@{$refMethod->getName()}", $route->method);

                    if ($middleware = $this->reader->getMethodAnnotation($refMethod, Middleware::class)) {
                        $rule->middleware($middleware->value);
                    }

                    if ($group = $this->reader->getMethodAnnotation($refMethod, Group::class)) {
                        $rule->group($group->value);
                    }
                }
            }
        }
    }
}
