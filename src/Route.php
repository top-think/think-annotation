<?php

namespace think\annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * 注册路由
 * @package topthink\annotation
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
final class Route extends Annotation
{
    /**
     * @Enum({"GET","POST","PUT","DELETE","PATCH","OPTIONS","HEAD"})
     * @var string
     */
    public $method = "*";

}
