<?php

namespace tp5;

class MailerConfig
{
    /********* 邮件驱动 *********/
    const DRIVER_SMTP = 'smtp';
    const DRIVER_SENDMAIL = 'sendmail';
    const DRIVER_MAIL = 'mail';

    /********* 文本类型 *********/
    const CONTENT_HTML = 'text/html';
    const CONTENT_PLAIN = 'text/plain';

    /********* 优先级 **********/
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH = 2;
    const PRIORITY_NORMAL = 3;
    const PRIORITY_LOW = 4;
    const PRIORITY_LOWEST = 5;
}
