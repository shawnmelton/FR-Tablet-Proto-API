<?php
class Router {
    static function run() {
        $bits = explode('/', str_replace($_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI']));
        $siteroot = dirname(dirname(__FILE__));
        
        // Default to home page.
        $controller = 'Controller';
        $action = 'index';
        
        // Determine controller
        if(isset($bits[1]) && ($bits[1] = strtolower(preg_replace('/\W/', '', $bits[1]))) != '') {
            $controller = ucwords(strtolower($bits[1])) .'Controller';
            if(!file_exists($siteroot .'/controllers/'. $controller .'.php')) {
                header('HTTP/1.0 404 Not Found');
                exit;
            }
        }

        $controller = new $controller();
        $controller->api();
    }
}