<?php
/**
 * @author XJ.
 * @Date   2024/9/11 星期三
 */

namespace Fatbit\HyperfTools\Utils;

use Hyperf\Engine\Coroutine as Co;
use Swoole\Coroutine as SwooleCo;

class MasterContext
{
    protected static array $nonCoContext = [];

    /**
     * 是否在子协程中
     * Created by XJ.
     * Date: 2021/12/16
     *
     * @return bool
     */
    public static function inSubCoroutine()
    {
        return SwooleCo::getPcid() > 0;
    }


    /**
     * @author XJ.
     * @Date   2024/9/11 星期三
     *
     * @param $cid
     *
     * @return int|mixed
     */
    public static function getMasterCid($cid = null)
    {
        $pid = SwooleCo::getPcid($cid);
        if ($pid < 0) {
            return $cid ?? SwooleCo::getCid();
        }

        return static::getMasterCid($pid);
    }

    /**
     * 设置
     * Created by XJ.
     * Date: 2021/12/16
     *
     * @param string    $id
     * @param           $value
     *
     * @return mixed
     */
    public static function set(string $id, $value)
    {
        $cid = static::getMasterCid();
        if ($cid > 0) {
            Co::getContextFor($cid)[$id] = $value;
        } else {
            static::$nonCoContext[$id] = $value;
        }

        return $value;
    }

    /**
     * 获取
     * Created by XJ.
     * Date: 2021/12/16
     *
     * @param string    $id
     * @param           $default
     *
     * @return false|mixed|null
     */
    public static function get(string $id, $default = null)
    {
        $cid = static::getMasterCid();
        if ($cid > 0) {
            return Co::getContextFor($cid)[$id] ?? $default;
        }

        return static::$nonCoContext[$id] ?? $default;
    }

    /**
     * 是否存在
     * Created by XJ.
     * Date: 2021/12/16
     *
     * @param string $id
     *
     * @return bool
     */
    public static function has(string $id)
    {
        $cid = static::getMasterCid();
        if ($cid > 0) {
            return isset(Co::getContextFor($cid)[$id]);
        }

        return isset(static::$nonCoContext[$id]);
    }

    /**
     * Release the context when you are not in coroutine environment.
     */
    public static function destroy(string $id)
    {
        $cid = static::getMasterCid();
        if ($cid > 0) {
            unset(Co::getContextFor($cid)[$id]);
        }
        unset(static::$nonCoContext[$id]);
    }

    /**
     * Retrieve the value and override it by closure.
     */
    public static function override(string $id, \Closure $closure)
    {
        $value = null;
        if (self::has($id)) {
            $value = self::get($id);
        }
        $value = $closure($value);
        self::set($id, $value);

        return $value;
    }

    /**
     * Retrieve the value and store it if not exists.
     *
     * @param mixed $value
     */
    public static function getOrSet(string $id, $value)
    {
        if (!self::has($id)) {
            return self::set($id, value($value));
        }

        return self::get($id);
    }

    public static function getContainer()
    {
        return Co::getContextFor(static::getMasterCid());
    }

}