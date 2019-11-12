<?php

namespace think\annotation\route;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Validate
 * @package think\annotation\route
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
final class Jwt extends Annotation
{
    /**
     * @var string
     */
    public $name = 'authorization';

    /**
     * @var string
     */
    public $type = 'Bearer';

    /**
     * @var bool
     */
    public $state = 'header';
}
