<?php
/**
 * @author XJ.
 * @Date   2024/9/11 星期三
 */

namespace Fatbit\HyperfTools\Utils\Traits;

use Fatbit\HyperfTools\Core\RedisKey\Interfaces\RedisKeyInterface;
use Hyperf\Stringable\Str;

/**
 * @implements RedisKeyInterface
 */
trait GetRedisKey
{

    /**
     * 获取 redis key列
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     * @return RedisKeyColumn
     */
    protected function getRedisKeyColumn(): RedisKeyColumn
    {
        return RedisKeyColumnManager::getRedisKeyColumn(serialize($this));
    }

    /**
     * 删除redis key
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     * @return bool
     */
    protected function delRedisKeyColumn(): bool
    {
        return RedisKeyColumnManager::delRedisKeyColumn(serialize($this));
    }

    /**
     * 获取redis key 前缀
     *
     * @author XJ.
     * Date: 2022/10/6 0006
     *
     * @param $get
     *
     * @return RedisKeyPrefix|null
     */
    protected function getRedisKeyPrefix($get = 0): ?RedisKeyPrefix
    {
        $res = (new ReflectionEnum($this))
                   ->getAttributes(RedisKeyPrefix::class)[$get] ?? null;

        return $res?->newInstance();
    }

    /**
     * 获取redis key
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     *
     * @param $get
     *
     * @return RedisKey|null
     * @throws \ReflectionException
     */
    protected function getRedisKey($get = 0): ?RedisKey
    {
        $res = (new ReflectionEnumUnitCase($this, $this->name))
                   ->getAttributes(RedisKey::class)[$get] ?? null;

        return $res?->newInstance();
    }

    /**
     * 获取缓存前缀
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     * @return string
     */
    public function getPrefix(): string
    {
        $prefix        = $this->getRedisKeyPrefix()?->prefix ?? '';
        $servicePrefix = $this->getRedisKey()?->servicePrefix ?? '';
        $prefix        = $prefix == '' ? '' : $prefix . ':';
        $servicePrefix = $servicePrefix == '' ? '' : $servicePrefix . ':';

        return '{' . $prefix . $servicePrefix . $this->value . ':}';
    }

    /**
     * 获取缓存注释
     *
     * @author XJ.
     * Date: 2022/11/11 0011
     * @return string
     */
    public function getDesc(): string
    {
        return $this->getRedisKey()?->desc ?: ($this->getRedisKeyPrefix()?->desc ?? '');
    }

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
    public function ttl(int|callable $ttl = 0): static
    {
        $this->getRedisKeyColumn()->ttl = value($ttl);

        return $this;
    }

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
    public function nx(bool $isNx = true): static
    {
        $this->getRedisKeyColumn()->isNx = $isNx;

        return $this;
    }

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
    public function force(bool|callable $isForce = true): static
    {
        $this->getRedisKeyColumn()->force = value($isForce);

        return $this;
    }

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
    public function key(string|callable $key): static
    {
        $this->getRedisKeyColumn()->key = value($key);

        return $this;
    }

    /**
     * 获取缓存
     *
     * @author XJ.
     * Date: 2022/11/22 0022
     * @return array|float|int|object|string|null
     */
    public function get()
    {
        $redis = redis();
        $res   = json2arr($redis->get($this->getKey()));
        RedisKeyColumnManager::refreshRedisKeyColumn(serialize($this));

        return $res;
    }

    /**
     * 获取缓存键
     *
     * @author XJ.
     * Date: 2022/11/22 0022
     * @return string
     */
    public function getKey(): string
    {
        return $this->getPrefix() . $this->getRedisKeyColumn()->key;
    }

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
    public function getCache(mixed $callback)
    {
        $res = getCache(
            key     : $this->getKey(),
            callback: $callback,
            ttl     : $this->getTtl(),
            isForce : $this->getRedisKeyColumn()->force,
            isNx    : $this->getIsNx(),
        );
        RedisKeyColumnManager::refreshRedisKeyColumn(serialize($this));

        return $res;
    }

    /**
     * 获取缓存的有效期
     *
     * @author XJ.
     * Date: 2022/12/6 0006
     * @return int|null
     */
    public function getCacheTtl(): ?int
    {
        return redis()->ttl($this->getKey()) ?: null;
    }

    /**
     * 时候存在缓存
     *
     * @author XJ.
     * Date: 2022/12/7 0007
     * @return bool
     */
    public function existsCache(): bool
    {
        return (bool)redis()->exists($this->getKey());
    }

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
    public function incrBy(int|callable $step = 1): false|int
    {
        $val = redis()->incrBy($this->getKey(), value($step));
        RedisKeyColumnManager::refreshRedisKeyColumn(serialize($this));

        return $val;
    }

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
    public function decrBy(int|callable $step = 1): false|int
    {
        $val = redis()->decrBy($this->getKey(), value($step));
        RedisKeyColumnManager::refreshRedisKeyColumn(serialize($this));

        return $val;
    }

    /**
     * 累加缓存(并且刷新缓存时间)
     *
     * @author XJ.
     * Date: 2022/12/7 0007
     *
     * @param int|callable       $step
     * @param int|callable|false $ttl
     *
     * @return false|int
     * @throws \RedisException
     */
    public function incCache(int|callable $step = 1): false|int
    {
        $script = <<<LUA
            local key = KEYS[1]
            local exp = tonumber(ARGV[2])
            local val = redis.call("incrBy", key, ARGV[1])
            if exp > 0 then 
                redis.call("expire", key, exp)
            else 
                redis.call("PERSIST", key)
            end
            return val
        LUA;

        $val = redis()->eval($script, [$this->getKey(), value($step), $this->getTtl()], 1);
        RedisKeyColumnManager::refreshRedisKeyColumn(serialize($this));

        return $val;
    }

    /**
     * 累减缓存(并且刷新缓存时间)
     *
     * @author XJ.
     * Date: 2022/12/7 0007
     * @return false|int
     */
    public function decrCache(int|callable $step = 1): false|int
    {
        $script = <<<LUA
            local key = KEYS[1]
            local exp = tonumber(ARGV[2])
            local val = redis.call("decrBy", key, ARGV[1])
            if exp > 0 then 
                redis.call("expire", key, exp)
            else 
                redis.call("PERSIST", key)
            end
            return val
        LUA;

        $val = redis()->eval($script, [$this->getKey(), value($step), $this->getTtl()], 1);
        RedisKeyColumnManager::refreshRedisKeyColumn(serialize($this));

        return $val;
    }

    /**
     * 获取所有keys
     *
     * @author XJ.
     * Date: 2022/11/14 0014
     *
     * @param string|callable|null $key
     *
     * @return array
     * @throws \RedisException
     */
    public function allKeys(null|string|callable $key = null): array
    {
        $res    = redis()->keys($this->getPrefix() . (value($key) ?? '*'));
        $prefix = config('database.redis.options.prefix');

        return array_map(fn($v) => $prefix && Str::startsWith($v, $prefix) ? Str::after($v, $prefix) : $v, $res);
    }

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
    public function clearCache(null|string|callable $key = null): bool|int
    {
        $cacheKeys = $this->allKeys($key);
        redis()->del($cacheKeys);

        return count($cacheKeys);
    }

    /**
     * 获取缓存时间
     *
     * @author XJ.
     * Date: 2022/11/18 0018
     * @return int
     */
    public function getTtl(): int
    {
        return ($this->getRedisKeyColumn()->ttl ?? ($this->getRedisKey()?->ttl ?? ($this->getRedisKeyPrefix()?->ttl ?? -1)));
    }

    /**
     * 获取是否开启nx
     *
     * @author XJ.
     * Date: 2022/11/18 0018
     * @return bool
     */
    public function getIsNx(): bool
    {
        return $this->getRedisKeyColumn()->isNx ?? ($this->getRedisKey()?->nx ?? false);
    }

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
    public function when($condition, callable $callback, callable|null $default = null): static
    {
        if ($condition) {
            $res = $callback($this, $condition);

            return $res instanceof static ? $res : $this;
        }
        if ($default) {
            $res = $default($this, $condition);

            return $res instanceof static ? $res : $this;
        }

        return $this;
    }

}