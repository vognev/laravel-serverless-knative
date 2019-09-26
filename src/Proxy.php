<?php

namespace Laravel\Serverless\Knative;

use Closure;
use Laravel\Serverless\HeadersParser;
use Symfony\Component\HttpFoundation\Response;

class Proxy
{
    public static function start(Closure $callback)
    {
        return new static($callback);
    }

    private function __construct(Closure $callback)
    {
        $port       = env('PORT') ?? 8080;
        $context    = [
            'handler' =>  env('HANDLER')
        ];

        $socket = stream_socket_server("tcp://0.0.0.0:$port", $errno, $errstr);

        if (! $socket) {
            $this->log("$errstr ($errno)");
            exit(1);
        }

        pcntl_signal(SIGCHLD, SIG_IGN);

        $this->log('Accepting connections');
        while ($conn = stream_socket_accept($socket, -1)) {
            $pid = pcntl_fork();
            if (-1 === $pid || $pid > 0) {
                fclose($conn);
            } else {
                $this->handle($context, $conn, $callback);
                exit();
            }
        }

        $this->log('Gracefully terminating');
        while (-1 !== pcntl_wait($status));
    }

    private function handle(array $context, $conn, $callback) : void
    {
        $requestline = fgets($conn, 4096);
        if (substr_count($requestline, ' ') < 2) {
            $this->log("Malformed requestline: '$requestline'");
            return;
        }

        list($method, $path, $proto) = explode(' ', trim($requestline), 3);

        $headers = [];
        while ($headerline = trim(fgets($conn, 4096))) {
            // todo: check for truncation
            if (false === strpos($headerline, ':')) {
                $this->log("Malformed header: ${headerline}");
            } else {
                $headers[] = $headerline;
            }
        }

        $headers = HeadersParser::parse($headers);
        $event = [];
        $body = null;

        if (array_key_exists('content-length', $headers)) {
            // todo: validate it!
            $contentLength = current($headers['content-length']);
            $body = fopen('php://temp', 'wb+');
            stream_copy_to_stream($conn, $body, $contentLength);
            rewind($body);

            if (array_key_exists('content-type', $headers)) {
                $contentType = current($headers['content-type']);
                if ('application/x-www-form-urlencoded' == $contentType) {
                    parse_str(stream_get_contents($body), $body);
                }
                if ('application/json' === $contentType) {
                    $body = json_decode(stream_get_contents($body), true);
                }
            }
        }

        $event['data'] = $body;
        $event['extensions']['request'] = [
            'method'    => $method,
            'proto'     => $proto,
            'path'      => $path,
            'headers'   => $headers,
        ];

        $functionStarted = microtime(true);
        try {
            $result = $callback($event, $context, $this);
        } catch (\Exception $e) {
            $this->log($e->getMessage());
            $this->log($e->getTraceAsString());
            $result = $e;
        }

        $functionDuration = microtime(true) - $functionStarted;
        $this->log(sprintf('Completed in %2.6fs', $functionDuration));

        if ($result instanceof \Exception) {
            fwrite($conn, "${proto} 500 Internal Server Error\r\n");
            fwrite($conn, "Content-Type: text/plain\r\n");
            fwrite($conn, "Content-Length: " . strlen($e->getMessage()) . "\r\n");
            fwrite($conn, "\r\n");
            fwrite($conn, $e->getMessage());
        } elseif($result instanceof Response) {
            $result->headers->set('content-length', strlen($result->getContent()));
            $result->headers->set('connection', 'close');
            fwrite($conn, (string) $result);
        } else {
            fwrite($conn, "${proto} 200 OK\r\n");
            fwrite($conn, "Content-Type: text/plain\r\n");
            fwrite($conn, "Content-Length: " . strlen($result) . "\r\n");
            fwrite($conn, "\r\n");
            fwrite($conn, $result);
        }
    }

    public function log(string $message)
    {
        fwrite(STDERR, sprintf(
            "[%s] [%d] %s", date('Y-m-d H:i:s'), getmypid(), $message
        ) . PHP_EOL);
    }
}
