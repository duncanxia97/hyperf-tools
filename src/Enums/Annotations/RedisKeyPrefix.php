<?php
/**
 * @author XJ.
 * Date: 2022/11/11 0011
 */

namespace Fatbit\HyperfTools\Enums\Annotations;

use Attribute;

/**
 * redis key 定义
 * 全局的如果继承了该类的下级默认使用值
 *
 * @author XJ.
 * Date: 2022/11/11 0011
 */
#[Attribute(Attribute::TARGET_CLASS)]
class RedisKeyPrefix
{
    /**
     * @param string $prefix 缓存前缀
     * @param string $desc   缓存注释
     * @param int    $ttl    缓存时间
     */
    public function __construct(
        public readonly string $prefix,
        public readonly string $desc,
        public readonly int    $ttl = -1,
    )
    {
    }
}