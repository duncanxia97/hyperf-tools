<?php
/**
 * @author XJ.
 * Date: 2022/11/11 0011
 */

namespace Fatbit\HyperfTools\Utils\RedisKey;


use Fatbit\HyperfTools\Utils\Traits\MasterContextInstance;

/**
 * redis建列管理器
 *
 * @author XJ.
 * Date: 2022/11/11 0011
 */
final class RedisKeyColumnManager
{
    use MasterContextInstance;

    /**
     * @var array{string:RedisKeyColumn}
     */
    private array $redisKeyColumns = [];

    /**
     * 获取redis key 列
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     *
     * @param string $enumCase 序列化的枚举 例如: serialize(Suit::Hearts)
     *
     * @return RedisKeyColumn
     */
    public static function getRedisKeyColumn(string $enumCase): RedisKeyColumn
    {
        if (isset(self::getInstance()->redisKeyColumns[$enumCase])) {
            return self::getInstance()->redisKeyColumns[$enumCase];
        }

        return self::getInstance()->redisKeyColumns[$enumCase] = new RedisKeyColumn();
    }

    /**
     * 刷新获取新的redis key 列
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     *
     * @param string $enumCase 序列化的枚举 例如: serialize(Suit::Hearts)
     *
     * @return RedisKeyColumn
     */
    public static function refreshRedisKeyColumn(string $enumCase): RedisKeyColumn
    {
        return self::getInstance()->redisKeyColumns[$enumCase] = new RedisKeyColumn();
    }

    /**
     * 删除redis key 列
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     *
     * @param string $enumCase 序列化的枚举 例如: serialize(Suit::Hearts)
     *
     * @return bool
     */
    public static function delRedisKeyColumn(string $enumCase): bool
    {
        if (isset(self::getInstance()->redisKeyColumns[$enumCase])) {
            unset(self::getInstance()->redisKeyColumns[$enumCase]);

            return true;
        }

        return false;
    }

}