<?php

namespace Sxqibo\FastPayment\Common;

use Exception;

class Cache
{
    /**
     * 缓存路径
     * @var null
     */
    public static $cache_path = null;

    /**
     * 缓存写入操作
     * @var array
     */
    public static $cache_callable = [
        'set' => null, // 写入缓存
        'get' => null, // 获取缓存
        'del' => null, // 删除缓存
        'put' => null, // 写入文件
    ];

    /**
     * 获取缓存内容
     * @param string $name 缓存名称
     * @return null|mixed
     */
    public static function getCache($name)
    {
        if (is_callable(self::$cache_callable['get'])) {
            return call_user_func_array(self::$cache_callable['get'], func_get_args());
        }
        $file = self::getCacheDir($name);
        if (file_exists($file) && is_file($file) && ($content = file_get_contents($file))) {
            $data = unserialize($content);
            if (isset($data['expired']) && (intval($data['expired']) === 0 || intval($data['expired']) >= time())) {
                return $data['value'];
            }
            self::delCache($name);
        }
        return null;
    }

    /**
     * 缓存配置与存储
     * @param string $name 缓存名称
     * @param string $value 缓存内容
     * @param int $expired 缓存时间(0表示永久缓存)
     * @return string
     * @throws Exception
     */
    public static function setCache($name, $value = '', $expired = 3600)
    {
        if (is_callable(self::$cache_callable['set'])) {
            return call_user_func_array(self::$cache_callable['set'], func_get_args());
        }
        $file = self::getCacheDir($name);
        $data = ['name' => $name, 'value' => $value, 'expired' => time() + intval($expired)];
        if (!file_put_contents($file, serialize($data))) {
            throw new \Exception('local cache error.', '0');
        }
        return $file;
    }

    /**
     * 移除缓存文件
     * @param string $name 缓存名称
     * @return boolean
     */
    public static function delCache($name)
    {
        if (is_callable(self::$cache_callable['del'])) {
            return call_user_func_array(self::$cache_callable['del'], func_get_args());
        }
        $file = self::getCacheDir($name);
        return file_exists($file) ? unlink($file) : true;
    }

    /**
     * 应用缓存目录
     * @param string $name
     * @return string
     */
    private static function getCacheDir($name)
    {
        if (empty(self::$cache_path)) {
            self::$cache_path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'certs' . DIRECTORY_SEPARATOR;
        }
        self::$cache_path = rtrim(self::$cache_path, '/\\') . DIRECTORY_SEPARATOR;
        file_exists(self::$cache_path) || mkdir(self::$cache_path, 0755, true);

        return self::$cache_path . $name;
    }
}
