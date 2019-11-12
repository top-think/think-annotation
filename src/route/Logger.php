<?php

namespace think\annotation\route;

use Doctrine\Common\Annotations\Annotation;

/**
 * Class Validate
 * @package think\annotation\route
 * @Annotation
 * @Annotation\Target({"METHOD"})
 */
final class Logger extends Annotation
{
    /**
     * 参数sprintf格式化内容，参数取值于value
     * @var string
     */
    public $message;

    /**
     * @var array
     */
    public $value = [];
}
