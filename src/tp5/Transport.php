<?php

namespace tp5;

use think\Config;
use Swift_SmtpTransport;
use Swift_SendmailTransport;
use Swift_MailTransport;

class Transport
{
    protected function createSmtpDriver()
    {
        $config = Config::get('mail');

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

    protected function createSendmailDriver()
    {
        $transport = Swift_SendmailTransport::newInstance(Config::get('mail.sendmail'));

        return $transport;
    }

    protected function createMailDriver()
    {
        $transport = Swift_MailTransport::newInstance();

        return $transport;
    }

    /**
     * 获取邮件驱动
     *
     * @return Swift_SmtpTransport|Swift_SendmailTransport|Swift_MailTransport
     * @throws MailerException
     */
    public function getDriver()
    {
        $driverName = Config::get('mail.driver');
        $driver = 'create' . ucfirst($driverName) . 'Driver';
        if (!method_exists($this, $driver)) {
            throw new MailerException("Mailer driver {$driverName} not exist");
        }

        return $this->$driver();
    }

    /**
     * 设置邮件驱动
     *
     * @param string $driver
     */
    public function setDriver($driver = MailerConfig::DRIVER_SMTP)
    {
        Config::set('mail.driver', $driver);
    }
}
