<?php
/** Created by 嗝嗝<china_wangyu@aliyun.com>. Date: 2019-11-14  */

namespace think\annotation\handler;


use Doctrine\Common\Annotations\Annotation;

final class Middleware extends Handler
{
    protected $middleware = [];

    public function cls(\ReflectionClass $refClass, Annotation $annotation, \think\Route &$route)
    {
        array_push($this->middleware,$annotation->value);
    }

    public function func(\ReflectionMethod $refMethod, Annotation $annotation, \think\route\RuleItem &$rule)
    {
        array_push($this->middleware,$annotation->value);
        $rule->middleware($this->middleware);
    }
}