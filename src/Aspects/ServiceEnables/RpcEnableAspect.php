<?php
/**
 * @author XJ.
 * Date: 2023/7/3 0003
 */

namespace Fatbit\HyperfTools\Aspects\ServiceEnables;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Server\Port;
use Hyperf\Server\Server;

#[Aspect]
class RpcEnableAspect extends AbstractAspect
{
    public array $classes = [
        Server::class.'::sortServers',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $res = $proceedingJoinPoint->process();
        if (env('RPC_ENABLED', true)) {
            return $res;
        }
        $newRes = [];
        /** @var Port $server */
        foreach ($res as $server){
            if ($server->getName() == 'jsonrpc') {
                continue;
            }
            $newRes[] = $server;
        }
        return $newRes;
    }
}