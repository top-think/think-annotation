<?php

namespace think\annotation\route;

use Attribute;

/**
 * 路由中间件
 */
#[Attribute(Attribute::TARGET_METHOD | Attribute::TARGET_CLASS)]
final class Middleware
{

}
