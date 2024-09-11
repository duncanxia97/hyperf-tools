<?php
/**
 * @author XJ.
 * Date: 2023/7/3 0003
 */

namespace Fatbit\HyperfTools\Core\Param;

use Hyperf\Contract\Arrayable;
use Hyperf\Contract\Jsonable;
use Fatbit\HyperfTools\Utils\Traits\FillParams;

abstract class AbstractParam implements Arrayable, Jsonable
{
    use FillParams;
}