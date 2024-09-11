<?php
/**
 * @author XJ.
 * Date: 2022/11/11 0011
 */

namespace Fatbit\HyperfTools\Enums\Annotations;

use Attribute;

/**
 * redis 缓存键定义
 *
 * @author XJ.
 * Date: 2022/11/11 0011
 */
#[Attribute(Attribute::TARGET_CLASS_CONSTANT)]
class RedisKey
{
    /**
     * @param string $servicePrefix 业务前缀
     * @param string $desc          缓存注释
     * @param int    $ttl           缓存时间
     * @param bool   $nx            是否使用nx
     */
    public function __construct(
        public readonly string  $servicePrefix,
        public readonly ?string $desc = null,
        public readonly ?int    $ttl = null,
        public readonly ?bool   $nx = null,
    )
    {
    }
}