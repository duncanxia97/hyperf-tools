<?php
/**
 * @author XJ.
 * Date: 2022/11/11 0011
 */

namespace Fatbit\HyperfTools\Utils\RedisKey;

/**
 * redis 建列
 *
 * @author XJ.
 * Date: 2022/11/11 0011
 */
final class RedisKeyColumn
{
    public function __construct(
        public string $key = '',
        public ?int   $ttl = null,
        public ?bool  $isNx = null,
        public bool   $force = false,
    )
    {
    }

}