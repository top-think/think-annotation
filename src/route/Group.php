<?php

namespace think\annotation\route;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Group
{
    public function __construct(public string $rule, public array $options = [])
    {
    }
}
