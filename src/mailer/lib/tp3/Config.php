<?php
/**
 * tp-mailer [A powerful and beautiful php mailer for All of ThinkPHP and Other PHP Framework based SwiftMailer]
 *
 * @author    yuan1994 <tianpian0805@gmail.com>
 * @link      https://github.com/yuan1994/tp-mailer
 * @copyright 2016 yuan1994 all rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace mailer\lib\tp3;

/**
 * 兼容ThinkPHP3的配置获取类
 *
 * Class ConfigTp3
 * @package mailer
 */
class Config
{
    /**
     * 检测配置是否存在
     *
     * @param string    $name 配置参数名（支持二级配置 .号分割）
     * @param string    $range  作用域
     * @return bool
     */
    public static function has($name, $range = '')
    {
        if ($range) {
            $name = $range . '.' . $name;
        }

        return C($name);
    }

    /**
     * 获取配置参数 为空则获取所有配置
     *
     * @param string    $name 配置参数名（支持二级配置 .号分割）
     * @param string    $range  作用域
     * @return mixed
     */
    public static function get($name = null, $range = '')
    {
        if ($range && $name) {
            $name = $range . '.' . $name;
        } elseif ($range && !$name) {
            $name = $range;
        }

        return C($name);
    }

    /**
     * 设置配置参数 name为数组则为批量设置
     *
     * @param string|array  $name 配置参数名（支持二级配置 .号分割）
     * @param mixed         $value 配置值
     * @param string        $range  作用域
     * @return mixed
     */
    public static function set($name, $value = null, $range = '')
    {
        if ($range && $name) {
            $name = $range . '.' . $name;
        }

        return C($name, $value);
    }
}
