<?php
/**
 * tp-mailer [A powerful and beautiful php mailer for All of ThinkPHP and Other PHP Framework based SwiftMailer]
 *
 * @author    yuan1994 <tianpian0805@gmail.com>
 * @link      https://github.com/yuan1994/tp-mailer
 * @copyright 2016 yuan1994 all rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace mailer\lib;

use Swift_SmtpTransport;
use Swift_SendmailTransport;
use Swift_MailTransport;

class Transport
{
    // 单例
    private static $instance;

    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * 创建一个smtp传输对象
     *
     * @param array $config 配置信息
     * @return Swift_SmtpTransport
     */
    public function createSmtpDriver($config = [])
    {
        $config = array_merge(Config::get('mail'), $config);

        $transport = Swift_SmtpTransport::newInstance(
            $config['host'], $config['port'], $config['security']
        );

        if (isset($config['addr'])) {
            $transport->setUsername($config['addr']);
            $transport->setPassword($config['pass']);
        }

        if (isset($config['stream'])) {
            $transport->setStreamOptions($config['stream']);
        }

        return $transport;
    }

    /**
     * 创建一个sendmail传输对象
     *
     * @param $sendmail null|string sendmail配置
     * @return Swift_SendmailTransport
     */
    public function createSendmailDriver($sendmail = null)
    {
        $transport = Swift_SendmailTransport::newInstance(
            $sendmail ? $sendmail : Config::get('mail.sendmail')
        );

        return $transport;
    }

    /**
     * 创建一个mail传输对象
     *
     * @return Swift_MailTransport
     */
    public function createMailDriver()
    {
        $transport = Swift_MailTransport::newInstance();

        return $transport;
    }

    /**
     * 获取邮件驱动
     *
     * @param null|string $driver 发送邮件驱动名称
     * @return Swift_SmtpTransport|Swift_SendmailTransport|Swift_MailTransport
     * @throws Exception
     */
    public function getDriver($driver = null)
    {
        $driverName = $driver ? $driver : Config::get('mail.driver');
        $driver = 'create' . ucfirst($driverName) . 'Driver';
        if (!method_exists($this, $driver)) {
            throw new Exception("Mailer driver {$driverName} not exist");
        }

        return $this->$driver();
    }
}
