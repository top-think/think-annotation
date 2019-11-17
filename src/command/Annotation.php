<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace think\annotation\command;

use think\console\command\Make;

class Annotation extends Make
{
    protected $type = "Annotation";

    protected function configure()
    {
        parent::configure();
        $this->setName('make:annotation')
            ->setDescription('Create a new annotation class');
    }

    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'annotation.plain.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\annotation';
    }
}
