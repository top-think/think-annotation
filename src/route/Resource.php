<?php

namespace think\annotation\route;

use Attribute;

/**
 * 注册资源路由
 */
#[Attribute(Attribute::TARGET_CLASS)]
final class Resource extends Rule
{

}
