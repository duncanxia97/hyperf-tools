<?php
/**
 * @author XJ.
 * Date: 2023/7/7 0007
 */

namespace Fatbit\HyperfTools\Exceptions;

use Fatbit\HyperfTools\Core\ErrorCode\Interfaces\ErrorCodeInterface;
use Hyperf\Server\Exception\ServerException;
use Fatbit\HyperfTools\Enums\ErrorLevel;

class CodeException extends ServerException
{
    readonly protected int $appId;

    protected array        $traces = [];

    public function __construct(
        readonly protected ErrorCodeInterface $errorCode,
        readonly protected ErrorLevel         $level,
        ?int                                  $appId = null
    )
    {
        $this->appId = $appId ?? APP_ID;
        parent::__construct(
            message : $errorCode->getErrorCodeParam()->errorMsg(),
            code    : $errorCode->getCode(),
            previous: $errorCode->getErrorCodeParam()->previous(),
        );
    }

    public function getErrorCode(): ErrorCodeInterface
    {
        return $this->errorCode;
    }

    public function getErrorLevel(): ErrorLevel
    {
        return $this->level;
    }

    public function setTraces(array $traces)
    {
        $this->traces = $traces;
    }

    public function getTraces(): array
    {
        return $this->traces ?: $this->getTrace();
    }

    /**
     * 哪个服务节点抛出异常
     *
     * @return int
     */
    public function getThrowAppId()
    {
        return $this->appId;
    }
}