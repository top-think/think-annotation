<?php

namespace think\annotation\model\relation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class MorphTo
{
    /**
     * MORPH TO 关联定义
     * @param string $name 关联名
     * @param null $morph 多态字段信息
     * @param array $alias 多态别名定义
     */
    public function __construct(
        public string $name,
        public $morph = null,
        public array $alias = []
    )
    {
        $this->morph = $morph ?? $name;
    }
}
