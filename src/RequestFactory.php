<?php

namespace Laravel\Serverless\Knative;

class RequestFactory
{
    public static function fromPayload(array &$event) : \Illuminate\Http\Request
    {
        $content = $event['data'];

        $headers = $event['extensions']['request']['headers'];

        $queryString = parse_url($event['extensions']['request']['path'], PHP_URL_QUERY);

        $request = []; $files = [];
        parse_str($queryString, $query);

        self::parseRequest($headers, $content, $request, $files);
        $cookies = self::parseCookies($headers);

        $fullUrl = $event['extensions']['request']['path'];
        $baseUrl = $headers['x-replaced-path'][0] ?? '';
        if (! empty($baseUrl)) {
            $path = parse_url($fullUrl, PHP_URL_PATH);
            if (strlen($baseUrl) - strlen($path) === strrpos($baseUrl, $path)) {
                // real baseUrl (prefix), ends with $path
                $baseUrl = substr($baseUrl, 0, -strlen($path));
            } else {
                // we're at root of domain otherwise
                $fullUrl = $baseUrl . $fullUrl;
                $baseUrl = '';
            }
        }

        $server  = self::parseServer($headers) + [
            'REQUEST_METHOD' => strtoupper($event['extensions']['request']['method'] ?? 'GET'),
            'REQUEST_URI' => implode('', [
                $baseUrl,
                $fullUrl
            ]),
            'SERVER_NAME' => 'localhost',
            'SERVER_PROTOCOL' => $event['extensions']['request']['proto'],
            'SERVER_PORT' => 80,
            'REMOTE_ADDR' => '127.0.0.1',
            'SCRIPT_FILENAME' => public_path('index.php'),
            'SCRIPT_NAME' => $baseUrl . '/index.php'
        ];

        return new \Illuminate\Http\Request(
            $query, $request, [], $cookies, $files, $server, $content
        );
    }

    private static function parseCookies(array &$headers) : array
    {
        $cookies = [];

        foreach ($headers['cookie'] ?? [] as $cookies) {
            foreach ((array) $cookies as $cookieString) {
                parse_str(strtr($cookieString, array('&' => '%26', '+' => '%2B', ';' => '&')), $cookies);
            }
        }

        return $cookies;
    }

    private static function parseServer(array &$headers) : array
    {
        $server = [];
        foreach ($headers as $headerName => $headerStrings) {
            foreach ((array) $headerStrings as $headerString) {
                $serverHeaderName = 'HTTP_' . strtoupper(str_replace('-', '_', $headerName));
                $server[$serverHeaderName] = $headerString;
            }
        }

        return $server;
    }

    private static function parseRequest(array &$headers, &$content, array &$request, array &$files) : void
    {
        if (array_key_exists('content-type', $headers)) {
            $contentType = current((array) $headers['content-type']);
        }

        if (!isset($contentType) || !$contentType) {
            return;
        }

        try {
            switch (true) {
                // exclude CTs already parsed in event
                case 0 === stripos($contentType, 'application/x-www-form-urlencoded'):
                case 0 === stripos($contentType, 'application/json'):
                    $request = $content;
                    break;
                case 0 === stripos($contentType, 'multipart/form-data');
                    $data = new \Laravel\Serverless\MultipartParser($content);
                    $request = $data->getFormData(); $files = $data->getFiles();
                    break;
                default:
                    break; // or throw ?
            }
        } finally {
            is_resource($content) && rewind($content);
        }
    }
}
