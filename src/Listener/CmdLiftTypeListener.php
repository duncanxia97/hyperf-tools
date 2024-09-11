<?php
/**
 * @author XJ.
 * @Date   2023/8/2 0002
 */

namespace Fatbit\HyperfTools\Listener;

use Fatbit\HyperfTools\Enums\RequestLife;
use Fatbit\HyperfTools\Tracer\TracerEntity;
use Hyperf\Command\Command;
use Hyperf\Command\Event\AfterHandle;
use Hyperf\Command\Event\BeforeHandle;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Tracer\SpanStarter;
use OpenTracing\Tracer;

#[Listener]
class CmdLiftTypeListener implements ListenerInterface
{

    use SpanStarter;

    protected Tracer $tracer;

    public function __construct(ContainerInterface $container)
    {
        $this->tracer = $container->get(Tracer::class);
    }

    public function listen(): array
    {
        return [
            BeforeHandle::class,
            AfterHandle::class,
        ];
    }

    public function process(object $event): void
    {

        if ($event instanceof BeforeHandle) {
            /** @var Command $command */
            $command = $event->getCommand();
            $span    = $this->startSpan('cmd');
            context_set('cmdLiftType:span', $span);
            RequestLife::CMD
                ->setLiftType()
                ->setTraceId($span->getContext()->getContext()->getTraceId())
                ->setBeginPath(
                    $command->getName()
                );
        }
        if ($event instanceof AfterHandle) {
            $span = context_get('cmdLiftType:span');
            $span->finish();
            TracerEntity::getInstance()->clear();
        }
    }
}