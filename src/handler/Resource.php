<?php
/** Created by 嗝嗝<china_wangyu@aliyun.com>. Date: 2019-11-14  */

namespace think\annotation\handler;


use Doctrine\Common\Annotations\Annotation;

final class Resource extends Handler
{
    public function cls(\ReflectionClass $refClass, Annotation $annotation, \think\Route &$route)
    {
        $route->resource($annotation->value, $refClass->name)->option($annotation->getOptions());
    }
}