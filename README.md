# think-annotation for ThinkPHP6

## 安装

> composer require lazyskills/think-annotation

## 配置

> 配置文件位于 `config/annotation.php`

## 使用方法

~~~
<?php

namespace app\controller;

use think\annotation\Inject;
use think\annotation\Route;
use think\annotation\route\Group;
use think\annotation\route\Middleware;
use think\annotation\route\Resource;
use think\Cache;
use think\middleware\SessionInit;

/**
 * Class IndexController
 * @package app\controller
 * @Group("bb")
 * @Resource("aa")
 * @Middleware({SessionInit::class})
 */
class IndexController
{

    /**
     * @Inject()
     * @var Cache
     */
    protected $cache;

    public function index()
    {
        //...
    }

    /**
     * @Route("xx")
     */
    public function xx()
    {
        //...
    }

}

~~~

### 自定义注解

#### 创建注解

通过命令行模式创建，例如创建一个User的注解，分为以下三部分

- 添加注解命令行

    修改tp6命令行文件位置：`config/console.php`
    
    添加以下内容:
    
    ```php
    <?php
    // +----------------------------------------------------------------------
    // | 控制台配置
    // +----------------------------------------------------------------------
    return [
        // 指令定义
        'commands' => [
            \think\annotation\command\Annotation::class,
            \think\annotation\command\Handler::class
        ],
    ];
    ```
- 生成注解类（annotation）

  命令行操作：例如生成一个 User 类注解
  
  ```php
  php think make:annotation User
  ```
  
  生成完成的注解类，如下：
  
  ```php
  <?php
  declare (strict_types = 1);
  
  namespace app\annotation;
  
  use Doctrine\Common\Annotations\Annotation;
  
  /**
   * class User
   * @package app\annotation
   * @Annotation
   * @Target({"METHOD","CLASS"}) # 不需要进行类注解去掉"CLASS"，不需要方法注解去掉"METHOD"
   */
  class User extends Annotation
  {
      // TODO 完成你对注解字段的定义
  }
  ```
  
  > 只需要完成 TODO 位置对注解参数的定义,可以参考TP6自带的注解类

- 生成注解处理器类（handler）

  命令行操作：例如生成一个 User 类注解处理器类
    
  ```php
  php think make:handler User
  ```
    
  生成完成的注解类，如下：
  
  ```php
  <?php
  declare (strict_types = 1);
  
  namespace app\annotation\handler;
  
  use Doctrine\Common\Annotations\Annotation;
  use think\annotation\handler\Handler;
  
  class User extends Handler
  {
      public function cls(\ReflectionClass $refClass, Annotation $annotation, \think\Route &$route)
      {
          // TODO: 完成类注解的操作
      }
  
      public function func(\ReflectionMethod $refMethod, Annotation $annotation, \think\route\RuleItem &$rule)
      {
          // TODO: 完成方法注解的操作
      }
  }
  ```
  > 只需要完成 TODO 注解的解释进行操作处理哦, `cls()`方法是针对注解类`@Target()`中包含`CLASS`时才会被调用
  > ,`func()`方法是针对注解类`@Target()`中包含`METHOD`时才会被调用。

  参数说明：
  
  |  参数名   | 简介  |
  |  ----  | ----  |
  | $refClass  | `\ReflectionClass` 当前注解类的反射对象 |
  | $annotation  | 当前类注解的参数对象（`cls和func的annotation一样`） |
  | $route  | 当前类的`TP6` `\think\Route`路由对象 |
  | $refMethod  | `\ReflectionMethod` 当前注解类访问方法的反射对象 |
  | $rule  | 当前方法的`TP6` `\think\route\RuleItem` 路由规则对象 |

  > 上述只是对参数做的阐述，实际参数意义和作用具体看自身业务。

IDE Support
-----------

Some IDEs already provide support for annotations:

- Eclipse via the Symfony2 Plugin <http://symfony.dubture.com/>
- PHPStorm via the PHP Annotations Plugin <http://plugins.jetbrains.com/plugin/7320> or the Symfony2 Plugin <http://plugins.jetbrains.com/plugin/7219>

