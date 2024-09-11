<?php
/**
 * Created by XJ.
 * Date: 2021/11/17
 */

namespace Hyperf\Database\Model;

use Fatbit\HyperfTools\Exceptions\EmptyListException;
use Fatbit\HyperfTools\Params\ReturnPageListParam;
use Hyperf\Database\Model\Builder as BaseBuilder;
use Hyperf\Database\Query\Expression;
use Hyperf\HttpServer\Contract\RequestInterface;
/**
 * Builder IDE帮助类
 *
 * @package Hyperf\Database\Model
 * @mixin BaseBuilder
 */
class Builder
{

    /**
     * @author XJ.
     * @Date   2023/8/3 0003
     *
     * @param array|string $columns
     * @param bool         $isThrow
     *
     * @return ReturnPageListParam
     */
    public function pageData(array|string $columns = ['*'], bool $isThrow = false): ReturnPageListParam
    {
        $page     = make(RequestInterface::class)?->input('page', 1) ?: 1;
        $pageSize = make(RequestInterface::class)?->input('pageSize', 15) ?: 15;
        $total    = null;
        if ($page == 1) {
            /** @var Builder $this */
            $total = $this->toBase()->getCountForPagination();
            if ($total == 0) {
                $isThrow && throw new EmptyListException();

                return new ReturnPageListParam(
                    [
                        'list'  => [],
                        'total' => 0,
                    ]
                );
            }
        }

        return new ReturnPageListParam(
            [
                'list'  => $this->forPage($page, $pageSize)->get($columns)->all(),
                'total' => $total,
            ]
        );
    }
}