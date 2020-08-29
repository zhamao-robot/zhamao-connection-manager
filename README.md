# zhamao-connection-manager 
一个基于 Swoole\Table 的简易 Websocket/HTTP 连接属性扩充管理器，兼容 Swoole 多进程的。

A swoole-based multi-process connection manager using shared memory table

## 用法
1.在 `Swoole\Server->start()` 前执行初始化：
```php
$result = ZM\ConnectionManager\ManagerGM::init(1024);
```
参数如下：
- `$max_conn`: (必填) 设置服务器最大接收的连接属性储存的数目
- `$conflict_proportion`: (选填) hash 冲突的最大百分比 (默认 `0.2`)，有关此参数，见 [Swoole 文档](https://wiki.swoole.com/#/memory/table?id=__construct)。
- `$options`: (选填) 自定义属性参数的键名参数列表，用于添加自定义字段 (默认为空数组)

以下是带丰富参数的用例初始化方式：
```php

$result = ZM\ConnectionManager\ManagerGM::init(1024, 0.2, [
    [
        "key" => 'connectToken',
        "type" => 'string',
        "size" => 128
    ]
]);
```

> 参数初始化的必填字段为 `key`，`type`，其中 `type` 限定为以下类型：`string`，`int`，`float`，`int1`，`int2`，`int8`
> 
> `size` 属性在 `type` 为 `string` 时必须填写，为字符串的最大长度。储存过程中如果字符串输入过长，会自动截断。

2.在 Swoole 服务器的 `onOpen` 或 `onRequest` 事件最开始执行的代码：
```php
use Swoole\Http\Request;
function onOpen($server, Request $request) {
    ZM\ConnectionManager\ManagerGM::pushConnect($request->fd);
}
function onRequest(Request $request, $response) {
    ZM\ConnectionManager\ManagerGM::pushConnect($request->fd);
}
```
参数如下：
- `$fd`: (必填) 设置服务器最大接收的连接属性储存的数目
- `$name`: (选填) 连接对象的类型名称 (默认 `default`)
- `$options`: (选填) 自定义属性参数的键名参数列表，用于添加和设置自定义字段 (默认为空数组)

高级用例：
```php
$fd = 3;
ZM\ConnectionManager\ManagerGM::pushConnect($fd, 'wechatBot', ["connectToken" => 'abcde']);
```

3.在 Swoole 服务器的 `onClose` 事件执行的代码（销毁连接对象）：
```php
function onClose($server, $fd) {
    ZM\ConnectionManager\ManagerGM::popConnect($fd);
}
```

4.在 Swoole 服务器的 `message` 或 `request` 事件下可以用的方法：
```php
$fd = 4;// 从 Frame 或 Request 对象中获取的 $->fd 值。
$obj = ZM\ConnectionManager\ManagerGM::get($fd);
if($obj === null) {
    // 如果 fd 对应的连接不存在，则会返回 null
    echo "连接不存在！" . PHP_EOL;
}
// 函数返回一个 `ConnectionObject` 对象，用于提取数据。
echo $obj->getFd(); // 返回 fd 连接标识符
echo $obj->getName(); // 返回连接类型名称
echo $obj->getOption("connectToken"); // 返回自定义字段的数据
echo $obj->getOptions(); // 返回所有自定义字段的数据
// 对象也可以直接操作改变内容
$obj->setName("qqBot"); // 改变连接的类型名称
$obj->setOption("connectToken", "qwerty"); //改变自定义字段的数据

// 此方法可以获取此类型名称下所有连接的 `ConnectionObject` 对象数组
$conns = ZM\ConnectionManager\ManagerGM::getAllByName("wechatBot");
```
