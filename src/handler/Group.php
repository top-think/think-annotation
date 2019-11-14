<?php
/** Created by 嗝嗝<china_wangyu@aliyun.com>. Date: 2019-11-14  */

namespace think\annotation\handler;


use Doctrine\Common\Annotations\Annotation;

class Group extends Handler
{
    public function handleClass(string $class, Annotation $annotation, \think\Route &$route)
    {
        // TODO: Implement handleClass() method.
        $route->group($annotation->value)->option($annotation->getOptions());
    }

    public function handleMethod(\ReflectionMethod $refMethod, Annotation $annotation, \think\Route &$route)
    {
        // TODO: Implement handleMethod() method.
        $route->group($annotation->value);
    }
}