<?php
/** Created by 嗝嗝<china_wangyu@aliyun.com>. Date: 2019-11-14  */

namespace think\annotation\handler;


use Doctrine\Common\Annotations\Annotation;

interface HandleInterface
{
    public function handleClass(string $class,Annotation $annotation,\think\Route &$route);

    public function handleMethod(\ReflectionMethod $refMethod,Annotation $annotation,\think\Route &$route);
}