<?php
/**
 * @author XJ.
 * Date: 2023/7/3 0003
 */

namespace Fatbit\HyperfTools\Enums;

use Fatbit\Enums\Annotations\EnumCase;
use Fatbit\Enums\Interfaces\EnumCaseInterface;
use Fatbit\Enums\Traits\EnumCaseGet;
use Fatbit\HyperfTools\Utils\MasterContext;

/**
 * 请求生命周期类型
 *
 * @author XJ.
 * Date: 2023/7/3 0003
 */
enum RequestLife: int implements EnumCaseInterface
{
    use EnumCaseGet;

    #[EnumCase('http请求')]
    case HTTP = 1;

    #[EnumCase('process进程')]
    case PROCESS = 2;

    #[EnumCase('rpc请求')]
    case RPC = 3;

    #[EnumCase('cron定时任务')]
    case CRON = 4;

    #[EnumCase('MQ队列')]
    case MQ = 5;

    #[EnumCase('cmd命令行')]
    case CMD = 6;

    public function setLiftType(?float $startTime = null)
    {
        MasterContext::set(self::class, $this->value);
        MasterContext::set(self::class . ':_startTime', $startTime ?: microtime(true));

        return $this;
    }

    public function setTraceId(string $traceId): static
    {
        MasterContext::set(self::class . ':_traceId', $traceId);

        return $this;
    }

    public static function getTraceId(): ?string
    {
        return MasterContext::get(self::class . ':_traceId');
    }

    public function setBeginPath(string $path): static
    {
        MasterContext::set(self::class . ':_beginPath', $this->name . ':' . $path);

        return $this;
    }

    public static function getBeginPath(): ?string
    {
        return MasterContext::get(self::class . ':_beginPath');
    }

    /**
     * 获取请求生命类型
     *
     * @author XJ.
     * Date: 2023/7/3 0003
     * @return int
     */
    public static function getLiftType(): ?int
    {
        return MasterContext::get(self::class);
    }

    /**
     * 获取请求类型开始时间
     *
     * @author XJ.
     * Date: 2023/7/3 0003
     * @return float|null
     */
    public static function getLiftStartTime(): ?float
    {
        return MasterContext::get(self::class . ':_startTime');
    }

    /**
     * 是否是该生命类型
     *
     * @author XJ.
     * @Date   2023/7/19 0019
     * @return bool
     */
    public function isThisIt(): bool
    {
        return $this->value == context_get(self::class);
    }
}
