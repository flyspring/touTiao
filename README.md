<h1 align="left"><a href="#">字节跳动 今日头条 byteDance touTiao SDK</a></h1>

📦 字节跳动PHP SDK 抖音小程序、头条小程序开发组件。PHP SDK for bytedance (douyin, tiktok, toutiao)


## Requirement

1. PHP >= 7.1
2. **[Composer](https://getcomposer.org/)**
3. openssl 拓展


## Installation

```shell
$ composer require "flyspring/toutiao" -vvv
```

## Usage

基本使用（以服务端为例），所有例子参见examples文件夹:

```php
use Spring\TouTiao\Application as MiniProgram;

$config = require '../src/config/toutiao.php';
if (empty($config)) {
    echo 'config is empty';
    exit;
}

/**
 * 
 * @var MiniProgram $miniProgram
 */
$miniProgram = new MiniProgram($config);

// code2session
$ret = $miniProgram->auth->session('');

var_dump($ret);
```


## Documentation

Coming soon

## Integration

Laravel 5 集成，参见代码ServiceProvider.php

## Contributors


## License

MIT

## Special Thanks
[@overtrue](https://github.com/overtrue)
