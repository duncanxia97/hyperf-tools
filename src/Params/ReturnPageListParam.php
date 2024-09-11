<?php
/**
 * @author XJ.
 * @Date   2023/7/19 0019
 */

namespace Fatbit\HyperfTools\Params;

use Fatbit\HyperfTools\Core\Param\AbstractListParam;
use Hyperf\Paginator\LengthAwarePaginator;

class ReturnPageListParam extends AbstractListParam
{

    /**
     * @var int 总数
     */
    protected ?int $total = null;

    public function __construct(null|array|LengthAwarePaginator $data = null)
    {
        if ($data instanceof LengthAwarePaginator) {
            $this->total = $data->total();
            $this->list  = $data->items();
        } else {
            parent::__construct($data);
        }
    }

    /**
     * 列表
     *
     * @author XJ.
     * Date: 2022/11/24 0024
     * @return array
     */
    public function &list()
    {
        return $this->list;
    }
}