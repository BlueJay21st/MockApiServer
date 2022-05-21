# MockApiServer

用于快速创建虚拟服务端接口，需要设置伪静态。

## 使用方法：

### 设置伪静态

使用Typecho的伪静态规则即可

    if (!-e $request_filename) {
        rewrite ^(.*)$ /index.php$1 last;
    }

### 设置路由响应规则

    Mock::api( String $uri, $method, Int $http_code = 200 );

#### $uri

请求地址，支持通配符(只能匹配单格)，比如：

规则为：/search/*

以下请求可以匹配：`/search/text`、`/search/text/?get_param=test`.

以下请求无法匹配：`/search/text/other`

#### $method

此请求要执行的闭包函数，或者想要返回的内容。

如果传入闭包函数，可以使用：

`Mock::setCode(Int $http_code)`设置响应的HTTP状态码，如`Mock::setCode(302)`.

`Mock::setHeader(String $header)`设置响应标头，如`Mock::setHeader('Location: https://github.com')`.

`msleep($ms)`设置等待时间，单位为毫秒.

如果传入字符串将被直接输出，如果传入数组则会通过`json_encode`转换为JSON后输出.

#### $http_code

当前请求的状态码，`$method`非闭包函数时有效，若`$method`为闭包函数则仅在`Mock::setCode(Int $http_code)`未被调用或设定值为`200`时有效.
