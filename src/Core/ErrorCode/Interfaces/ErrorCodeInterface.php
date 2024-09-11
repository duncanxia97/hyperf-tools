<?php
/**
 * @author XJ.
 * Date: 2023/7/7 0007
 */

namespace Fatbit\HyperfTools\Core\ErrorCode\Interfaces;

use Fatbit\HyperfTools\Params\ErrorCodeParam;

/**
 * @extends \IntBackedEnum
 */
interface ErrorCodeInterface extends \Fatbit\Enums\Interfaces\ErrorCodeInterface
{

    /**
     * 获取错误参数
     *
     * @author XJ.
     * @Date   2023/8/1 0001
     * @return ErrorCodeParam
     */
    public function getErrorCodeParam(): ErrorCodeParam;

    /**
     * 摧毁参数
     *
     * @author XJ.
     * @Date   2023/8/1 0001
     * @return mixed
     */
    public function destroyParam();

    /**
     * 设置错误信息
     *
     * @author XJ.
     * Date: 2023/7/7 0007
     *
     * @param string $msg
     *
     * @return $this
     */
    public function setErrorMsg(string $msg): static;

    /**
     * 设置错误数据
     *
     * @author XJ.
     * Date: 2023/7/7 0007
     *
     * @param $data
     *
     * @return $this
     */
    public function setErrorData($data): static;


    /**
     * 设置拓展数据
     *
     * @author XJ.
     * Date: 2023/7/7 0007
     *
     * @param array $ext
     *
     * @return $this
     */
    public function setErrorExt(array $ext): static;

    /**
     * 是否启用记录错误
     *
     * @author XJ.
     * Date: 2023/7/10 0010
     * @return $this
     */
    public function enableRecordError(): static;


    /**
     * 上一个错误
     *
     * @author XJ.
     * Date: 2023/7/10 0010
     *
     * @param \Throwable $throwable
     *
     * @return $this
     */
    public function setPrevious(\Throwable $throwable): static;

}