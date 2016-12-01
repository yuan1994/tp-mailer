## Tp5 Mailer
基于 SwiftMailer 二次开发, 为 ThinkPHP5 量身定制, 使 ThinkPHP5 支持邮件模板、纯文本、附件邮件发送, 邮件发送只是简单到只需一行代码

## 优雅的发送邮件
```
use tp5\Mailer;

$mailer = Mailer::instance();
$mailer->from('tianpian0805@gmail.com', 'yuan1994')
    ->to('your-mail@domain.com')
    ->subject('欢迎使用Tp5 Mailer')
    ->text('欢迎您使用Tp5 Mailer')
    ->send();
```

## 安装
使用 Composer 安装:
```
composer require yuan1994/tp5-mailer
```

## 配置
请在 `application` 目录下或者模块目录下的 `extra` 文件夹里添加 `mail.php` 配置文件, 内容如下:
```
return [
    'driver'          => 'smtp', // 邮件驱动, 支持 smtp|sendmail|mail 三种驱动
    'host'            => 'smtp.qq.com', // SMTP服务器地址
    'port'            => 465, // SMTP服务器端口号,一般为25
    'addr'            => '', // 发件邮箱地址
    'pass'            => '', // 发件邮箱密码
    'name'            => 'tpadmin', // 发件邮箱名称
    'content_type'    => 'text/html', // 默认文本内容 text/html|text/plain
    'charset'         => 'utf-8', // 默认字符集
    'security'        => 'ssl', // 加密方式 null|ssl|tls
    'sendmail'        => '/usr/sbin/sendmail -bs',
    'debug'           => true, // 开启debug模式会直接抛出异常, 记录邮件发送日志
    'left_delimiter'  => '{', // 模板变量替换左定界符
    'right_delimiter' => '}', // 模板变量替换右定界符
];
```

## 使用
```
// 使用Tp5 Mailer
use tp5/Mailer

// 创建实例
$mailer = Mailer::instance();

// 设置收件人 以下几种方式任选一种
$mailer->to(['tianpian0805@gmail.com']);
$mailer->to(['tianpian0805@gmail.com' => 'yuan1994']);
$mailer->to('tianpian0805@gmail.com', 'yuan1994');
$mailer->to(['tianpian0805@qq.com', 'tianpian0805@gmail.com' => 'yuan1994']);
$mailer->to(['tianpian0805@qq.com', 'tianpian0805@gmail.com', 'tianpian0805@163.com']);

// 设置发件人 发件人邮箱地址必须和配置项里一致, 默认会自动设置发件地址 (配置里的addr) 和发件人 (配置里的name)
$mailer->form('tianpian0805@gmail.com', 'yuan1994');
$mailer->form(['tianpian0805@gmail.com' => 'yuan1994']);

// 设置邮件主题
$mailer->subject('邮件主题');

// 设置邮件内容 - HTML
$mailer->html('<p>欢迎使用Tp5 Mailer</p>');
// 或者使用变量替换HTML内容
$mailer->html('<p>欢迎使用{name}</p>', ['name' => 'Tp5 Mailer']);

// 设置邮件内容 - 纯文本
$mailer->text('欢迎使用Tp5 Mailer');
// 或者使用变量替换纯文本内容
$mailer->text('欢迎使用{name}', ['name' => 'Tp5 Mailer']);
// 你也可以很方便的设置多行文本, 直接回车换行或者使用 line() 方法, 支持多次调用
$mailer->line('尊敬的 访客: ');
$mailer->line('   欢迎您使用Tp5 Mailer');
$mailer->line(); // 不传值默认输出空行
$mailer->line('yuan1994 ' . date('Y-m-d') );
// 以上历程输出
/***************
尊敬的 访客: 
   欢迎您使用Tp5 Mailer
   
yuan1994 2016-12-01
****************/

// 设置邮件内容 - ThinkPHP5模板 (具体请看ThinkPHP5的模板怎么用)
$mailer->view('mail/register');
$mailer->view('admin@mail/register', ['account' => $account, 'name' => $name]);

// 添加附件
$mailer->attach('http://domain.com/path/to/file.ext');
// 或者指定附件的文件名
$mailer->attach(ROOT_PATH . 'foo.ext', '文件名.pdf');
// 使用匿名函数 $attachment用法请参考 http://swiftmailer.org/docs/messages.html#attaching-files
$mailer->attach(ROOT_PATH . 'foo.ext', function($attachment, $mailer) {
    $attachment->setFilename($mailer->cnEncode('文件名.jpg'));
});

// 设置消息加密/签名 使用方法请参考 http://swiftmailer.org/docs/messages.html#signed-encrypted-message
$mailer->signCertificate(function() {
    //$signer->setSignCertificate('/path/to/certificate.pem', '/path/to/private-key.pem');
    //$signer->setSignCertificate('/path/to/certificate.pem', array('/path/to/private-key.pem', 'passphrase'));
    //$signer->setSignCertificate('/path/to/certificate.pem', '/path/to/private-key.pem', PKCS7_BINARY);
    
    $smimeSigner->setSignCertificate('/path/to/sign-certificate.pem', '/path/to/private-key.pem');
    $smimeSigner->setEncryptCertificate('/path/to/encrypt-certificate.pem');
})

// 设置字符编码
$mailer->charset('utf8');

// 设置邮件最大长度
$mailer->lineLength(1000);

// 设置邮件优先级
$mailer->priority(MailerConfig::PRIORITY_HIGHEST);
// 可选值有: 
// PRIORITY_HIGHEST
// PRIORITY_HIGH
// PRIORITY_NORMAL
// const PRIORITY_LOW
// const PRIORITY_LOWEST

// Requesting a Read Receipt
$mailer->readReceiptTo('tianpian0805@gamil.com');

// 发送邮件
$mailer->send();
// 使用匿名函数, $mailer是tp5/Mailer对象, $message是Swift_Message对象
$mailer->send(function ($mailer, $message) {
    $mailer->to('tianpian0805@gmail.com')
        ->line('你好')
        ->line('这是一封测试邮件')
        ->subject('测试邮件');
});
// 发送邮件的返回值为发送成功用户的数字, 全部失败为0, 全部成功为设置收件人的数量
```

以上所有方法 (除最后发送的方法 send()) 都支持连贯调用
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

如果有邮件发送失败, 可以通过 `getFails()` 获取发送失败邮件地址的列表
```
$mailer->getFails();
// 如果没有数据将输出空数组, 如果有数据将返回邮件列表数组, 例如
// array('example1@damain.com', 'example2@damain.com')
```

使用 `getHeaders()` 和 `getHeadersString()` 方法可以获取头信息
`getHeaders()` 返回的是头信息数组, `getHeadersString()` 返回的是头信息字符串

更多文档请参考 [SwiftMailer](http://swiftmailer.org/docs/)

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
如果有遇到问题请提交 [issues](https://github.com/yuan1994/tp5-mailer/issues)

## License
Apache 2.0
