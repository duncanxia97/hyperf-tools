<?php
/**
 * @author XJ.
 * @Date   2023/7/31 0031
 */

namespace Fatbit\HyperfTools\Aspects\TracerRecords;

use Elasticsearch\Client as ESClient;
use Elasticsearch\Endpoints\AbstractEndpoint;
use Fatbit\HyperfTools\Tracer\TracerEntity;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

#[Aspect]
class ESClientTraceAspect extends AbstractAspect
{

    public array $classes = [
        ESClient::class . '::performRequest',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /************处理ES追踪链路***********/
        $startTime = microtime(true);
        $result    = $proceedingJoinPoint->process();
        /** @var AbstractEndpoint $endpoint */
        $endpoint = $proceedingJoinPoint->getArguments()[0];
        TracerEntity::getInstance()->apeendEs(
            $endpoint->getIndex() ?? '',
            $endpoint->getMethod(),
            $endpoint->getURI(),
            $endpoint->getParams(),
            $endpoint->getBody(),
            $endpoint->getOptions(),
                (microtime(true) - $startTime)*1000
        );

        return $result;
    }
}