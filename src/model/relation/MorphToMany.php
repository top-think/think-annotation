<?php

namespace think\annotation\model\relation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class MorphToMany
{
    /**
     * MORPH TO MANY关联定义
     * @param string $name 关联名
     * @param string $model 模型名
     * @param string $middle 中间表名/模型名
     * @param null $morph 多态字段信息
     * @param ?string $localKey 当前模型关联键
     */
    public function __construct(
        public string $name,
        public string $model,
        public string $middle,
        public $morph = null,
        public ?string $localKey = null
    )
    {
    }
}
