<?php

namespace think\annotation\route;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Validate
 * @package think\annotation\route
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
final class Param extends Annotation
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $doc;

    /**
     * @var string
     */
    public $rule;

    /**
     * @var array
     */
    public $message = [];
}
