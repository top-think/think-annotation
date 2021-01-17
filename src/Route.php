<?php

namespace think\annotation;

use Attribute;
use JetBrains\PhpStorm\ExpectedValues;

/**
 * 注册路由
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
final class Route
{
    /**
     * 路由规则
     * @var string
     */
    public string $rule;

    /**
     * 请求类型
     * @var string
     */
    public string $method = "*";

    public function __construct(
        string $rule,
        #[ExpectedValues(values: ["GET", "POST", "PUT", "DELETE", "PATCH", "OPTIONS", "HEAD"])] string $method = "*"
    )
    {
        $this->rule   = $rule;
        $this->method = $method;
    }

}
