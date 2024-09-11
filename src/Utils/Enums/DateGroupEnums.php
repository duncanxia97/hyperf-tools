<?php
/**
 * @author XJ.
 * Date: 2023/7/3 0003
 */

namespace Fatbit\HyperfTools\Utils\Enums;

use Fatbit\Enums\Annotations\EnumCase;
use Fatbit\Enums\Interfaces\EnumCaseInterface;
use Fatbit\Enums\Traits\EnumCaseGet;

enum DateGroupEnums: int implements EnumCaseInterface
{
    use EnumCaseGet;

    #[EnumCase(desc: '小时',)]
    case HOUR    = 1;

    #[EnumCase(desc: '天',)]
    case DAY     = 2;

    #[EnumCase(desc: '周',)]
    case WEEK    = 3;

    #[EnumCase(desc: '月',)]
    case MONTH   = 4;

    #[EnumCase(desc: '季度',)]
    case QUARTER = 5;

    #[EnumCase(desc: '年',)]
    case YEAR    = 6;

}
