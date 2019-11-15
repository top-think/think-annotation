<?php
/** Created by 嗝嗝<china_wangyu@aliyun.com>. Date: 2019-11-14  */

namespace think\annotation\handler;


use Doctrine\Common\Annotations\Annotation;

class Validate extends Handler
{
    public function cls(string $class, Annotation $annotation, \think\Route &$route)
    {
        // TODO: Implement handleClass() method.
    }

    public function func(\ReflectionMethod $refMethod, Annotation $annotation, \think\Route &$route)
    {
        // TODO: Implement handleMethod() method.
        $route->validate($annotation->value, $annotation->scene, $annotation->message, $annotation->batch);
    }
}