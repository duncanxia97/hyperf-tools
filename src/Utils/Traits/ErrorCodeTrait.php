<?php
/**
 * @author XJ.
 * Date: 2023/7/10 0010
 */

namespace Fatbit\HyperfTools\Utils\Traits;

use Fatbit\Enums\Traits\GetErrorCode;
use Fatbit\HyperfTools\Core\ErrorCode\Interfaces\ErrorCodeInterface;
use Fatbit\HyperfTools\Params\ErrorCodeParam;
use Fatbit\HyperfTools\Utils\MasterContext;

/**
 * @implements ErrorCodeInterface
 */
trait ErrorCodeTrait
{
    use GetErrorCode {
        getCode as _getCode;
    }

    /**
     * 设置错误参数参照hyperf-constants
     *
     * @link   https://hyperf.wiki/3.0/#/zh-cn/constants?id=%e5%8f%af%e5%8f%98%e5%8f%82%e6%95%b0
     * @author XJ.
     * @Date   2023/8/10 0010
     *
     * @param array $args
     *
     * @return $this
     */
    public function setMsgArguments(...$args)
    {
        $this->getErrorCodeParam()->setData('msgArgs', $args);

        return $this;
    }

    /**
     * 获取错误码
     *
     * @author XJ.
     * @Date   2023/8/1 0001
     * @return ErrorCodeParam
     */
    public function getErrorCodeParam(): ErrorCodeParam
    {
        if (!MasterContext::has(serialize($this))) {
            MasterContext::set(
                serialize($this),
                new ErrorCodeParam(
                    [
                        'errorCode' => $this,
                    ]
                )
            );
        }

        return MasterContext::get(serialize($this));
    }

    /**
     * 用完摧毁
     *
     * @author XJ.
     * @Date   2023/8/1 0001
     */
    public function destroyParam()
    {
        MasterContext::destroy(serialize($this));
    }

    /**
     * 设置错误信息
     *
     * @author XJ.
     * @Date   2023/8/1 0001
     *
     * @param string $msg
     *
     * @return $this
     */
    public function setErrorMsg(string $msg): static
    {
        $this->getErrorCodeParam()->setData('errorMsg', $msg);

        return $this;
    }


    /**
     * 设置错误信息
     *
     * @author XJ.
     * @Date   2023/8/1 0001
     *
     * @param $data
     *
     * @return $this
     */
    public function setErrorData($data): static
    {
        $this->getErrorCodeParam()->setData('data', $data);

        return $this;
    }

    /**
     * 设置错误拓展数据
     *
     * @author XJ.
     * @Date   2023/8/1 0001
     *
     * @param array $ext
     *
     * @return $this
     */
    public function setErrorExt(array $ext): static
    {
        $this->getErrorCodeParam()->setData('ext', $ext);

        return $this;
    }

    /**
     * 是否启动错误记录
     *
     * @author XJ.
     * @Date   2023/8/1 0001
     *
     * @param bool $enable
     *
     * @return $this
     */
    public function enableRecordError(bool $enable = false): static
    {
        $this->getErrorCodeParam()->setData('enableRecordError', $enable);

        return $this;
    }

    /**
     * 设置上级错误
     *
     * @author XJ.
     * @Date   2023/8/1 0001
     *
     * @param \Throwable $throwable
     *
     * @return $this
     */
    public function setPrevious(\Throwable $throwable): static
    {
        $this->getErrorCodeParam()->setData('previous', $throwable);

        return $this;
    }

    /**
     * 获取错误码
     *
     * @author XJ.
     * @Date   2023/7/14 0014
     * @return int
     */
    public function getCode(): int
    {
        return $this->value === 0 ? 0 : APP_ID . $this->_getCode();
    }
}