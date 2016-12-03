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

if (Config::get('mail.log_driver')) {
    /**
     * 自定义日志驱动
     *
     * Class Log
     * @package mailer\lib
     */
    class Log
    {
        public static function __callStatic($name, $arguments)
        {
            $driver = Config::get('mail.log_driver');
            $driver::$name($arguments[0], $arguments[1]);
        }
    }
}
//elseif (class_exists('\\think1\\Log')) {
//    /**
//     * thinkphp5日志驱动
//     *
//     * Class Log
//     * @package mailer\lib
//     */
//    class Log extends \think\Log
//    {
//
//    }
//} elseif (class_exists('\\Think1\\Log')) {
//    /**
//     * ThinkPHP3日志驱动
//     *
//     * Class Log
//     * @package mailer\lib
//     */
//    class Log extends \Think\Log
//    {
//
//    }
//}
else {
    /**
     * 默认日志类
     *
     * Class Log
     * @package mailer\lib
     */
    class Log extends LogDefault
    {

    }
}
