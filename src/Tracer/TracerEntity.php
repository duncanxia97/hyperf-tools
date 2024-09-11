<?php
/**
 * @author XJ.
 * @Date   2024/9/11 星期三
 */

namespace Fatbit\HyperfTools\Tracer;

use Fatbit\HyperfTools\Enums\RequestLife;
use Fatbit\HyperfTools\Utils\Traits\MasterContextInstance;

class TracerEntity
{
    use MasterContextInstance;

    protected array $debug = [];

    public function apeendSql(string $sql, float $execTime)
    {
        $this->appendDebug(
            'sql',
            [
                'info' => $sql,
            ],
            $execTime
        );
    }

    public function apeendRpc(array $debug, float $time)
    {
        $this->appendDebug('rpc', $debug, $time);
    }

    public function apeendMq(
        string       $producer,
        string       $exchange,
        string|array $routingKey,
        string       $payload,
        array        $properties,
        string       $type,
        string       $poolName,
        float        $time,
    )
    {
        $this->appendDebug(
            'mq',
            [
                'producer'   => $producer,
                'exchange'   => $exchange,
                'routingKey' => $routingKey,
                'payload'    => $payload,
                'properties' => $properties,
                'type'       => $type,
                'poolName'   => $poolName,
            ],
            $time
        );
    }

    public function apeendHttp(array $debug, float $time)
    {
        $this->appendDebug('http', $debug, $time);
    }

    public function apeendRedis(string $command, string $args, float $time)
    {
        $this->appendDebug(
            'redis',
            [
                'command' => $command,
                'args'    => $args,
            ],
            $time
        );
    }

    public function apeendEs(
        string       $index,
        string       $method,
        string       $uri,
        array        $params,
        array|string $body,
        array        $options,
        float        $time
    )
    {
        $this->appendDebug(
            'es',
            [
                'index'  => $index,
                'method' => $method,
                'uri'    => $uri,
                'params' => $params,
                'body'   => arr2json($body),
                'option' => $options,
            ],
            $time
        );
    }

    protected function appendDebug(string $type, array $debug, float $execTime)
    {
        $this->debug[$type][] = $debug + [
                'execTime' => to_number($execTime, 6) . 'ms',
                'endTime'  => microtime(true),
            ];
    }

    public function getDebug(): array
    {
        return $this->debug;
    }

    public function clear()
    {
        $this->debug = [];
        self::resetInstance();
    }

    public function getDebugInfo()
    {
        $debugInfo = [
            'memory'    => sprintf("%3.2f", memory_get_usage() / 1024 / 1024) . "M",
            'runtime'   => microtime(true) - RequestLife::getLiftStartTime(),
            'slowLog'   => TracerEntity::getInstance()->getDebug(),
            'traceId'   => RequestLife::getTraceId(),
            'beginPath' => RequestLife::getBeginPath(),
            'endTime'   => microtime(true)
        ];
        $this->clear();

        return $debugInfo;
    }

}