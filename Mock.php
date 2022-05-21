<?php
class Mock
{
    protected const HTTP_CODE = [
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 Unused',
        307 => '307 Temporary Redirect',
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Time-out',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Large',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested range not satisfiable',
        417 => '417 Expectation Failed',
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Time-out',
        505 => '505 HTTP Version not supported'
    ];

    protected static $route;
    
    protected static $response_code = 200;
    protected static $response_header = [];

    public static function api(String $uri, $method, Int $http_code = 200)
    {
        $uri = ltrim($uri, '/');
        $uri = rtrim($uri, '/');
        self::$route[$uri] = ['http_code' => $http_code, 'method' => $method];
    }

    protected static function router()
    {
        $request_uri = ltrim($_SERVER['REQUEST_URI'], '/index.php');
        $request_uri = rtrim(ltrim($request_uri, '/'), '/');
        $check_get_param = strpos($request_uri, '?');
        if($check_get_param){
            $request_uri = substr($request_uri, 0, $check_get_param);
        }

        if(isset(self::$route[$request_uri])){
            return self::$route[$request_uri];
        }

        $request_uri = explode('/', $request_uri);
        $request_uri_count = count($request_uri);
        foreach(self::$route as $api_uri => $api_route){
            $api_uri = explode('/', $api_uri);
            if(count($api_uri) === $request_uri_count){
                for($i = 0; $i < $request_uri_count; $i++){
                    if($api_uri[$i] !== '*' && $api_uri[$i] !== $request_uri[$i]) break;
                    if(($i + 1) === $request_uri_count) return $api_route;
                }
            }
        }
        exit('Route matching failed');
    }

    public static function setCode($http_code)
    {
        self::$response_code = $http_code;
    }

    public static function setHeader($header)
    {
        self::$response_header[] = $header;
    }

    public static function response()
    {
        $route = self::router();
        if($route['method'] instanceof Closure){
            $result = $route['method']();
            if(is_array($result)){
                $result = json_encode($result);
            }
            if(self::$response_code !== 200){
                $http_code = self::$response_code;
            }else{
                $http_code = $route['http_code'];
            }
        }else if(is_array($route['method'])){
            $result = json_encode($route['method']);
            $http_code = $route['http_code'];
        }else if(is_string($route['method']) || is_numeric($route['method'])){
            $result = $route['method'];
            $http_code = $route['http_code'];
        }

        if(isset(self::HTTP_CODE[$http_code])){
            header('HTTP/1.1 ' . self::HTTP_CODE[$http_code]);
            foreach(self::$response_header as $header){
                header($header);
            }
            echo $result;
        }else{
            exit('Input HTTP code not found: ' . $http_code);
        }
    }
}

function msleep(Int $ms)
{
    usleep($ms * 1000);
}
