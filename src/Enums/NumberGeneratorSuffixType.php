<?php
/**
 * @author XJ.
 * @Date   2023/7/19 0019
 */

namespace Fatbit\HyperfTools\Enums;

use Fatbit\Enums\Annotations\EnumCase;
use Fatbit\Enums\Interfaces\EnumCaseInterface;
use Fatbit\Enums\Traits\EnumCaseGet;

enum NumberGeneratorSuffixType: int implements EnumCaseInterface
{
    use EnumCaseGet;

    #[EnumCase('随机数')]
    case RANDOM = 1;

    #[EnumCase('唯一id')]
    case UNIQID = 2;

    #[EnumCase('累加器')]
    case ADDER = 3;
}