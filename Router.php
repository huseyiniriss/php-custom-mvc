<?php

class Router
{
    public static function get($route, $controller, $callback){
        self::getCallback($route, 'GET', $controller, $callback);
    }

    public static function post($route, $controller, $callback){
        self::getCallback($route, 'POST', $controller, $callback);
    }

    public static function getRoutePattern($route)
    {
        $r = explode('/', $route);
        $pattern = '/^\/' . $r[1];
        $params = array_slice($r, 2);
        if (count($params) === 0) {
            $pattern .= '$/';
        }
        foreach ($params as $key => $param) {
            if ($param === end($params)) {
                $pattern .= '\/([A-z0-9-:]+$)/';
            } else {
                $pattern .= '\/([A-z0-9-:]+)';
            }
        }
        return $pattern;
    }

    public static function getParams($route, $params){
        $param = [];
        foreach ($route as $key=>$r){
            $param[$r] = $params[$key];
        }
        return $param;
    }

    public static function getCallback($route, $requestType, $controller, $callback){
        $pattern = self::getRoutePattern($route);
        $uri = explode('?', $_SERVER['REQUEST_URI'])[0];
        $res = preg_match($pattern, $uri, $match);
        $paramNames = array_slice(explode('/:', substr($route, 1)), 1);
        $paramValues = explode('/', implode(array_slice($match, 1)));
        $args = self::getParams($paramNames, $paramValues);
        if ($res !== 0 && $_SERVER['REQUEST_METHOD'] === $requestType) {
            if (is_callable([__NAMESPACE__.$controller, $callback])){
                call_user_func_array(
                    [__NAMESPACE__.$controller, $callback],
                    [$_GET, $args]
                );
            } else {
                die("not exist $callback function");
            }
        }
    }
}
