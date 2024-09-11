<?php
/**
 * @author XJ.
 * Date: 2023/7/3 0003
 */

namespace Fatbit\HyperfTools\Aspects\ServiceEnables;

use Hyperf\Crontab\Process\CrontabDispatcherProcess;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

#[Aspect]
class CrontabEnableAspect extends AbstractAspect
{
    public array $classes = [
        CrontabDispatcherProcess::class.'::isEnable',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if (env('CRONTAB_ENABLE', true)) {
            return $proceedingJoinPoint->process();
        }
        return false;
    }
}