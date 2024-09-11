<?php
/**
 * @author XJ.
 * @Date   2024/2/5 0005
 */

namespace Fatbit\HyperfTools\Aspects\TracerRecords;

use Fatbit\HyperfTools\Tracer\TracerEntity;
use GuzzleHttp\Client;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Stringable\Str;

#[Aspect]
class HttpClientAspect extends AbstractAspect
{
    public array $classes = [
        Client::class . '::requestAsync',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $startTime = microtime(true);
        $res       = $proceedingJoinPoint->process();
        $method    = $proceedingJoinPoint->getArguments()[0];
        $uri       = $proceedingJoinPoint->getArguments()[1];
        $options   = $proceedingJoinPoint->getArguments()[2];
        $uri       = '/' . Str::after(Str::after($uri, '://'), '/');
        TracerEntity::getInstance()->apeendHttp(
            [
                'method'  => $method,
                'uri'     => $uri,
                'headers' => $options['headers'] ?? [],
                'body'    => json2arr($options['body'] ?? []),
            ],
            microtime(true) - $startTime,
        );

        return $res;
    }
}