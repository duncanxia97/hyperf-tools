<?php
/**
 * @author XJ.
 * Date: 2023/7/7 0007
 */

namespace Fatbit\HyperfTools\Exceptions;

use Fatbit\HyperfTools\Core\ErrorCode\Interfaces\ErrorCodeInterface;
use Fatbit\HyperfTools\Enums\ErrorLevel;

class RpcException extends CodeException
{
    public function __construct(ErrorCodeInterface $errorCode)
    {
        parent::__construct($errorCode, ErrorLevel::RPC);
    }
}