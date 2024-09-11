<?php
/**
 * @author XJ.
 * Date: 2023/7/3 0003
 */

namespace Fatbit\HyperfTools\Utils\Enums;

use Fatbit\Enums\Annotations\EnumCase;
use Fatbit\Enums\Interfaces\EnumCaseInterface;
use Fatbit\Enums\Traits\EnumCaseGet;

/**
 * @author XJ.
 * Date: 2023/7/3 0003
 */
enum TimeSecondEnum implements EnumCaseInterface
{
    use EnumCaseGet;

    #[EnumCase('秒基数', ext: ['second' => 1])]
    case BASE;

    #[EnumCase('分钟', ext: ['second' => 60])]
    case MINUTE;

    #[EnumCase('天', ext: ['second' => 60 * 24])]
    case DAY;

    #[EnumCase('周', ext: ['second' => 7 * 60 * 24])]
    case WEEK;

    #[EnumCase('月')]
    case MONTH;

    #[EnumCase('年')]
    case YEAR;

    /**
     * 获取秒
     *
     * @author XJ.
     * Date: 2023/7/3 0003
     *
     * @param int|null $sinceTime 开始于(比如不填就是以今天计算月的秒数或今年的秒数)
     *
     * @return int
     */
    public function second(int $sinceTime = null): int
    {
        return match ($this) {
            self::MONTH => self::DAY->value() * date('t', strtotime(date('Y-m-01', $sinceTime))),
            self::YEAR  => self::DAY->value() * ((int)date('z', strtotime(date('Y-12-31', $sinceTime))) + 1),
            default     => $this->getSecond(),
        };
    }

    /**
     * 获取值
     *
     * @author XJ.
     * Date: 2023/7/3 0003
     */
    public function value(): int
    {
        return $this->second() * self::BASE->second();
    }

}
