# PHP 方法链式调用代理

[![Latest Version on Packagist](https://img.shields.io/packagist/v/liyuze/method-chaining-proxy.svg?style=flat-square)](https://packagist.org/packages/liyuze/method-chaining-proxy)
[![Total Downloads](https://img.shields.io/packagist/dt/liyuze/method-chaining-proxy.svg?style=flat-square)](https://packagist.org/packages/liyuze/method-chaining-proxy)
[![PHP Composer](https://github.com/liyuze/method-chaining-proxy/actions/workflows/php.yml/badge.svg?branch=main&event=push)](https://github.com/liyuze/method-chaining-proxy/actions/workflows/php.yml)

流式接口（fluent interface）是软件工程中面向对象API的一种实现方式，以提供更为可读的源代码。方法链式调用（method chaining）是流式接口的一种实现。
在 PHP 中的实现方式是在类的方法返回值中返回 `$this` 或 `new static()` 来实现方法链式调用。

本扩展包尝试解决：

1. 传统返回值（无 `return` 或有 `return` 的值非本类对象）下实现链式调用。
2. 通过"强制"忽略（tap 模式）或关注（pipe 模式）返回值实现更加灵活的链式调用。
3. 优化控制语句（if/switch/foreach/while）对链式调用在代码层级中断。

> 关于代码提示：PHPStorm [暂不支持在@template 时同时使用 @mixin 语句](https://youtrack.jetbrains.com/issue/WI-64022/Support-mixin-with-template)
，所以无法实现代码自动提示功能。如果你有更好实现代码提示的方法，欢迎与我联系~😃
> ！！！过度使用本包可能影响代码的可读性和增加程序调试难度，建议在简单的逻辑中使用。

## 灵感

灵感来源于 laravel 的 `tap` 和 `pipe` 方法。

```php 
/**
 * Call the given Closure with the given value then return the value.
 *
 * @param  mixed  $value
 * @param  callable|null  $callback
 * @return mixed
 */
function tap($value, $callback = null)
{
    if (is_null($callback)) {
        return new HigherOrderTapProxy($value);
    }

    $callback($value);

    return $value;
}
```

```php
namespace Illuminate\Support\Traits;

trait EnumeratesValues
{
    ...
    /**
     * Pass the collection to the given callback and return the result.
     *
     * @param  callable  $callback
     * @return mixed
     */
    public function pipe(callable $callback)
    {
        return $callback($this);
    }
    ...
}

```

## 安装

你可以通过 composer 进行安装:

```bash
composer require liyuze/method-chaining-proxy
```

## 用例

### 快速创建代理器

#### mixed 代理器

创建代理器

```php
$cat = new Cat('Tom', 5);
$proxy = MethodChainingFactory::create($cat);
//或
$proxy = MethodChainingFactory::mixed($cat);
```

默认创建的是 `mixedMode` 的代理器，特点是如果调用的方法**没有返回值或返回值是`null`**时不会更新代理器的代理值。

```php
$proxy = $proxy->setAge(9)->setName('Tony')->getName();
$proxy->popValue(); //Tony
$cat->getName();    //Tony
```

#### tap 模式代理器

也可以通过 `tapMode()` 方法创建一个**忽略任何返回值**，代理器值一直不变的值代理器。

```php
$cat = new Cat('Tom', 5);
$proxy = MethodChainingFactory::tap($cat);
$proxy = $proxy->setAge(9)->setName('Tony')->getName();
$proxy->popValue(); //Cat('Tony', 9)
$cat->getName();    //Tony
```

#### pipe 模式代理器

也可以通过 `pipeMode()` 方法创建一个接受**任何返回值**代理器。

```php
$cat = new Cat('Tom', 5);
$proxy = MethodChainingFactory::pipe($cat);
$proxy = $proxy->setAge(9);
$proxy->popValue(); //null
$cat->getName();    //Tony
```

### 切换代理器模式

可以通过 'switchMixedMode()'、'switchTapMode()'、'switchPipeMode()' 方法来切换代理器的代理模式。

```php
$cat = new Cat('Tom', 5);
$proxy = MethodChainingFactory::tap($cat);
$proxy = $proxy->switchToPipeMode()->getName();;
$proxy->popValue(); //Tom
$cat->getName();    //Tom
```

> 可以使用 `mixed`、`tap`、`pipe` 属性访问方式来调用对应的切换代理方法。

如果临时切换代理模式的情况，可以通过'tapOnce()'、'pipeOnce()'制定，所制定的调用方法仅会生效一次。

```php
$cat = new Cat('Tom', 5);
$proxy = MethodChainingFactory::tap($cat);
$proxy = $proxy->pipeOnce()->getName()
$proxy->popValue(); //Tom
$cat->getName();    //Tom
```

> 可以使用 `tapOnce`、`pipeOnce` 属性访问方式来调用对应的单次切换代理方法。

### 摘取

可以通过 `pick()` 方法在方法链式调用过程中通过引用传值的方法获取某个属性值。

```php
$cat = new Cat('Tom', 5);
$name = null;
$proxy = MethodChainingFactory::create($cat);
$proxy->setAge(9)->pick('name', $name)->setName('Tony');
$name; //Tom
$cat->getName();    //Tony
```

对于方法的值，可以通过 `methodPick()` 来摘取。

```php
$cat = new Cat('Tom', 5);
$name = null;
$proxy = MethodChainingFactory::create($cat);
$proxy = $proxy->setAge(9)->methodPick($name, 'getName')->setName('Tony');;
$name; //Tom
$cat->getName();    //Tony
```

### 后置操作

通过调用 `after()` 方法传入一个闭包来执行自定义的代码功能，它也支持链式调用。

闭包的第一个参数是当前代理器 `代理值`，如果闭包有返回值且不为null时将会更新代理器的代理值。

```php
$cat = new Cat('Tom', 5);
$birthMonth = 3;
$proxy = MethodChainingFactory::create($cat);
$proxy = $proxy->setAge(9)->setName('Tony')->after(
    function ($proxyValue) use ($birthMonth) {
        //6月前出生的加1岁
        if ($birthMonth < 6) {
            $proxyValue->setAge($proxyValue->getAge() + 1);
        }
    });//->after(...)->after(...);
    
$proxy->popValue()->getAge();   // 10


$number = MethodChainingFactory::create($cat)->after(fn () => 3)->popValue();
// 3
```

### If 逻辑代理器

当给定的判定值为真时才会运行后续方法链的代理器。

```php
$ifProxy = ControlChainingFactory::if(new Cat('Tom', 5), false);
// 或 $ifProxy = new IfChainingProxy(new Cat('Tom', 5), false);
$ifProxy->setName('Tony')
    ->elseChaining()
    ->setName('Alan');

$ifProxy->endSwitchChaining()->getName();   //Alan
```

也可以通过 `ControlChainingFactory::unless()` 创建一个判定值为假时才会运行后续方法链的代理器。

### Switch  逻辑代理器

当给定的判定值与 `caseChaining()` 传入的值相等时才会运行后续方法链的代理器。

```php
$switchProxy = ControlChainingFactory::switch(new Cat('Tom', 5), 2);
// 或 $switchProxy = new SwitchChainingProxy(new Cat('Tom', 5), 2);
$cat = $switchProxy
    ->caseChaining(1)->setName('Tony')
    ->caseChaining(2)->setName('Alan')
    ->endSwitchChaining();
        
$cat->getName();   //Alan
$cat->getAge();   //10
```

```php
 $switchProxy = ControlChainingFactory::switch(new Cat('Tom', 5), 2);
// 或 $switchProxy = new SwitchChainingProxy(new Cat('Tom', 5), 2);
$cat = $switchProxy
    ->caseChaining(1)->setName('Tony')->breakChaining()
    ->caseChaining(2)->setName('Alan')
    ->caseChaining(2)->setAge(10)->breakChaining()
    ->caseChaining(2)->setName('Andy')
    ->endSwitchChaining();
    
$cat->getName();   //Alan
$cat->getAge();   //10
```

### 示例类

```php
class Cat
{
    public string $name;
    public int $age;

    public function __construct(string $name, int $age)
    {
        $this->name = $name;
        $this->age = $age;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAge()
    {
        return $this->age;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function setAge(int $age)
    {
        $this->age = $age;
    }
    
    public function setNameForChaining(string $name)
    {
        $this->name = $name;
        return $this;
    }

    public function setAgeForChaining(int $age)
    {
        $this->age = $age;
        return $this;
    }

    public function do(\Closure $closure)
    {
        $closure();

        return $this;
    }
}
```

### 测试

```bash
composer test
```

### 修改记录

点击 [CHANGELOG](CHANGELOG.md) 查看最近修改了哪些内容。

## 贡献

点击 [CONTRIBUTING](CONTRIBUTING.md) 查看详情

### 安全

如果您发现任何与安全相关的问题，请发送电子邮件290315384@qq.com而不是使用问题追踪器。

## 贡献值

- [Yuze Li](https://github.com/liyuze)
- [All Contributors](../../contributors)

## 开源协议

The MIT License (MIT)。点击 [License File](LICENSE.md) 查看更多信息。

## PHP Package 模板

本扩展包使用 [PHP Package Boilerplate](https://laravelpackageboilerplate.com) 工具生成，该工具由 [Beyond Code](http://beyondco.de/) 提供。
