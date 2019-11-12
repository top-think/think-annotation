<?php

namespace think\annotation;

use Doctrine\Common\Annotations\Reader;
use ReflectionMethod;
use think\App;
use think\event\RouteLoaded;
use think\Request;

/**
 * Trait InteractsWithRoute
 * @package think\annotation\traits
 * @property App    $app
 * @property Reader $reader
 */
trait InteractsWithRoute
{
    
    /**
     * @var \think\Route
     */
    protected $route;
    
    use InteractsWithRouteAnnotion;
    
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
            $refClass        = new \ReflectionClass($class);
            $routeGroup      = false;
            $routeMiddleware = [];
            $callback        = null;

            //类
            /** @var Resource $resource */
            $this->setClassRouteResource($refClass,$class,$callback);

            $this->setClassRouteMiddleware($refClass,$routeGroup,$routeMiddleware);

            /** @var Group $group */
            $this->setClassRouteGroup($refClass,$routeMiddleware,$routeGroup,$callback);

            //方法
            foreach ($refClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $refMethod) {

                /** @var Route $route */
                if ($route = $this->reader->getMethodAnnotation($refMethod, Route::class)) {

                   $rule = $this->setMethodRoute($route,$refMethod,$routeGroup,$class);
                    
                   $this->setMethodRouteMiddleware($refMethod,$rule);
                   
                   $this->setMethodRouteGroup($refMethod,$rule);

                   $this->setMethodRouteModel($refMethod,$rule);

                   $this->setMethodRouteValidate($refMethod,$rule);

                   $this->setMethodRouteParamValidate($refMethod);
                   
                   $this->setMethodRbac($refMethod);

                   $this->setMethodLogger($refMethod);

                   $this->setMethodJwt($refMethod);
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
