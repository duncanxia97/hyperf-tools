<?php
/**
 * @author XJ.
 * Date: 2022/12/7 0007
 */

namespace Fatbit\HyperfTools\RedisKeys;

use Fatbit\HyperfTools\Core\RedisKey\Interfaces\RedisKeyInterface;
use Fatbit\HyperfTools\Enums\Annotations\RedisKey;
use Fatbit\HyperfTools\Enums\Annotations\RedisKeyPrefix;
use Fatbit\HyperfTools\Utils\Traits\GetRedisKey;

#[RedisKeyPrefix('numberGenerator', '编码生成器')]
enum NumberGeneratorRedisKey: string implements RedisKeyInterface
{
    use GetRedisKey;

    #[RedisKey('', '累加器', 2592000, true)]
    case ADDER = 'adder';
}