<?php

namespace App\Bootstrap;

use App\Controller\IndexController;
use App\Helper\H;

class Web
{
    public static $parts = [];

    public function process() {
        // here can be used routes but for simplifying project here will be only using of controller
        $controller = new IndexController();

        $req_uri = H::getRequestUri();

        $is_ok = preg_match('~^\/.*~', $req_uri) !== false;

        self::$parts = explode('/', trim($req_uri, '/'));

        if ($is_ok) {
            $controller->index();
            exit;
        }

        http_response_code(404);
        echo '<h1>Not found</h1>';
    }
}