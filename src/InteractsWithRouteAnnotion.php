<?php

namespace think\annotation;


use think\annotation\route\Group;
use think\annotation\route\Jwt;
use think\annotation\route\Logger;
use think\annotation\route\Middleware;
use think\annotation\route\Model;
use think\annotation\route\Param;
use think\annotation\route\Rbac;
use think\annotation\route\Resource;
use think\annotation\route\Validate;
use think\exception\ValidateException;

/**
 * Trait InteractsWithRouteAnnotion
 * @package think\annotation\traits
 * @property App    $app
 * @property Reader $reader
 */
trait InteractsWithRouteAnnotion
{


    public function setClassRouteResource($refClass,$class,&$callback){
        /** @var Resource $resource */
        if ($resource = $this->reader->getClassAnnotation($refClass, Resource::class)) {
            //资源路由
            $callback = function () use ($class, $resource) {
                $this->route->resource($resource->value, $class)
                    ->option($resource->getOptions());
            };
        }
    }

    public function setClassRouteMiddleware($refClass,&$routeGroup,&$routeMiddleware){
        if ($middleware = $this->reader->getClassAnnotation($refClass, Middleware::class)) {
            $routeGroup      = '';
            $routeMiddleware = $middleware->value;
        }
    }

    public function setClassRouteGroup($refClass,$routeMiddleware,&$routeGroup,&$callback){
        if ($group = $this->reader->getClassAnnotation($refClass, Group::class)) {
            $routeGroup = $group->value;
        }

        if (false !== $routeGroup) {
            $routeGroup = $this->route->group($routeGroup, $callback);
            if ($group) {
                $routeGroup->option($group->getOptions());
            }

            $routeGroup->middleware($routeMiddleware);
        } else {
            if ($callback) {
                $callback();
            }
            $routeGroup = $this->route->getGroup();
        }
    }
    
    
    public function setMethodRoute($route,$refMethod,$routeGroup,$class){
        //注册路由
        $rule = $routeGroup->addRule($route->value, "{$class}@{$refMethod->getName()}", $route->method);

        $rule->option($route->getOptions());

        return $rule;
    }


    public function setMethodRouteMiddleware($refMethod,&$rule){
        //中间件
        if ($middleware = $this->reader->getMethodAnnotation($refMethod, Middleware::class)) {
            $rule->middleware($middleware->value);
        }
    }

    public function setMethodRouteGroup($refMethod,&$rule){
        //设置分组别名
        if ($group = $this->reader->getMethodAnnotation($refMethod, Group::class)) {
            $rule->group($group->value);
        }
    }
    
    public function setMethodRouteModel($refMethod,&$rule){
        //绑定模型,支持多个
        if (!empty($models = $this->getMethodAnnotations($refMethod, Model::class))) {
            /** @var Model $model */
            foreach ($models as $model) {
                $rule->model($model->var, $model->value, $model->exception);
            }
        }
    }
    
    public function setMethodRouteValidate($refMethod,&$rule){
        if ($validate = $this->reader->getMethodAnnotation($refMethod, Validate::class)) {
            $rule->validate($validate->value, $validate->scene, $validate->message, $validate->batch);
        }
    }
    
    public function setMethodRouteParamValidate($refMethod){
        if ($params = $this->getMethodAnnotations($refMethod,Param::class)){
            try{
                $paramConfig = config('annotation.param');
                return call_user_func(array(new $paramConfig['bind'],'handle'),$params);
            }catch (\Exception $exception){
                throw new \Exception($exception->getMessage());
            }
        }
    }

    public function setMethodRbac($refMethod){
        if ($rbac = $this->getMethodAnnotations($refMethod,Rbac::class)){
            try{
                $rbacConfig = config('annotation.rbac');
                return call_user_func(array(new $rbacConfig['bind'],'handle'),$rbac);
            }catch (\Exception $exception){
                throw new \Exception($exception->getMessage());
            }
        }
    }

    public function setMethodLogger($refMethod){
        if ($logger = $this->getMethodAnnotations($refMethod,Logger::class)){
            try{
                $loggerConfig = config('annotation.logger');
                return call_user_func(array(new $loggerConfig['bind'],'handle'),$logger);
            }catch (\Exception $exception){
                throw new \Exception($exception->getMessage());
            }
        }
    }

    public function setMethodJwt($refMethod){
        if ($jwt = $this->getMethodAnnotations($refMethod,Jwt::class)){
            try{
                $jwtConfig = config('annotation.jwt');
                return call_user_func(array(new $jwtConfig['bind'],'handle'),$jwt);
            }catch (\Exception $exception){
                throw new \Exception($exception->getMessage());
            }
        }
    }
}
