<?php
/**
 * @author XJ.
 * @Date   2023/7/31 0031
 */

namespace Fatbit\HyperfTools\Aspects\TracerRecords;

use Fatbit\HyperfTools\Tracer\TracerEntity;
use Hyperf\Amqp\Message\ProducerMessageInterface;
use Hyperf\Amqp\Producer;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

#[Aspect]
class MQProducerTraceAspect extends AbstractAspect
{

    public array $classes = [
        Producer::class . "::produce",
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /************处理追踪链路***********/
        // 获取队列信息
        /** @var ProducerMessageInterface $producerMessage */
        $producerMessage = $proceedingJoinPoint->getArguments()[0];
        $annotation      = AnnotationCollector::getClassAnnotation(get_class($producerMessage), \Hyperf\Amqp\Annotation\Producer::class);
        if ($annotation) {
            $annotation->routingKey && $producerMessage->setRoutingKey($annotation->routingKey);
            $annotation->exchange && $producerMessage->setExchange($annotation->exchange);
        }
        $startTime = microtime(true);

        $result = $proceedingJoinPoint->process();

        TracerEntity::getInstance()->apeendMq(
            get_class($producerMessage),
            $producerMessage->getExchange(),
            $producerMessage->getRoutingKey(),
            $producerMessage->payload(),
            $producerMessage->getProperties(),
            $producerMessage->getType(),
            $producerMessage->getPoolName(),
            microtime(true) - $startTime
        );

        return $result;
    }
}