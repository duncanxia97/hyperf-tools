<?php
/**
 * @author XJ.
 * Date: 2022/11/11 0011
 */

namespace Fatbit\HyperfTools\Core\RedisKey\Interfaces;

use UnitEnum;

/**
 *
 * @property string $value
 * @property string $name
 * @extends \StringBackedEnum
 * @extends UnitEnum
 *
 */
interface RedisKeyInterface
{
    /**
     * 获取缓存前缀
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     * @return string
     */
    public function getPrefix(): string;

    /**
     * 获取缓存注释
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     * @return string
     */
    public function getDesc(): string;

    /**
     * 缓存时间
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     *
     * @param int|callable $ttl
     *
     * @return $this
     */
    public function ttl(int|callable $ttl = 0): static;

    /**
     * 是否开启nx
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     *
     * @param bool $isNx
     *
     * @return $this
     */
    public function nx(bool $isNx = true): static;

    /**
     * 是否强制获取(不走缓存并且刷新缓存)
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     *
     * @param bool|callable $isForce
     *
     * @return $this
     */
    public function force(bool|callable $isForce = true): static;

    /**
     * 设置缓存键
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     *
     * @param string|callable $key
     *
     * @return $this
     */
    public function key(string|callable $key): static;

    /**
     * 获取缓存
     *
     * @author XJ.
     * Date: 2022/11/22 0022
     * @return array|float|int|object|string|null
     */
    public function get();

    /**
     * 获取缓存数据
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     *
     * @param callable|string|array $callback 缓存数据
     *
     * @return array|float|int|object|string|null
     */
    public function getCache(callable|string|array $callback);

    /**
     * 获取缓存的有效期
     *
     * @author XJ.
     * Date: 2022/12/6 0006
     * @return int|null
     */
    public function getCacheTtl(): ?int;

    /**
     * 是否存在缓存
     *
     * @author XJ.
     * Date: 2022/12/7 0007
     * @return bool
     */
    public function existsCache(): bool;

    /**
     * 累加缓存
     *
     * @author XJ.
     * @Date   2024/5/31 星期五
     *
     * @param int|callable $step
     *
     * @return false|int
     * @throws \RedisException
     */
    public function incrBy(int|callable $step = 1): false|int;

    /**
     * 累减缓存
     *
     * @author XJ.
     * @Date   2024/5/31 星期五
     *
     * @param int|callable $step
     *
     * @return false|int
     * @throws \RedisException
     */
    public function decrBy(int|callable $step = 1): false|int;

    /**
     * 累加缓存(并且刷新缓存时间)
     *
     * @author XJ.
     * Date: 2022/12/7 0007
     * @return false|int
     */
    public function incCache(int|callable $step = 1): false|int;

    /**
     * 累减缓存(并且刷新缓存时间)
     *
     * @author XJ.
     * Date: 2022/12/7 0007
     * @return false|int
     */
    public function decrCache(int|callable $step = 1): false|int;

    /**
     * 清除缓存
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     *
     * @param string|callable|null $key 需要清除的key
     *
     * @return false|int
     */
    public function clearCache(null|string|callable $key = null): bool|int;

    /**
     * 获取缓存时间
     *
     * @author XJ.
     * Date: 2022/11/18 0018
     * @return int
     */
    public function getTtl(): int;

    /**
     * 获取是否开启nx
     *
     * @author XJ.
     * Date: 2022/11/18 0018
     * @return bool
     */
    public function getIsNx(): bool;

    /**
     * @author XJ.
     * @Date   2024/5/31 星期五
     * @template T
     *
     * @param T                                $condition
     * @param callable(static, T): static      $callback
     * @param callable(static, T): static|null $default
     *
     * @return static
     */
    public function when($condition, callable $callback, callable|null $default = null): static;

}