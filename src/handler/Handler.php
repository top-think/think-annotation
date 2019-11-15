<?php
/** Created by 嗝嗝<china_wangyu@aliyun.com>. Date: 2019-11-14  */

namespace think\annotation\handler;


use Doctrine\Common\Annotations\Annotation;

abstract class Handler implements HandleInterface
{
    public function cls(string $class, Annotation $annotation, \think\Route &$route)
    {
        // TODO: Implement cls() method.
    }

    public function func(\ReflectionMethod $refMethod, Annotation $annotation, \think\Route &$route)
    {
        // TODO: Implement func() method.
    }

    /**
     * 判断是否当前控制器方法访问
     */
    public function currentMethodRequest(\ReflectionMethod $refMethod, \think\Route $route):bool {
        if (PHP_SAPI == 'cli'){
            return false;
        }
        $rule = $route->getRule(trim($_SERVER['PATH_INFO'],'/'));
        $ruleController = $refMethod->class.'@'.$refMethod->name;
        return isset($rule[$ruleController]) ? true : false;
    }
}