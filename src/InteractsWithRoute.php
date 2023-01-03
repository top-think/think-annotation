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
        foreach (Constructs::fromDirectory($dir) as $construct) {
            $class           = $construct->name();
            $refClass        = new ReflectionClass($class);
            $routeGroup      = false;
            $routeMiddleware = [];
            $callback        = null;

            //类
            if ($resource = $this->reader->getAnnotation($refClass, Resource::class)) {
                //资源路由
                $callback = function () use ($class, $resource) {
                    $this->route->resource($resource->rule, $class)->option($resource->options);
                };
            }

            if ($middleware = $this->reader->getAnnotation($refClass, Middleware::class)) {
                $routeGroup      = '';
                $routeMiddleware = $middleware->value;
            }

            if ($group = $this->reader->getAnnotation($refClass, Group::class)) {
                $routeGroup = $group->rule;
            }

            if (false !== $routeGroup) {
                $routeGroup = $this->route->group($routeGroup, $callback);
                if ($group) {
                    $routeGroup->option($group->options);
                }

                $routeGroup->middleware($routeMiddleware);
            } else {
                if ($callback) {
                    $callback();
                }
                $routeGroup = $this->route->getGroup();
            }

            //方法
            foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $refMethod) {

                if ($route = $this->reader->getAnnotation($refMethod, Route::class)) {

                    //注册路由
                    $rule = $routeGroup->addRule($route->rule, "{$class}@{$refMethod->getName()}", $route->method);

                    $rule->option($route->options);

                    //中间件
                    if ($middleware = $this->reader->getAnnotation($refMethod, Middleware::class)) {
                        $rule->middleware($middleware->value);
                    }

                    //设置分组别名
                    if ($group = $this->reader->getAnnotation($refMethod, Group::class)) {
                        $rule->group($group->value);
                    }

                    //绑定模型,支持多个
                    if (!empty($models = $this->reader->getAnnotations($refMethod, Model::class))) {
                        /** @var Model $model */
                        foreach ($models as $model) {
                            $rule->model($model->var, $model->value, $model->exception);
                        }
                    }

                    //验证
                    if ($validate = $this->reader->getAnnotation($refMethod, Validate::class)) {
                        $rule->validate($validate->value, $validate->scene, $validate->message, $validate->batch);
                    }
                }
            }
        }
    }

}
