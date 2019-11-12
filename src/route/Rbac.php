<?php

namespace think\annotation\route;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Validate
 * @package think\annotation\route
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
final class Rbac extends Annotation
{
    /**
     * @var string
     */
    public $rule;

    /**
     * @var string
     */
    public $auth;

    /**
     * @var bool
     */
    public $show = true;
}
