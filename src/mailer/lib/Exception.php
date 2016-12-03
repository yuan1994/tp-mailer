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

if (class_exists('\\think\\Exception')) {
    /**
     * thinkphp5异常类
     *
     * Class Exception
     * @package mailer\lib
     */
    class Exception extends \think\Exception
    {

    }
} elseif (class_exists('\\Think\\Exception')) {
    /**
     * ThinkPHP3异常类
     *
     * Class Exception
     * @package mailer\lib
     */
    class Exception extends \Think\Exception
    {

    }
} else {
    /**
     * 默认异常类
     *
     * Class Exception
     * @package mailer\lib
     */
    class Exception extends \Exception
    {

    }
}
