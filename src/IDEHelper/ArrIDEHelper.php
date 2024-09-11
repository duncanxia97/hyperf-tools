<?php
/**
 * Created by XJ.
 * Date: 2021/11/25
 */

namespace Hyperf\Utils;

use \Hyperf\Utils\Arr as BaseArr;

/**
 * Arr IDE帮助类
 *
 * @package Hyperf\Utils
 * @mixin BaseArr
 */
class Arr
{
    /**
     * 数组计算汇总
     * Created by XJ.
     * Date: 2021/11/25
     *
     * @param array                      $arr
     * @param string|callable|array|null $field
     * @param bool                       $isReturnArray
     *
     * @return float|int|array|float[]|int[]|mixed
     */
    public static function sum(array $arr, string|null|callable|array $field = null, bool $isReturnArray = false)
    {
        return arrSum($arr, $field, $isReturnArray);
    }

    /**
     * 数组索引
     * Created by XJ.
     * Date: 2021/8/17
     *
     * @param array                 $arr                数组
     * @param string|array|string[] $keys               索引值
     * @param bool                  $isAppend           是否追加数组
     * @param bool                  $columnKeys         数据集键
     * @param string                $keyGlue            联合索引拼接字符串
     * @param int|string|null       $fillNotExistKeyVal 填充不存在的键值(null:不填充)
     *
     * @return array|null
     */
    public static function indexBy(
        $arr,
        $keys,
        bool $isAppend = false,
        $columnKeys = true,
        string $keyGlue = '-',
        $fillNotExistKeyVal = 0
    ): ?array
    {
        return arrIndexBy($arr, $keys, $isAppend, $columnKeys, $keyGlue, $fillNotExistKeyVal);
    }

    /**
     * 数组排序
     * Created by XJ.
     * Date: 2021/12/15
     *
     * @param array           $arr        数组
     * @param string|callable $field      排序字段或者排序方法
     * @param int             $option     排序规则
     * @param bool            $descending 倒序
     *
     * @return array
     */
    public static function sortBy(
        array           $arr,
        string|callable $field,
        int             $option = SORT_REGULAR,
        bool            $descending = false
    ): array
    {
        return arrSortBy($arr, $field, $option, $descending);
    }

    /**
     * 对数组取一列数据或多列数据
     * Created by XJ.
     * Date: 2022/1/4
     *
     * @param array                      $arr
     * @param callable|string|array|null $column
     * @param callable|string|array|null $key
     *
     * @return array
     */
    public static function column(array                      $arr,
                                  callable|string|array|null $column,
                                  callable|string|array|null $key = null
    ): array
    {
        return arrColumn($arr, $column, $key);
    }


    /**
     * 数组key 转换
     * Created by XJ.
     * Date: 2022/1/10
     *
     * @param array        $arr    数组
     * @param int|callable $toType 转换类型 (1: 蛇形key, 2: 小驼峰key, 3: 大驼峰key)
     *
     * @return array
     */
    public static function keyConvert(array $arr, int|callable $toType = 1)
    {
        return arrKeyConvert($arr, $toType);
    }

    /**
     * 数组协程map
     * Created by XJ.
     * Date: 2022/1/10
     *
     * @param array    $arr   数组
     * @param callable $call  回调
     * @param int      $limit 协程数
     *
     * @return array
     */
    public static function coMap(array $arr, callable $call, int $limit = 5): array
    {
        return arrCoMap($arr, $call, $limit);
    }

    /**
     * 数组降级
     *
     * @author XJ.
     * Date: 2023/2/17 0017
     *
     * @param array $arr
     *
     * @return array
     */
    public static function demote(array $arr): array
    {
        return array_demote($arr);
    }

    /**
     * 转换成树形数据
     *
     * @author XJ.
     * @Date   2023/8/11 0011
     *
     * @param array         $list
     * @param               $pid
     * @param string        $pidField
     * @param string        $pkField
     * @param int|null      $maxLevel
     * @param string        $childrenName
     * @param int           $level
     * @param callable|null $toVal
     *
     * @return array|null
     */
    public static function toTree(
        array     $list,
                  $pid = 0,
        string    $pidField = 'pid',
        string    $pkField = 'id',
        ?int      $maxLevel = null,
        string    $childrenName = 'children',
        int       $level = 1,
        ?callable $toVal = null
    ): ?array
    {
        return arr2tree($list, $pid, $pidField, $pkField, $maxLevel, $childrenName, $level, $toVal);
    }

    /**
     * 转换成平级数组
     *
     * @author XJ.
     * @Date   2023/8/11 0011
     *
     * @param array $tree
     * @param string $
     * @param int   $level
     * @param array $arr
     *
     * @return array|null
     */
    public static function fromTree(array $tree, string $childrenName = 'children', int $level = 1, array &$arr = []): ?array
    {
        return tree2arr($tree, $childrenName, $level, $arr);
    }
}