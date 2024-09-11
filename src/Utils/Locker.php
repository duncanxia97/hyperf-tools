<?php
/**
 * @author XJ.
 * @Date   2024/9/11 星期三
 */

namespace Fatbit\HyperfTools\Utils;

class Locker
{

    /**
     * 锁名前缀
     *
     * @var string
     */
    protected string $keyPrefix = 'locker';

    /**
     * 是否需要添加app前缀
     *
     * @var bool
     */
    protected bool $needPrefixApp = true;

    /**
     * 锁名
     *
     * @var string
     */
    protected ?string $key = null;

    public function __construct(string|callable|null $key = null)
    {
        if (!is_null($key)) {
            $this->setKey($key);
        }
    }

    public function setKey(string|callable $key)
    {
        return $this->key = value($key);
    }

    /**
     * 获取锁名
     *
     * @author XJ.
     * Date: 2023/2/2 0002
     *
     * @param string|callable $key 锁名
     *
     * @return string
     */
    public function getKey(string|callable|null $key = null): string
    {
        $key = value($key ?? $this->key);
        if (!$key) {
            throw new \Exception(3009, 'locker key must be a string');
        }

        return (!$this->needPrefixApp ?: (APP_ID . ':')) . $this->keyPrefix . ':' . $key;
    }

    /**
     * 锁是否存在
     *
     * @author XJ.
     * Date: 2023/2/2 0002
     *
     * @param string $key 锁名
     *
     * @return bool
     * @throws \RedisException
     */
    public function exists(string|callable|null $key = null): bool
    {
        return (bool)redis()->exists($this->getKey($key));
    }

    /**
     * 上锁
     *
     * @author XJ.
     * Date: 2023/2/2 0002
     *
     * @param string|callable $key      锁名
     * @param int             $expired  锁的有效期(秒)
     * @param bool            $ifExists 是否判断锁存在
     *
     * @return bool
     * @throws \RedisException
     */
    public function lock(string|callable|null $key, int $expired, bool $ifExists = false): bool
    {
        $this->setKey($key);

        return !(
            ($ifExists && $this->exists($key))
            || !redis()->set($this->getKey($key), 1, ['nx', 'ex' => $expired])
        );
    }

    /**
     * 运行锁
     *
     * @author XJ.
     * Date: 2023/2/2 0002
     *
     * @param string|callable $key        锁名
     * @param callable        $run        运行函数
     * @param int             $expired    锁有效期
     * @param bool            $ifExists   是否判断锁存在
     * @param int             $retry      重试次数
     * @param int             $retrySleep 重试睡眠时间(millisecond)
     *
     * @return bool
     * @throws \RedisException
     * @throws \Throwable
     */
    public function run(string|callable|null $key, callable $run, int $expired = 60, bool $ifExists = false, int $retry = 0, int $retrySleep = 0): bool
    {
        return retry(
            $retry,
            function () use ($key, $run, $expired, $ifExists) {
                if (!$this->lock($key, $expired, $ifExists)) {

                    return false;
                }

                try {
                    $run();
                } finally {
                    $this->freed($key);
                }

                return true;
            },
            $retrySleep
        );
    }

    /**
     * 释放锁
     *
     * @author XJ.
     * Date: 2023/2/2 0002
     *
     * @param string|callable $key 锁名
     *
     * @return bool
     * @throws \RedisException
     */
    public function freed(string|callable|null $key = null): bool
    {
        $script = <<<Lua
            if redis.call("GET", KEYS[1]) == ARGV[1] then
                return redis.call("DEL", KEYS[1])
            else
                return 0
            end
        Lua;

        return redis()->eval($script, [$this->getKey($key), 1], 1) > 0;
    }
}