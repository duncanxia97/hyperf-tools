<?php
/**
 * @author XJ.
 * @Date   2023/7/24 0024
 */

namespace Fatbit\HyperfTools\Core\Model;

use DateTimeInterface;
use Hyperf\Database\Model\Concerns\CamelCase;
use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;
use Fatbit\HyperfTools\Model\Traits\ModelSuffix;

class AbstractModel extends Model implements CacheableInterface
{
    use Cacheable, CamelCase, ModelSuffix;

    public static bool    $snakeAttributes = false;


    /**
     * The name of the "created at" column.
     *
     * @var string|null
     */
    const CREATED_AT = 'created_time';

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = 'updated_time';

    const DELETED_AT = 'deleted_time';

    protected array $dates = [
        'deleted_time'
    ];


    protected array $casts = [
        'created_time' => 'date:Y-m-d H:i:s',
        'updated_time' => 'date:Y-m-d H:i:s',
        'deleted_time' => 'date:Y-m-d H:i:s',
    ];
}