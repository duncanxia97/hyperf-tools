<?php
/**
 * @author XJ.
 * @Date   2023/7/31 0031
 */

namespace Fatbit\HyperfTools\Model\Traits;

use Fatbit\HyperfTools\Core\Model\AbstractModel;
use Fatbit\HyperfTools\Enums\SubTableEnum;
use Fatbit\HyperfTools\ErrorCodes\SysErrorCode;
use Fatbit\HyperfTools\Exceptions\ModelException;
use Hyperf\Database\Model\Builder;

/**
 * @mixin AbstractModel
 */
trait ModelSuffix
{
    protected $suffix = null;

    /**
     * @var SubTableEnum
     */
    protected $subTableMod = SubTableEnum::YEAR;

    // 设置表后缀
    public function setSuffix($suffix)
    {
        if (($uses = class_uses($this)) && in_array(AutoCreateSubTable::class, $uses)) {
            // 判断是否用自动创建分表引用
            /** @var AutoCreateSubTable $this */
            $this->createSubTable();
        }
        $this->suffix = $suffix;
        if (!empty($suffix)) {
            $this->table = $this->getTable() . '_' . $suffix;
        }
    }

    /**
     * 提供一个静态方法设置表后缀
     * Created by XJ.
     * Date: 2022/5/4
     *
     * @param $suffix
     *
     * @return Builder
     */
    public static function suffix($suffix)
    {
        $instance = new static;
        $instance->setSuffix($suffix);

        return $instance->newQuery();
    }

    /**
     * 获取分表后缀格式
     * Created by XJ.
     * Date: 2022/4/22
     *
     * @return string
     */
    protected function getSubTableSuffixFormat(): string
    {
        return $this->subTableMod->getSuffixFormat();
    }

    /**
     * 根据雪花ID生成模型实例
     *
     * @param int $snackId
     *
     * @return AbstractModel|ModelSuffix
     */
    public static function getModelBySnack(int $snackId)
    {
        $model = new static;
        if (strlen((string)$snackId) < 10) {
            return $model;
        }
        try {
            $time = snowflake()->degenerate($snackId)->getTimestamp() / 1000;
            $year = date($model->getSubTableSuffixFormat(), (int)$time);
        } catch (\Throwable $e) {
            throw new ModelException(
                SysErrorCode::SYSTEM_ERROR
                    ->setErrorMsg('ID格式错误')
                    ->setPrevious($e)
            );
        }
        if (($uses = class_uses($model)) && in_array(AutoCreateSubTable::class, $uses)) {
            // 判断是否用自动创建分表引用
            $model->createSubTable();
        }

        return $model->setTable($model->getTable() . '_' . $year);
    }


    /**
     * 根据雪花id设置表名
     *
     * @param int $snackId
     *
     * @throws ModelException
     */
    public function setTableNameBySnack(int $snackId)
    {
        if (strlen((string)$snackId) < 10) {
            return;
        }
        try {
            $time = snowflake()->degenerate($snackId)->getTimestamp() / 1000;
            $year = date($this->getSubTableSuffixFormat(), (int)floor($time));
        } catch (\Throwable $e) {
            throw new ModelException(
                SysErrorCode::SYSTEM_ERROR
                    ->setErrorMsg('ID格式错误')
                    ->setPrevious($e)
            );
        }
        $this->setSuffix($year);
    }
}