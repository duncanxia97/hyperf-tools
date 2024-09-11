<?php
/**
 * @author XJ.
 * @Date   2023/8/25 0025
 */

namespace Fatbit\HyperfTools\Enums;

use Fatbit\Enums\Annotations\EnumCase;
use Fatbit\Enums\Interfaces\EnumCaseInterface;
use Fatbit\Enums\Traits\EnumCaseGet;

/**
 * @author XJ.
 * @Date   2023/8/25 0025
 * @method string ask()
 * @method string able()
 */
enum EnableEnum: int implements EnumCaseInterface
{
    use EnumCaseGet;

    #[EnumCase('启用', ext: ['ask' => '是', 'able' => '可用'])]
    case YES = 1;

    #[EnumCase('未启用', ext: ['ask' => '否', 'able' => '不可用'])]
    case NO = 2;

}
