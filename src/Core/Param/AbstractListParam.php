<?php
/**
 * @author XJ.
 * @Date   2023/7/19 0019
 */

namespace Fatbit\HyperfTools\Core\Param;

use Fatbit\HyperfTools\Utils\ArrayList;
use Hyperf\Contract\Arrayable;
use Hyperf\Contract\Jsonable;
use Fatbit\HyperfTools\Utils\Traits\FillParams;

abstract class AbstractListParam extends ArrayList implements Arrayable, Jsonable
{
    use FillParams;

    protected $list;

}