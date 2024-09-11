<?php
/**
 * @author XJ.
 * Date: 2023/7/3 0003
 */

namespace Fatbit\HyperfTools\Utils\Enums;

use Fatbit\Enums\Annotations\EnumCase;
use Fatbit\Enums\Interfaces\EnumCaseInterface;
use Fatbit\Enums\Traits\EnumCaseGet;

enum FileSizeEnum implements EnumCaseInterface
{
    use EnumCaseGet;

    #[EnumCase('kb', ext: ['basic' => 1])]
    case KB;

    #[EnumCase('mb', ext: ['basic' => 2])]
    case MB;

    #[EnumCase('gb', ext: ['basic' => 3])]
    case GB;

    #[EnumCase('tb', ext: ['basic' => 4])]
    case TB;

    #[EnumCase('pb', ext: ['basic' => 5])]
    case PB;

    /**
     * 转换
     *
     * @author XJ.
     * Date: 2023/7/3 0003
     *
     * @param int $size
     *
     * @return int
     */
    public function convert(int $size): int
    {
        $size /= 1024 ** ($this->ext('basic') ?? 0);

        return to_number($size, 4);
    }


}
