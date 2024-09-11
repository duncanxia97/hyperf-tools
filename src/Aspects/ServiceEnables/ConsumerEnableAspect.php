<?php
/**
 * @author XJ.
 * Date: 2023/7/3 0003
 */

namespace Fatbit\HyperfTools\Aspects\ServiceEnables;

use Hyperf\Amqp\Builder;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use PhpAmqpLib\Channel\AMQPChannel;

#[Aspect]
class ConsumerEnableAspect extends AbstractAspect
{
    public array $classes = [
        ConsumerMessage::class . '::isEnable',
        Builder::class.'::declare',
    ];


    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $flag = config('amqp.enable', env('MQ_ENABLE', true));
        if (!$flag && $proceedingJoinPoint->className  == Builder::class) {
            // 判断是否为qmqp通道
            /** @var ?AMQPChannel $channel */
            $channel = $proceedingJoinPoint->getArguments()[1] ?? null;
            $channel?->close();
            return;
        }

        if ($flag) {
            return $proceedingJoinPoint->process();
        }

        return false;
    }
}