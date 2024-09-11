<?php
/**
 * @author XJ.
 * @Date   2023/8/23 0023
 */

namespace Fatbit\HyperfTools\Aspects\TracerRecords;

use Fatbit\HyperfTools\Tracer\TracerEntity;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\JsonRpc\TcpServer;
use Hyperf\RpcClient\AbstractServiceClient;
use Hyperf\RpcClient\Client;
use Hyperf\RpcMultiplex\Client as RMClient;
use Hyperf\RpcMultiplex\TcpServer as RMTcpServer;
use Hyperf\Tracer\SpanStarter;
use Hyperf\Tracer\SpanTagManager;
use OpenTracing\Tracer;


//#[Aspect]
class RpcTraceAspect extends AbstractAspect
{
    use SpanStarter;

    public array $classes = [
        AbstractServiceClient::class . '::__generateRpcPath',
        AbstractServiceClient::class . '::__generateData',
        Client::class . '::send',
        RMClient::class . '::send',
        TcpServer::class . '::buildJsonRpcRequest',
        RMTcpServer::class . '::buildRequest',
    ];

    public function __construct(
        protected Tracer $tracer,
        protected SpanTagManager $spanTagManager,
    )
    {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $startTime = microtime(true);
        $res = $proceedingJoinPoint->process();
        $endTime = microtime(true);
        get_class($proceedingJoinPoint->getInstance()).':'.$proceedingJoinPoint->getReflectMethod();

        TracerEntity::getInstance()->apeendRpc([], $endTime - $startTime);

        return $res;
    }
}