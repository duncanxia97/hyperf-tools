<?php
/**
 * @author XJ.
 * Date: 2023/7/5 0005
 */

namespace Fatbit\HyperfTools\Aspects;

use Fatbit\HyperfTools\Enums\RequestLife;
use Fatbit\HyperfTools\Tracer\TracerEntity;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Command\Command;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Crontab\Strategy\Executor;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpMessage\Server\Request as Psr7Request;
use Hyperf\HttpServer\Server as HttpServer;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Process\AbstractProcess;
use Hyperf\RpcServer\Server as RpcServer;
use Hyperf\Tracer\SpanStarter;
use OpenTracing\Tracer;
use Psr\Log\LoggerInterface;

#[Aspect]
class AddLiftTypeAspect extends AbstractAspect
{
    use SpanStarter;


    protected Tracer $tracer;


    protected LoggerInterface $loggerFactory;

    public array              $classes = [
//        Command::class . '::execute', // 无法实现,参考:CmdLiftTypeListener
        Executor::class . '::decorateRunnable',
        ConsumerMessage::class . '::consumeMessage',
//        HttpServer::class . '::onRequest', // 单元测试时, 由于请求方式不同无法实现 参考:HttpCoreMiddleware
        AbstractProcess::class . '::bindServer',
        RpcServer::class . '::onReceive',
    ];


    public function __construct(ContainerInterface $container)
    {
        $this->tracer        = $container->get(Tracer::class);
        $this->loggerFactory = make(LoggerFactory::class)->get('log', 'default');

    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->class[Command::class])) {
            if ($proceedingJoinPoint->methodName === 'handle') {
                /** @var \Hyperf\Command\Command $instance */
                $instance = $proceedingJoinPoint->getInstance();
                $span     = $this->startSpan('cmd');
                RequestLife::CMD
                    ->setLiftType()
                    ->setTraceId($span->getContext()->getContext()->getTraceId())
                    ->setBeginPath(
                        $instance->getName()
                    );
            }
        }

        switch (true) {
            case $proceedingJoinPoint->className === HttpServer::class && $proceedingJoinPoint->methodName === 'onRequest':
                // rest
                // HttpTraceMiddleware中间件已加载
                /** @var HttpServer $instance */
                $instance                       = $proceedingJoinPoint->getInstance();
                $args                           = $proceedingJoinPoint->getArguments();
                $httpServerRef                  = new \ReflectionClass($instance);
                $initRequestAndResponse         = $httpServerRef->getMethod('initRequestAndResponse');
                $initRequestAndResponseCallback = $initRequestAndResponse->getClosure($instance);
                /** @var Psr7Request $psr7Request */
                [$psr7Request] = $initRequestAndResponseCallback(...$args);
                $requestPath = $psr7Request->getUri()->getPath();
                $span        = $this->startSpan('rest');
                RequestLife::HTTP
                    ->setLiftType($psr7Request->getServerParams()['request_time_float'] ?? null)
                    ->setTraceId($span->getContext()->getContext()->getTraceId())
                    ->setBeginPath($requestPath);
                break;
            case $proceedingJoinPoint->className === AbstractProcess::class && $proceedingJoinPoint->methodName === 'bindServer':
                // 进程
                $span = $this->startSpan('process');
                RequestLife::PROCESS
                    ->setLiftType()
                    ->setTraceId($span->getContext()->getContext()->getTraceId());
                break;
            case $proceedingJoinPoint->className === RpcServer::class && $proceedingJoinPoint->methodName === 'onReceive':
                // rpc
                // JsonRpcAspect 切面已加载
                $span = $this->startSpan('rpc');
                RequestLife::RPC
                    ->setLiftType()
                    ->setTraceId($span->getContext()->getContext()->getTraceId());
                break;
            case $proceedingJoinPoint->className === Executor::class && $proceedingJoinPoint->methodName === 'execute':
                // 定时任务
                $span = $this->startSpan('cron');
                /** @var \Hyperf\Crontab\Crontab $crontab */
                $crontab = $proceedingJoinPoint->getArguments()[0];
                $name    = $crontab->getName();
                if (is_array($crontab->getCallback())) {
                    $name .= ':' . implode('::', $crontab->getCallback());
                }
                if (is_string($crontab->getCallback())) {
                    $name .= ':' . $crontab->getCallback();
                }
                if (is_callable($crontab->getCallback())) {
                    $name .= ':Closure';
                }

                RequestLife::CRON
                    ->setLiftType()
                    ->setTraceId($span->getContext()->getContext()->getTraceId())
                    ->setBeginPath($name);
                break;
            case $proceedingJoinPoint->className === ConsumerMessage::class && $proceedingJoinPoint->methodName === 'consumeMessage':
                // mq消费者
                // ListenAmqpConsumerExecuteAspect 切面已加载
                $span = $this->startSpan('mq');
                /** @var ConsumerMessage $instance */
                $instance = $proceedingJoinPoint->getInstance();
                RequestLife::MQ
                    ->setLiftType()
                    ->setTraceId($span->getContext()->getContext()->getTraceId())
                    ->setBeginPath(get_class($instance));
                break;
            case $proceedingJoinPoint->className === Command::class && $proceedingJoinPoint->methodName === 'execute':
                // 命令行
                // 无法切入因为不在一个主进程里面详细参考:CmdLiftTypeListener
                /** @var Command $instance */
                $instance = $proceedingJoinPoint->getInstance();
                $span     = $this->startSpan('cmd');
                RequestLife::CMD
                    ->setLiftType()
                    ->setTraceId($span->getContext()->getContext()->getTraceId())
                    ->setBeginPath(
                        $instance->getName()
                    );
                break;

        }


        $res = $proceedingJoinPoint->process();

        if (isset($span)) {
            $span->finish();
        }

        TracerEntity::getInstance()->clear();

        return $res;

    }
}