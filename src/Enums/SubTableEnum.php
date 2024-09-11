<?php
/**
 * @author XJ.
 * @Date   2023/7/31 0031
 */


namespace Fatbit\HyperfTools\Enums;

use Fatbit\Enums\Annotations\EnumCase;
use Fatbit\Enums\Interfaces\EnumCaseInterface;
use Fatbit\Enums\Traits\EnumCaseGet;

enum SubTableEnum: int implements EnumCaseInterface
{
    use EnumCaseGet;

    #[EnumCase('年分表', ext: ['suffixFormat' => 'Y', 'ttlFormat' => 'year'])]
    const YEAR  = 1;

    #[EnumCase('月分表', ext: ['suffixFormat' => 'ym', 'ttlFormat' => 'month'])]
    const MONTH = 2;

    #[EnumCase('天分表', ext: ['suffixFormat' => 'd', 'ttlFormat' => 'day'])]
    const DAY   = 3;
}
