<?php

namespace think\annotation\route;

use Attribute;

/**
 * 注入模型
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Model
{
    /**
     * @var string
     */
    public string $var = 'id';

    /**
     * @var boolean
     */
    public bool $exception = true;
}
