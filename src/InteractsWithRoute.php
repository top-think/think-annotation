<?php

namespace think\annotation;

use Ergebnis\Classy\Constructs;
use ReflectionClass;
use ReflectionMethod;
use think\annotation\route\Group;
use think\annotation\route\Middleware;
use think\annotation\route\Model;
use think\annotation\route\Resource;
use think\annotation\route\Route;
use think\annotation\route\Validate;
use think\App;
use think\event\RouteLoaded;
use think\helper\Arr;

/**
 * Trait InteractsWithRoute
 * @package think\annotation\traits
 * @property App $app
 */
trait InteractsWithRoute
{
    /**
     * @var \think\Route
     */
    protected $route;

    protected $parsedClass = [];

    protected function registerAnnotationRoute()
    {
        if ($this->app->config->get('annotation.route.enable', true)) {
            $this->app->event->listen(RouteLoaded::class, function () {

                $this->route = $this->app->route;

                $dirs = array_merge(
                    $this->app->config->get('annotation.route.controllers', []),
                    [$this->app->getAppPath() . $this->app->config->get('route.controller_layer')]
                );

                foreach ($dirs as $dir => $options) {
                    if (is_numeric($dir)) {
                        $dir     = $options;
                        $options = [];
                    }

                    if (is_dir($dir)) {
                        $this->scanDir($dir, $options);
                    }
                }
            });
        }
    }

    protected function scanDir($dir, $options = [])
    {
        $groups = [];
        foreach (Constructs::fromDirectory($dir) as $construct) {
            $class = $construct->name();
            if (in_array($class, $this->parsedClass)) {
                continue;
            }
            $this->parsedClass[] = $class;

            $refClass = new ReflectionClass($class);

            $routes = [];
            //方法
            foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $refMethod) {
                if ($routeAnn = $this->reader->getAnnotation($refMethod, Route::class)) {

                    $routes[] = function () use ($routeAnn, $class, $refMethod) {
                        //注册路由
                        $rule = $this->route->rule($routeAnn->rule, "{$class}@{$refMethod->getName()}", $routeAnn->method);

                        $rule->option($routeAnn->options);

                        //中间件
                        if ($middlewareAnn = $this->reader->getAnnotation($refMethod, Middleware::class)) {
                            $rule->middleware($middlewareAnn->value);
                        }

                        //绑定模型,支持多个
                        if (!empty($modelsAnn = $this->reader->getAnnotations($refMethod, Model::class))) {
                            foreach ($modelsAnn as $modelAnn) {
                                $rule->model($modelAnn->var, $modelAnn->value, $modelAnn->exception);
                            }
                        }

                        //验证
                        if ($validateAnn = $this->reader->getAnnotation($refMethod, Validate::class)) {
                            $rule->validate($validateAnn->value, $validateAnn->scene, $validateAnn->message, $validateAnn->batch);
                        }
                    };
                }
            }

            $groups[] = function () use ($routes, $refClass, $class) {
                $groupName    = '';
                $groupOptions = [];
                if ($groupAnn = $this->reader->getAnnotation($refClass, Group::class)) {
                    $groupName    = $groupAnn->name;
                    $groupOptions = $groupAnn->options;
                }

                $group = $this->route->group($groupName, function () use ($refClass, $class, $routes) {
                    if ($resourceAnn = $this->reader->getAnnotation($refClass, Resource::class)) {
                        //资源路由
                        $this->route->resource($resourceAnn->rule, $class)->option($resourceAnn->options);
                    }

                    //注册路由
                    foreach ($routes as $route) {
                        $route();
                    }
                });

                $group->option($groupOptions);

                if ($middlewareAnn = $this->reader->getAnnotation($refClass, Middleware::class)) {
                    $group->middleware($middlewareAnn->value);
                }
            };
        }

        if (!empty($groups)) {
            $name = Arr::pull($options, 'name', '');
            $this->route->group($name, function () use ($groups) {
                //注册路由
                foreach ($groups as $group) {
                    $group();
                }
            })->option($options);
        }
    }

}
