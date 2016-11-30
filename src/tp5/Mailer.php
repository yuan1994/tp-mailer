<?php

namespace tp5;

use Swift_Mailer;
use Swift_Message;
use think\Config;
use think\Exception;
use think\Log;
use think\View;

class Mailer
{
    // 单例
    private static $instance;
    /**
     * @var \Swift_Message
     */
    private $message;
    // 以行设置文本的内容
    private $line = [];
    // 错误信息
    private $errMsg;
    // 发送失败的帐号
    private $fails;
    // 左定界符
    private $LDelimiter = '{';
    // 右定界符
    private $RDelimiter = '}';

    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->init();
    }

    /**
     * 重置实例
     *
     * @return $this
     */
    public function init()
    {
        $this->message = Swift_Message::newInstance(
            null,
            null,
            Config::get('mail.content_type'),
            Config::get('mail.charset')
        )
            ->setFrom(Config::get('mail.addr'), Config::get('mail.name'));

        return $this;
    }

    /**
     * 设置邮件主题
     *
     * @param $subject
     * @return $this
     */
    public function subject($subject)
    {
        $this->message->setSubject($subject);

        return $this;
    }

    /**
     * 设置发件人
     *
     * @param $address
     * @param null $name
     * @return $this
     */
    public function form($address, $name = null)
    {
        $this->message->setFrom($address, $name);

        return $this;
    }

    /**
     * 设置收件人
     *
     * @param $address
     * @param null $name
     * @return $this
     */
    public function to($address, $name = null)
    {
        $this->message->setTo($address, $name);

        return $this;
    }

    /**
     * 设置邮件内容为HTML内容
     *
     * @param $content
     * @param array $param
     * @return $this
     */
    public function html($content, $param = [])
    {
        if ($param) {
            $content = strtr($content, $this->parseParam($param));
        }
        $this->message->setBody($content, MailerConfig::CONTENT_HTML);

        return $this;
    }

    /**
     * 设置邮件内容为纯文本内容
     *
     * @param $content
     * @param array $param
     * @return $this
     */
    public function text($content, $param = [])
    {
        if ($param) {
            $content = strtr($content, $this->parseParam($param));
        }
        $this->message->setBody($content, MailerConfig::CONTENT_PLAIN);

        return $this;
    }

    /**
     * 载入一个模板作为邮件内容
     *
     * @param string $template
     * @param array $param
     * @param array $config
     * @return Mailer
     */
    public function view($template, $param = [], $config = [])
    {
        $view = View::instance(Config::get('template'), Config::get('view_replace_str'));
        $content = $view->fetch($template, $param, [], $config);

        return $this->html($content);
    }

    /**
     * 添加一行数据
     *
     * @param $content
     * @return $this
     */
    public function line($content = '')
    {
        $this->line[] = $content;

        return $this;
    }

    /**
     * 添加附件
     *
     * @param string $filePath
     * @param string|\Swift_Attachment|null $attr
     * @return $this
     */
    public function attach($filePath, $attr = null)
    {
        $attachment = \Swift_Attachment::fromPath($filePath);
        if ($attr instanceof \Closure) {
            call_user_func_array($attr, [& $attachment, $this]);
        } elseif ($attr) {
            $attachment->setFilename($this->cnEncode($attr));
        } else {
            // 修复中文文件名乱码bug
            $tmp = str_replace("\\", '/', $filePath);
            $tmp = explode('/', $tmp);
            $filename = end($tmp);
            $attachment->setFilename($this->cnEncode($filename));
        }
        $this->message->attach($attachment);

        return $this;
    }

    /**
     * Signed/Encrypted Message
     *
     * @param \Swift_Signers_SMimeSigner $smimeSigner
     * @return $this
     */
    public function signCertificate($smimeSigner)
    {
        if ($smimeSigner instanceof \Closure) {
            $signer = \Swift_Signers_SMimeSigner::newInstance();
            call_user_func_array($smimeSigner, [& $signer]);
            $this->message->attachSigner($signer);
        }

        return $this;
    }

    /**
     * 设置字符编码
     *
     * @param string $charset
     * @return $this
     */
    public function charset($charset)
    {
        $this->message->setCharset($charset);

        return $this;
    }

    /**
     * 设置邮件最大长度
     *
     * @param int $length
     * @return $this
     */
    public function lineLength($length)
    {
        $this->message->setMaxLineLength($length);

        return $this;
    }

    /**
     * 设置优先级
     *
     * @param int $priority
     * @return $this
     */
    public function priority($priority = MailerConfig::PRIORITY_HIGHEST)
    {
        $this->message->setPriority($priority);

        return $this;
    }

    /**
     * Requesting a Read Receipt
     *
     * @param string $address
     * @return $this
     */
    public function readReceiptTo($address)
    {
        $this->message->setReadReceiptTo($address);

        return $this;
    }

    /**
     * 获取头信息
     *
     * @return \Swift_Mime_HeaderSet
     */
    public function getHeaders()
    {
        return $this->message->getHeaders();
    }

    /**
     * 获取头信息 (字符串)
     *
     * @return string
     */
    public function getHeadersString()
    {
        return $this->getHeaders()->toString();
    }

    /**
     * 发送邮件
     *
     * @param $driver
     * @return bool|int
     * @throws Exception
     */
    public function send($message = null, $driver = null)
    {
        try {
            // 获取将行数据设置到message里
            if ($this->line) {
                $this->message->setBody(implode("\r\n", $this->line), MailerConfig::CONTENT_PLAIN);
                $this->line = [];
            }
            // 匿名函数
            if ($message instanceof \Closure) {
                call_user_func_array($message, [& $this, & $this->message]);
            }
            // 邮件驱动
            $transport = new Transport();
            if (null !== $driver) {
                $transport->setDriver($driver);
            }
            $mail = Swift_Mailer::newInstance($transport->getDriver());
            // debug模式记录日志
            if (Config::get('mail.debug')) {
                Log::write(var_export($this->getHeadersString(), true), 'MAILER');
            }

            return $mail->send($this->message, $this->fails);
        } catch (\Exception $e) {
            $this->errMsg = $e->getMessage();
            if (Config::get('mail.debug')) {
                // 调试模式直接抛出异常
                throw new Exception($e->getMessage());
            } else {
                return false;
            }
        }
    }

    /**
     * 获取错误信息
     *
     * @return mixed
     */
    public function getError()
    {
        return $this->errMsg;
    }

    /**
     * 获取发送错误的邮箱帐号列表
     *
     * @return mixed
     */
    public function getFails()
    {
        return $this->fails;
    }

    /**
     * 中文文件名编码, 防止乱码
     *
     * @param $string
     * @return string
     */
    public function cnEncode($string)
    {
        return "=?UTF-8?B?" . base64_encode($string) . "?=";
    }

    /**
     * 将参数中的key值替换为可替换符号
     *
     * @param $param
     * @return mixed
     */
    private function parseParam($param)
    {
        $ret = [];
        $leftDelimiter = Config::has('mail.left_delimiter')
            ? Config::get('mail.left_delimiter')
            : $this->LDelimiter;
        $rightDelimiter = Config::has('mail.right_delimiter')
            ? Config::get('mail.right_delimiter')
            : $this->RDelimiter;
        foreach ($param as $k => $v) {
            $ret[$leftDelimiter . $k . $rightDelimiter] = $v;
        }

        return $ret;
    }
}
