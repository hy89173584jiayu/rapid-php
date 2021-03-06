<?php

namespace rapidPHP\library\core\server\response;

use rapidPHP\library\core\server\Request;
use rapidPHP\library\core\server\Response;

class CGIResponse extends Response
{

    /**
     * @var CGIResponse
     */
    private static $instance;

    /**
     * 快速获取实例对象
     * @return Response
     */
    public static function getInstance()
    {
        return self::$instance instanceof self ? self::$instance : self::$instance = new self(session_id());
    }

    /**
     * 设置HttpCode，如404, 501, 200
     * @param $code
     * @return bool
     */
    public function status($code): bool
    {
        return $this->setHeader(["HTTP/1.1 {$code}", "Status: {$code}"]);
    }

    /**
     * 设置Http头信息
     * @param $data
     * @param bool $ucfirst
     * @return bool
     */
    public function header($data, $ucfirst = true): bool
    {
        if ($ucfirst) $data = ucfirst($data);

        header($data);

        return true;
    }

    /**
     * 重定向
     * @param $url
     * @param int $httpCode
     * @return bool
     */
    public function redirect($url, $httpCode = 302): bool
    {
        $this->status($httpCode);

        return $this->header("Location: {$url}");
    }

    /**
     * 设置Cookie
     *
     * @param string $key
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @param string $samesite 从  php v7.3.0 版本开始支持
     * @return bool
     */
    public function cookie($key, $value, $expire = 0, $path = '/', $domain = '', $secure = false, $httponly = false, $samesite = ''): bool
    {
        if (version_compare(PHP_VERSION, '7.3.0', '>=')) {
            return setcookie($key, $value, [
                'expires' => $expire,
                'path' => $path,
                'domain' => $domain,
                'secure' => $secure,
                'httponly' => $httponly,
                'samesite' => $samesite,
            ]);
        } else {
            return setcookie($key, $value, $expire, $path, $domain, $secure, $httponly);
        }
    }


    /**
     * 启用Http-Chunk分段向浏览器发送数据
     *
     * @param string $data
     * @param array $options
     * @return bool
     */
    public function write($data, $options = []): bool
    {
        echo $data;
        return true;
    }


    /**
     * 发送文件或者下载文件
     * @param string $filename
     * @param array $options
     * @return bool
     */
    public function sendFile($filename, $options = []): bool
    {
        return $this->printFile($filename, array_merge(['download' => true], $options));
    }

    /**
     * 结束Http响应，发送HTML内容
     * @param string $data
     * @param array $options
     * @return bool
     */
    public function end($data = '', $options = []): bool
    {
        echo $data;
        exit();
    }

}