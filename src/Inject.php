<?php

namespace think\annotation;

use Attribute;

/**
 * 属性自动依赖注入
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Inject
{
    public function __construct(public ?string $abstract = null)
    {
    }
}
