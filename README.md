## Tp Mailer
基于 SwiftMailer 二次开发, 为 ThinkPHP系列框架量身定制, 使 ThinkPHP 支持邮件模板、纯文本、附件邮件发送以及更多邮件功能, 邮件发送简单到只需一行代码

同时了方便其他框架或者非框架使用, Tp Mailer也非常容易拓展融合到其他框架中, 欢迎大家 `Fork` 和 `Star`, 提交代码让Tp Mailer支持更多框架

## 目录 
* [优雅的发送邮件](#优雅的发送邮件) 
* [安装](#安装) 
    * [使用 Composer 安装 (强烈推荐)](#使用-composer-安装-强烈推荐)
    * [github下载 或 直接手动下载源码](#github下载-或-直接手动下载源码)
        * [下载文件](#下载文件)
        * [移动文件夹](#移动文件夹)
        * [引入自动载入文件](#引入自动载入文件)
* [配置](#配置) 
* [使用](#使用) 
    * [使用Tp Mailer](#使用tp-mailer)
    * [创建实例](#创建实例)
    * [设置收件人](#设置收件人)
    * [设置发件人](#设置发件人)
    * [设置邮件主题](#设置邮件主题)
    * [设置邮件内容 - HTML](#设置邮件内容---html)
    * [设置邮件内容 - 纯文本](#设置邮件内容---纯文本)
    * [设置邮件内容 - 模板](#设置邮件内容---模板)
    * [设将图片作为元数据嵌入到邮件中](#将图片作为元数据嵌入到邮件中)
        * [配置嵌入标签](#配置嵌入标签)
        * [模板或HTML中设置变量](#模板或html中设置变量)
        * [传递变量参数和值](#传递变量参数和值)
        * [示例](#示例)
    * [添加附件](#添加附件)
    * [设置消息加密/签名](#设置消息加密签名)
    * [设置字符编码](#设置字符编码)
    * [设置邮件最大长度](#设置邮件最大长度)
    * [设置邮件优先级](#设置邮件优先级)
    * [Requesting a Read Receipt](#requesting-a-read-receipt)
    * [注册插件](#注册插件)
    * [发送邮件](#发送邮件)
* [其他框架扩展](#其他框架扩展)
    * [第一件事: 实现 `$mailer->view()` 方法](#第一件事-实现-mailer-view-方法)
    * [第二件事: 添加自己框架的配置读取方法](#第二件事-添加自己框架的配置读取方法)
* [中文文件名乱码问题](#中文文件名乱码问题)
* [Issues](#issues)
* [License](#license)


## 优雅的发送邮件
**ThinkPHP5 示例**
```
use mailer\tp5\Mailer;

$mailer = Mailer::instance();
$mailer->from('tianpian0805@gmail.com', 'yuan1994')
    ->to('your-mail@domain.com')
    ->subject('纯文本测试')
    ->text('欢迎您使用Tp Mailer')
    ->send();
```
你也可以这样: **ThinkPHP3.2.3 示例**
```
require_once '/path/to/tp-mailer/src/autoload.php';

use mailer\tp32\Mailer;

$mailer = Mailer::instance();
$mailer->from('tianpian0805@gmail.com', 'yuan1994')
    ->to('your-mail@domain.com')
    ->subject('多行文本测试')
    ->line('PHPer们: ')
    ->line('欢迎你们使用Tp Mailer, 如果使用感觉很方面请给个Star, 也欢迎大家Fork帮忙完善')
    ->line()
    ->line('yuan1994 <tianpian0805@gmail.com ' . date('Y-m-d'))
    ->attach('/path/to/文件名.pdf', '自定义文件名.pdf')
    ->send();
```
你还可以这样: **ThinkPHP3.1.3 示例**
```
require_once '/path/to/tp-mailer/src/autoload.php';

use mailer\tp31\Mailer;

$mailer = Mailer::instance();
$mailer->send(function($mailer, $message) {
    $mailer->to('tianpian0805@gmail.com')
        ->subject('使用框架模板引擎渲染模板测试')
        ->view('mail:test', array(
            'param1' => '参数1',
            'param2' => '参数2',
            'param3' => '参数3'
        ));
});
```


## 安装
### 使用 Composer 安装 (强烈推荐):
支持 `psr-4` 规范, 开箱即用
```
composer require yuan1994/tp-mailer
```

### github下载 或 直接手动下载源码:
需手动引入自动载入文件

#### 下载文件:
git clone https://github.com/yuan1994/tp-mailer tp-mailer

git clone https://github.com/swiftmailer/swiftmailer swiftmailer

或者点击直接下载:

[https://github.com/yuan1994/tp-mailer/archive/master.zip](https://github.com/yuan1994/tp-mailer/archive/master.zip)

[https://github.com/swiftmailer/swiftmailer/archive/5.x.zip](https://github.com/swiftmailer/swiftmailer/archive/5.x.zip)

#### 移动文件夹:
然后将两个项目分别手动命名为 `tp-mailer` 和 `swiftmailer`, 放在自己项目的扩展类库文件夹里, 这两个文件夹必须在同一目录, 目录结构大概如下所示:
```
扩展目录
├── tp-mailer
│   └── src
├── swiftmailer
│   ├── lib
│   ├── doc
│   └── tests
```

#### 引入自动载入文件:
使用时引入或者全局自动引入

`require_once '/path/to/tp-mailer/src/autoload.php`;


## 配置
在配置文件里配置如下信息, 可以配置在 `mail.php` 或 `config.php` 文件中, 但要保证能通过 `mail.driver`, `mail.host` 访问到配置信息, 内容如下:
```
return [
        'driver'          => 'smtp', // 邮件驱动, 支持 smtp|sendmail|mail 三种驱动
        'host'            => 'smtp.qq.com', // SMTP服务器地址
        'port'            => 465, // SMTP服务器端口号,一般为25
        'addr'            => '', // 发件邮箱地址
        'pass'            => '', // 发件邮箱密码
        'name'            => '', // 发件邮箱名称
        'content_type'    => 'text/html', // 默认文本内容 text/html|text/plain
        'charset'         => 'utf-8', // 默认字符集
        'security'        => 'ssl', // 加密方式 null|ssl|tls, QQ邮箱必须使用ssl
        'sendmail'        => '/usr/sbin/sendmail -bs', // 不适用 sendmail 驱动不需要配置
        'debug'           => true, // 开启debug模式会直接抛出异常, 记录邮件发送日志
        'left_delimiter'  => '{', // 模板变量替换左定界符, 可选, 默认为 {
        'right_delimiter' => '}', // 模板变量替换右定界符, 可选, 默认为 }
        'log_driver'      => '', // 日志驱动类, 可选, 如果启用必须实现静态 public static function write($content, $level = 'debug') 方法
        'log_path'        => '\\mailer\\Log', // 日志路径, 可选, 不配置日志驱动时启用默认日志驱动, 默认路径是 /path/to/tp-mailer/log, 要保证该目录有可写权限, 最好配置自己的日志路径
        'embed'           => 'embed:', // 邮件中嵌入图片元数据标记
];
```


## 使用
> 以下使用及方法兼容所有框架, 包括 ThinkPHP5, ThinkPHP3.2, ThinkPHP3.1, 唯一有所区别的是 ThinkPHP3.2 和 ThinkPHP3.1 不支持composer自动载入, 需手动引入自动载入文件, 使用时引入或者全局自动引入:

> `require_once '/path/to/tp-mailer/src/autoload.php';`

> 使用use时, ThinkPHP5 的Mailer类的命名空间是 `mailer/tp5/Mailer`, ThinkPHP3.2 的命名空间是 `mailer/tp32/Mailer`, ThinkPHP3.1 的命名空间是  `mailer/tp31/Mailer`

以下示例以 ThinkPHP5 里使用为例, 其他框架完全一样

### 使用Tp Mailer
```
// 不支持自动载入的框架请手动引入自动载入文件
// require_once '/path/to/tp-mailer/src/autoload.php';

use mailer\tp5\Mailer
```

### 创建实例
不传递任何参数表示邮件驱动使用配置文件里默认的配置
```
$mailer = Mailer::instance();
```
如果你想实例化时不使用配置文件里的默认配置, 你可以这样:
```
$mailer = Mailer::instance(function() {
    return \Swift_SmtpTransport::newInstance(
            'host', 'port', 'security' 
        )
        ->setUsername($config['addr'])
        ->setPassword($config['pass']);
});
```
匿名必须返回一个 `\Swift_SmtpTransport` 或 `\Swift_SendmailTransport` 或 `\Swift_MailTransport`, 详细配置请参考 [SwiftMailer Transport Types](http://swiftmailer.org/docs/sending.html#transport-types)

你也可以直接手动传入一个现有的邮件驱动, 配置使用默认配置, 像这样:
```
$mailer = Mailer::instance('sendmail');
```

### 设置收件人
以下几种方式任选一种
```
$mailer->to(['tianpian0805@gmail.com']);
$mailer->to(['tianpian0805@gmail.com' => 'yuan1994']);
$mailer->to('tianpian0805@gmail.com', 'yuan1994');
$mailer->to(['tianpian0805@qq.com', 'tianpian0805@gmail.com' => 'yuan1994']);
$mailer->to(['tianpian0805@qq.com', 'tianpian0805@gmail.com', 'tianpian0805@163.com']);
```

### 设置发件人
发件人邮箱地址必须和配置项里一致, 默认会自动设置发件地址 (配置里的addr) 和发件人 (配置里的name)
```
$mailer->from('tianpian0805@gmail.com', 'yuan1994');
$mailer->from(['tianpian0805@gmail.com' => 'yuan1994']);
```

### 设置邮件主题
```
$mailer->subject('邮件主题');
```

### 设置邮件内容 - HTML
```
$mailer->html('<p>欢迎使用Tp Mailer</p>');
```

或者使用变量替换HTML内容
```
$mailer->html('<p>欢迎使用{name}</p>', ['name' => 'Tp Mailer']);
```

### 设置邮件内容 - 纯文本
```
$mailer->text('欢迎使用Tp Mailer');
```

还有另外一个用法完全相同的同名方法
```
$mailer->raw('欢迎使用Tp Mailer');
```

或者使用变量替换纯文本内容
```
$mailer->text('欢迎使用{name}', ['name' => 'Tp Mailer']);
```

你也可以很方便的设置多行文本, 直接回车换行或者使用 `line()` 方法, 支持多次调用
```
$mailer->line('尊敬的 访客: ');
$mailer->line('   欢迎您使用Tp Mailer');
$mailer->line(); // 不传值默认输出空行
$mailer->line('yuan1994 ' . date('Y-m-d') );
// 以上历程输出
/***************
尊敬的 访客: 
   欢迎您使用Tp Mailer
   
yuan1994 2016-12-01
****************/
```

`line()` 也支持使用变量替换纯文本内容
```
$mailer->text('欢迎使用{name}', ['name' => 'Tp Mailer']);
```

### 设置邮件内容 - 模板
ThinkPHP系列模板, 具体请看ThinkPHP各版本框架的模板怎么用, 第二个参数是要进行模板赋值的数组
```
$mailer->view('mail/register');
$mailer->view('admin@mail/register', ['account' => $account, 'name' => $name]);
```

### 将图片作为元数据嵌入到邮件中
邮件内容中包含图片的, 可以直接指定 `img` 标签的 `src` 属性为远程图片地址, 此处图片地址必须为远程图片地址, 必须为一个带域名的完整图片链接, 这似乎很麻烦, 所以你还可以将图片作为元数据嵌入到邮件中, 至于其他文件是否也可以嵌入请自己尝试, 详情请参考 [SwiftMailer Embedding Inline Media Files](http://swiftmailer.org/docs/messages.html#embedding-inline-media-files)

下面介绍一下 `tp-mailer` 如何快速简便的将图片元数据嵌入到邮件中:

#### 配置嵌入标签
嵌入元数据需要在模板赋值或者使用 `html()` 传递变量时, 给变量添加特殊的标签, 该嵌入标签默认为 `embed:`, 你可以修改配置文件中 `embed` 项, 修改为你想要的形式

#### 模板或HTML中设置变量
在模板中, 例如 ThinkPHP 全系列都是使用 `{$var}` 的形式传递变量, 假设变量为 `image_src`, 那么模板中填写 `{$image_src}`, 如果是在HTML中, 请使用 `{image_src}`, 注意如果修改过左、右定界符请使用自己定义的左右定界符

#### 传递变量参数和值
在 `html()` 和 `view()` 方法的第二个参数里, 该数组必须有一个变量, 格式为 `['embed:image_src'] => '/path/to/image.jpg']` 或者 `['embed:image_src'] => ['file_stream', 'filemime', 'filename']]`, 即参数数组的键名是上面配置的 `嵌入标签 + 变量名`, 但值有两种情况:

第一, 如果值为字符串, 则该值为图片的路径 (绝对路径或相对路径) 或者 有效的url地址;

第二, 如果值为数组, 数组为 `['stream', 'mime', 'name']` 的形式, 其中 `stream` 表示图片的数据流, 即是未保存的文件数据流, 例如 `file_get_contents()` 方法获取的文件数据流, 第二个参数可选, 为文件的mime类型, 默认为 `image/jpeg`, 第三个参数为文件名, 默认为 `image.jpg`

#### 示例
```
Mailer::instance()
    ->to('tianpian0805@gmail.com', 'yuan1994') 
    ->subject('测试邮件模板中嵌入图片元数据')
    ->view('index@mail/index', [
        'date' => date('Y-m-d H:i:s'),     
        'embed:image' => ROOT_PATH . 'image.jpg',
        // 'embed:image' => 'http://image34.360doc.com/DownloadImg/2011/08/2222/16275597_64.jpg',
        // 'embed:image' => [file_get_contents(ROOT_PATH . 'image1.jpg')],
        // 'embed:image' => [file_get_contents(ROOT_PATH . 'image1.jpg', 'image/png', '图片.png')],
     ])
    ->send();
```
其中模板的内容如下:
```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>测试邮件</title>
</head>
<body>
<p>尊敬的yuan1994:</p>
<p>     这是一封模板测试邮件</p>
<p>{$date}</p>
<p>
    <img src="{$image}" alt="">
</p>
</body>
</html>
```
在 HTML 中使用一样:
```
Mailer::instance()
    ->to('tianpian0805@gmail.com', 'yuan1994') 
    ->subject('测试邮件模板中嵌入图片元数据')
    ->html('<img src="{image}" />图片测试', [
        'embed:image' => ROOT_PATH . 'image.jpg',
        // 'embed:image' => 'http://image34.360doc.com/DownloadImg/2011/08/2222/16275597_64.jpg',
        // 'embed:image' => [file_get_contents(ROOT_PATH . 'image1.jpg')],
        // 'embed:image' => [file_get_contents(ROOT_PATH . 'image1.jpg', 'image/png', '图片.png')],
     ])
    ->send();
```


### 添加附件
已修复SwiftMailer设置中文文件名出现乱码的bug
```
$mailer->attach('http://domain.com/path/to/file.ext');
```

或者指定附件的文件名
```
$mailer->attach(ROOT_PATH . 'foo.ext', '文件名.pdf');
```

使用匿名函数 $attachment用法请参考 [SwiftMailer Attaching Files](http://swiftmailer.org/docs/messages.html#attaching-files)
```
$mailer->attach(ROOT_PATH . 'foo.ext', function($attachment, $mailer) {
    $attachment->setFilename($mailer->cnEncode('文件名.jpg'));
});
```

### 设置消息加密/签名
使用方法请参考 [SwiftMailer Signed/Encrypted Message](http://swiftmailer.org/docs/messages.html#signed-encrypted-message)
```
$mailer->signCertificate(function() {
    // $signer->setSignCertificate('/path/to/certificate.pem', '/path/to/private-key.pem');
    // $signer->setSignCertificate('/path/to/certificate.pem', array('/path/to/private-key.pem', 'passphrase'));
    // $signer->setSignCertificate('/path/to/certificate.pem', '/path/to/private-key.pem', PKCS7_BINARY);
    
    $smimeSigner->setSignCertificate('/path/to/sign-certificate.pem', '/path/to/private-key.pem');
    $smimeSigner->setEncryptCertificate('/path/to/encrypt-certificate.pem');
});
```

### 设置字符编码
```
$mailer->charset('utf8');
```

### 设置邮件最大长度
```
$mailer->lineLength(1000);
```

### 设置邮件优先级
```
$mailer->priority(MailerConfig::PRIORITY_HIGHEST);
// 可选值有: 
// MailerConfig::PRIORITY_HIGHEST
// MailerConfig::PRIORITY_HIGH
// MailerConfig::PRIORITY_NORMAL
// MailerConfig::PRIORITY_LOW
// MailerConfig::PRIORITY_LOWEST
```
> `MailerConfig` 的完整命名空间为 `mailer/lib/MailerConfig`

### Requesting a Read Receipt
```
$mailer->readReceiptTo('tianpian0805@gamil.com');
```

### 注册插件
```
// Use AntiFlood to re-connect after 100 emails
$mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(100));

// And specify a time in seconds to pause for (30 secs)
$mailer->registerPlugin(new Swift_Plugins_AntiFloodPlugin(100, 30));
```
插件的详细使用请参考 [SwiftMailer Plugins](http://swiftmailer.org/docs/plugins.html)

### 发送邮件
```
$mailer->send();
```

使用匿名函数, $mailer是 `Mailer` 对象, $message是 `Swift_Message` 对象
```
$mailer->send(function ($mailer, $message) {
    $mailer->to('tianpian0805@gmail.com')
        ->line('你好')
        ->line('这是一封测试邮件')
        ->subject('测试邮件');
});
```
第二个参数也可以是`匿名函数` 或 `字符串` 或 `null`, 用户指定发送邮件使用的驱动, 详细使用请参考本文档的 [创建实例](#创建实例)
```
$mailer->send(
    function ($mailer, $message) {
        $mailer->to('tianpian0805@gmail.com')
            ->line('你好')
            ->line('这是一封测试邮件')
            ->subject('测试邮件');
            },
    function () {
        return \Swift_SmtpTransport::newInstance(
                    'host', 'port', 'security' 
                )
                ->setUsername($config['addr'])
                ->setPassword($config['pass']);
    });
```

如果你使用了插件, 你还可以使用第三个参数, 第三个参数只能是匿名函数:
```
$mailer->send(
    function ($mailer, $message) {
        $mailer->to('tianpian0805@gmail.com')
            ->line('你好')
            ->line('这是一封测试邮件')
            ->subject('测试邮件');
            },
    null,
    function ($swiftMailer, $mailer) use ($lotsOfRecipients {
        // Continue sending as normal
        for ($lotsOfRecipients as $recipient) {
          // $swiftMailer->send($mailer->message, $fails);
          $swiftMailer->send($mailer->message);
        }
    });
```
第三个参数匿名函数里必须使用 `$swiftMailer` 调用 `send()` 方法发送邮件, 并且第一个参数必须为 `$mailer->message`, 插件的详细使用请参考 [SwiftMailer Plugins](http://swiftmailer.org/docs/plugins.html)

除使用插件, `send()` 方法第三个参数为匿名函数的情况外, 发送邮件的返回值为发送成功用户的数字, 全部失败为0, 全部成功为设置收件人的数量

以上所有方法 (除最后发送的方法 `send()`) 都支持连贯调用
```
$mailer->to('tianpian0805@gmail.com')
    ->subject('邮件主题')
    ->text('邮件内容')
    ->send();
```

如果执行过邮件发送过邮件发送之后, 需要重新初始化
```
// 第一次发送
$mailer->to('tianpian0805@gmail.com')
    ->subject('邮件主题')
    ->text('邮件内容')
    ->send();
    
// 接着进行第二次发送
$mailer->init();
// 或者直接连贯调用
$mailer->init()->to()->...->send();
```

开启 `debug` 模式后, 邮件发送失败会直接以异常抛出, 如果没有开启, 可以通过 `getError()` 获取错误信息
```
$mailer->getError();
```

如果有邮件发送失败, 可以通过 `getFails()` 获取发送失败邮件地址的列表, 邮件发送时第三个参数使用哦匿名函数的情况除外, 这种情况获取到的值为空, 需调用 `$swiftMailer->send()` 时第二个参数手动指定 `fails`
```
$mailer->getFails();
// 如果没有数据将输出空数组, 如果有数据将返回邮件列表数组, 例如
// array('example1@damain.com', 'example2@damain.com')
```

使用 `getHeaders()` 和 `getHeadersString()` 方法可以获取头信息
`getHeaders()` 返回的是头信息数组, `getHeadersString()` 返回的是头信息字符串

更多文档请参考 [SwiftMailer](http://swiftmailer.org/docs/)


## 其他框架扩展
其他框架扩展只需做两件事, 部署安装使用和文档一样

### 第一件事: 实现 `$mailer->view()` 方法
写自己的类继承 `mailer\lib\Mailer`, 然后实现里面的 `view` 方法, 根据自己的框架渲染出自己的模板:
```

    /**
     * 载入一个模板作为邮件内容
     *
     * 实现$template指定模板路径, $param设置模板替换参数, 最后能正常渲染出HTML
     *
     * @param string $template
     * @param array $param
     * @param array $config
     * @return Mailer
     */
    abstract public function view($template, $param = [], $config = []);
```
详细写法请参考 `mailer\tp5\Mailer` 和 `mailer\tp32\Mailer` 等已有类库的写法

### 第二件事: 添加自己框架的配置读取方法
修改 `mailer\lib\Config` 方法, 在下面添加自己框架读取配置的类, 具体要实现的方法请参考 `mailer\lib\tp3\Config`

OK, 此时你就能在你的框架中使用 Tp Mailer 了, 如果你还想做一件事 - Fork && Pull, 那就更好, 希望能一起完善 Tp Mailer


## 中文文件名乱码问题
经测试给邮件添加附件时如果附件时中文名会乱码, 如果添加附件时使用匿名闭包函数, 设置文件名时一定要使用 `cnEncode()` 方法对文件名进行处理, 否则收到的邮件中中文名会乱码, 其他的添加附件方法都在代码里默认调用了 `cnEncode()` 方法
```
// 以下两种形式会自动对文件名进行处理防止乱码
$mailer->attach('http://domain.com/path/to/file.ext');
$mailer->attach(ROOT_PATH . 'foo.ext', '文件名.pdf');

// 使用匿名函数时需手动对中文文件名进行处理
$mailer->attach(ROOT_PATH . 'foo.ext', function($attachment, $mailer) {
    $attachment->setFilename($mailer->cnEncode('文件名.jpg'));
}
```


## Issues
如果有遇到问题请提交 [issues](https://github.com/yuan1994/tp-mailer/issues)


## License
Apache 2.0
