<?php
/**
 * @author XJ.
 * @Date   2023/7/13 0013
 */

namespace Fatbit\HyperfTools\Utils\Traits;

use Fatbit\HyperfTools\Utils\MasterContext;

trait MasterContextInstance
{

    /**
     * 获取单列
     *
     * @author XJ.
     * Date: 2022/12/7 0007
     * @return static
     */
    public static function getInstance(): static
    {
        if (MasterContext::has(self::class)) {
            return MasterContext::get(self::class);
        }
        $static = new static();
        MasterContext::set(static::class, $static);

        return $static;
    }

    /**
     * 创建一个新的单列
     *
     * @author XJ.
     * Date: 2022/12/7 0007
     * @return static
     */
    public static function newInstance(): static
    {
        self::resetInstance();

        return self::getInstance();
    }

    /**
     * 重置单列
     *
     * @author XJ.
     * Date: 2022/12/7 0007
     */
    public static function resetInstance()
    {
        MasterContext::destroy(static::class);
    }

    protected function __construct()
    {
    }

    protected function __clone()
    {
    }
}