<?php
/**
 * @author XJ.
 * Date: 2023/4/14 0014
 */

namespace Fatbit\HyperfTools\Aspects\TracerRecords;

use Fatbit\HyperfTools\Tracer\TracerEntity;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Redis\RedisConnection;

#[Aspect]
class RedisTraceAspect extends AbstractAspect
{
    public array $classes = [
        RedisConnection::class . '::__call'
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $startTime = microtime(true);
        $res       = $proceedingJoinPoint->process();
        $args      = $proceedingJoinPoint->getArguments();
        $command   = $args[0] ?? '';
        TracerEntity::getInstance()
            ->apeendRedis(
                $command,
                arr2json($args[1] ?? ''),
                (microtime(true) - $startTime)*1000
            );

        return $res;
    }
}