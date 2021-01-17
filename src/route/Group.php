<?php

namespace think\annotation\route;

use Attribute;

/**
 * 路由分组
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Group extends Rule
{

}
