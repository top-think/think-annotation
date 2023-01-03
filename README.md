# think-annotation for ThinkPHP6

> PHP8版本

## 安装

> composer require topthink/think-annotation

## 配置

> 配置文件位于 `config/annotation.php`

## 使用方法

### 路由注解

~~~php
<?php

namespace app\controller;

use think\annotation\Inject;
use think\annotation\route\Group;
use think\annotation\route\Middleware;
use think\annotation\route\Resource;
use think\annotation\route\Route;
use think\Cache;
use think\middleware\SessionInit;

#[Group("bb")]
#[Resource("aa")]
#[Middleware([SessionInit::class])]
class IndexController
{

    #[Inject]
    protected Cache $cache;

    public function index()
    {
        //...
    }

    #[Route('xx')]
    public function xx()
    {
        //...
    }

}

~~~

### 模型注解

~~~php
<?php

namespace app\model;

use think\Model;
use think\annotation\model\relation\HasMany;

#[HasMany("articles", Article::class, "user_id")]
class User extends Model
{

    //...
}
~~~


