<?php

namespace think\annotation;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\Annotation\Enum;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Route
 * @package topthink\annotation
 * @Annotation
 * @Target({"METHOD","CLASS"})
 */
class Route extends Annotation
{
    /** @Enum({"GET","POST","PUT","DELETE","PATCH","OPTIONS","HEAD"}) */
    public $method;

}
