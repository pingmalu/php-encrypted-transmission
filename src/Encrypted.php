<?php
/*!
 * Encrypted transmission
 * https://malu.me
 * Version 1.0
 *
 * Copyright 2021, Ma Lu 
 * Released under the MIT license
 */

namespace Malu\Encrypted;

class Encrypted
{
     /**
     * url safe base64编码
     * @param $string
     * @return string|string[]
     */
    public static function urlsafe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    /**
     * url safe base64解码
     * @param $string
     * @return string|string[]
     */
    public static function urlsafe_b64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    /**
     * 加密函数
     * @param $data
     * @param $key
     * @return string
     */
    public static function encrypt($data, $key)
    {
        $key = md5($key);
        $x = 0;

        $data = self::urlsafe_b64encode($data);

        $len = strlen($data);
        $l = strlen($key);  // 32

        $char = "";
        $str = "";
        // 循环拼接私钥md5后的字符，组装到待加密字串长度
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= $key[$x];
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            // ord() 函数返回字符串的首个字符的 ASCII 值。
            // 给每个字符循环 私钥md5后的ASCII 与 与原文处理后的字符做异或运算
            // 最后把 ASCII 值转成字符
            $str .= chr(ord($data[$i]) ^ ord($char[$i]));
        }
        $str = self::urlsafe_b64encode($str); // 可以在URL安全传输

        return $str;
    }

    /**
     * 解密函数
     * @param $data
     * @param $key
     * @return string
     */
    public static function decrypt($data, $key)
    {
        $key = md5($key);
        $x = 0;
        $data = self::urlsafe_b64decode($data);

        $len = strlen($data);
        $l = strlen($key);

        $char = "";
        $str = "";
        // 循环拼接私钥md5后的字符，组装到加密字串长度一样长
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            // 把加密字串ASCII 与 私钥md5后的字符串ASCII 做异或运算
            // 最后把 ASCII 值还原成字符
            $str .= chr(ord(substr($data, $i, 1)) ^ ord(substr($char, $i, 1)));
        }
        $str = self::urlsafe_b64decode($str);
        return $str;
    }


    /**
     * 加密函数
     * @param $data
     * @param $key
     * @return string
     */
    public static function encrypt_v1($data, $key)
    {
        $key = md5($key);
        $x = 0;
        $len = strlen($data);
        $l = strlen($key);  // 32

        $char = "";
        $str = "";
        // 循环拼接私钥md5后的字符，组装到待加密字串长度
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= $key[$x];
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            // ord() 函数返回字符串的首个字符的 ASCII 值。
            // 给每个字符循环 加上 私钥md5后的 ASCII 与 256 求模后的值(求模是防止长度越界，比如中文字符)
            // 最后把 ASCII 值转成字符
            var_dump((ord($data[$i]) + ord($char[$i])) % 256);
            $str .= chr((ord($data[$i]) + ord($char[$i])) % 256);
        }
        var_dump($str);
        return base64_encode($str); // 用基础的64个字符替换
    }

    /**
     * 解密函数
     * @param $data
     * @param $key
     * @return string
     */
    public static function decrypt_v1($data, $key)
    {
        $key = md5($key);
        $x = 0;
        $data = base64_decode($data);
        $len = strlen($data);
        $l = strlen($key);

        $char = "";
        $str = "";
        // 循环拼接私钥md5后的字符，组装到加密字串长度一样长
        for ($i = 0; $i < $len; $i++) {
            if ($x == $l) {
                $x = 0;
            }
            $char .= substr($key, $x, 1);
            $x++;
        }
        for ($i = 0; $i < $len; $i++) {
            if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
                // 如果加密字串ASCII小于密文ASCII，表示长度已越界，需要补256
                // 那么把 加密字串ASCII + 256 - 私钥md5后的字符串ASCII
                $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
            } else {
                $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
            }
        }
        return $str;
    }
}