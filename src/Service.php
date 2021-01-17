<?php

namespace think\annotation;

class Service extends \think\Service
{
    use  InteractsWithInject, InteractsWithModel;

    public function boot()
    {
        //注解路由
        //TODO 暂未实现
        //$this->registerAnnotationRoute();

        //自动注入
        $this->autoInject();

        //模型注解方法提示
        $this->detectModelAnnotations();
    }

}
