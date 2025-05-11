<?php

namespace App\Helper;

class H
{
    private static function getRequestHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }

    public static function commonViewRender($template_file, $variables = [])
    {
        if (!file_exists($template_file)) {
            return false;
        }

        extract($variables);
        ob_start();
        include $template_file;
        return ob_get_clean();
    }

    public static function e($v) {
        return htmlspecialchars($v);
    }

    private static string $ipBaseUrl = 'post_test_task';

    private static ?string $baseUrl = null;

    public static function getBaseUrl(): string
    {
        if (isset(self::$baseUrl)) {
            return self::$baseUrl;
        }

        $server_protocol = 'http';
        $server_name = $_SERVER['SERVER_NAME'] ?? '';
        $is_ip = self::isIpServerName();
        $port = intval($_SERVER['SERVER_PORT']);
        $server_url = $server_name;
        if (!in_array($port, [80, 443])) {
            $server_url .= ':' . $port;
        }
        if ($is_ip) {
            $server_url = implode('/', [$server_url, self::$ipBaseUrl]);
        }

        self::$baseUrl = $server_protocol . '://' . $server_url;
        return self::$baseUrl;
    }

    private static function isIpServerName() {
        $ip_number_regex = '(25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])';
        $ip_regex = '~' . implode('\.', [$ip_number_regex, $ip_number_regex, $ip_number_regex, $ip_number_regex]) . '~';
        $server_name = $_SERVER['SERVER_NAME'] ?? '';
        return preg_match($ip_regex, $server_name) === 1;
    }

    public static function getRequestUri() {
        $request_uri = $_SERVER['REQUEST_URI'];
        if (self::isIpServerName()) {
            $regex_prefix_replace = '~^/' . self::$ipBaseUrl . '~';
            $request_uri = preg_replace($regex_prefix_replace, '', $request_uri);
        }
        return $request_uri;
    }
}