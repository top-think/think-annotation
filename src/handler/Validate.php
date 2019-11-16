<?php
/** Created by 嗝嗝<china_wangyu@aliyun.com>. Date: 2019-11-14  */

namespace think\annotation\handler;


use Doctrine\Common\Annotations\Annotation;

final class Validate extends Handler
{

    public function func(\ReflectionMethod $refMethod, Annotation $annotation, \think\route\RuleItem &$rule)
    {
        $rule->validate($annotation->value, $annotation->scene, $annotation->message, $annotation->batch);
    }
}