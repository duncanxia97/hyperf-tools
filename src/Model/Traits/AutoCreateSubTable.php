<?php
/**
 * Created by XJ.
 * Date: 2022/4/22
 */

namespace Fatbit\HyperfTools\Model\Traits;

use Fatbit\HyperfTools\Core\Model\AbstractModel;
use Hyperf\Database\Model\Events\Creating;
use Hyperf\Database\Model\Events\Saving;
use Hyperf\Database\Schema\Schema;
use Hyperf\DbConnection\Db;
use Hyperf\Redis\Redis;

/**
 * 自动创建分表
 *
 * @mixin AbstractModel
 * @uses ModelSuffix
 */
trait AutoCreateSubTable
{

    public function saving(Saving $event)
    {
        $this->createSubTable();
    }

    public function creating(Creating $event)
    {
        $this->createSubTable();
    }


    /**
     * 创建分表
     * Created by XJ.
     * Date: 2022/4/22
     */
    public function createSubTable()
    {
        $format      = $this->getSubTableSuffixFormat();
        $tableSuffix = date($format);
        $tableName   = (new static())->getTable();
        $redisKey    = env('APP_ID') . ':createSubTable:' . $tableName . ':' . $tableSuffix;
        $redis       = make(Redis::class);
        if (!$redis->exists($redisKey)) {
            // 缓存判断不存在分表
            $newTableName = $tableName . '_' . $tableSuffix;
            $tllFormat    = $this->subTableMod->getTTLFormat();
            if (!Schema::hasTable($newTableName)) {
                // 不存在分表
                $oldTableName  = $tableName;
                $lastTableName = $oldTableName . '_' . date($format, strtotime('-1 ' . $tllFormat));
                if (Schema::hasTable($lastTableName)) {
                    // 是否存在上一个分表 存在获取上一个分表创建新的分表
                    $oldTableName = $lastTableName;
                }
                $oldTableName = config('databases.default.prefix') . $oldTableName;
                $newTableName = config('databases.default.prefix') . $newTableName;
                Db::select("CREATE TABLE `{$newTableName}` LIKE `{$oldTableName}`;");
            }
            $redis->set($redisKey, '1', get_seconds($tllFormat));
        }
    }

}