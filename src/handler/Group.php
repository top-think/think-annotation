<?php
/** Created by 嗝嗝<china_wangyu@aliyun.com>. Date: 2019-11-14  */

namespace think\annotation\handler;


use Doctrine\Common\Annotations\Annotation;

final class Group extends Handler
{
    public function cls(\ReflectionClass $refClass, Annotation $annotation, \think\Route &$route)
    {
        $route->group($annotation->value)->option($annotation->getOptions());
    }

    public function func(\ReflectionMethod $refMethod, Annotation $annotation, \think\route\RuleItem &$rule)
    {
        $rule->group($annotation->value);
    }
}