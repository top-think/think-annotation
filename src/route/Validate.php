<?php

namespace think\annotation\route;

use Attribute;

/**
 * Class Validate
 */
#[Attribute(Attribute::TARGET_METHOD)]
final class Validate
{
    /**
     * @var string
     */
    public string $scene;

    /**
     * @var array
     */
    public array $message = [];

    /**
     * @var bool
     */
    public bool $batch = true;
}
