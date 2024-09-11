<?php
/**
 * @author XJ.
 * @Date   2023/8/17 0017
 */

namespace Fatbit\HyperfTools\HttpServer\Middleware;

use Hyperf\HttpMessage\Server\Request as Psr7Request;
use Hyperf\HttpServer\Server as HttpServer;
use Hyperf\Tracer\Middleware\TraceMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Fatbit\HyperfTools\Enums\RequestLife;
use Scjc\core\Tracer\JcTracerEntity;

class HttpTraceMiddleware extends TraceMiddleware
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getUri()->getPath() === '/favicon.ico') {
            return $handler->handle($request);
        }
        /** @var HttpServer $instance */
        $requestPath = $request->getUri()->getPath();
        $span        = $this->startSpan('rest');
        RequestLife::HTTP
            ->setLiftType($request->getServerParams()['request_time_float'] ?? null)
            ->setTraceId($span->getContext()->getContext()->getTraceId())
            ->setBeginPath($requestPath);

        $res = parent::process($request, $handler);

        if (isset($span)) {
            $span->finish();
        }

        JcTracerEntity::getInstance()->clear();

        return $res;
    }

}