<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Fatbit\HyperfTools\Listener;

use Fatbit\HyperfTools\Tracer\TracerEntity;
use Hyperf\Database\Events\QueryExecuted;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class DbQueryExecutedListener implements ListenerInterface
{

    public function listen(): array
    {
        return [
            QueryExecuted::class,
        ];
    }

    /**
     * @param QueryExecuted $event
     */
    public function process(object $event): void
    {
        if ($event instanceof QueryExecuted) {
            $sql      = $event->sql;
            $bindings = array_map(
                function ($binding) {
                    if (is_numeric($binding)) {
                        return $binding;
                    }
                    if (is_string($binding)) {
                        return '"' . $binding . '"';
                    }
                    if ($binding instanceof \DateTime) {
                        return '"' . $binding->format('Y-m-d H:i:s') . '"';
                    }

                    return $binding;
                },
                $event->bindings
            );
            $sql      = str_replace(['%', '?'], ['%%', '%s'], $sql);
            $sql      = sprintf($sql, ...$bindings);

            TracerEntity::getInstance()
                ->apeendSql(
                    $sql,
                    $event->time,
                );
        }
    }
}
