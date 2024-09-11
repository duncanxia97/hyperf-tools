<?php
/**
 * @author XJ.
 * Date: 2023/7/7 0007
 */

namespace Fatbit\HyperfTools\ErrorCodes;

use Fatbit\Enums\Annotations\ErrorCode;
use Fatbit\Enums\Annotations\ErrorCodePrefix;
use Fatbit\HyperfTools\Core\ErrorCode\Interfaces\ErrorCodeInterface;
use Fatbit\HyperfTools\Utils\Traits\ErrorCodeTrait;

#[ErrorCodePrefix(999, '系统错误')]
enum SysErrorCode: int implements ErrorCodeInterface
{
    use ErrorCodeTrait;

    #[ErrorCode('系统错误')]
    case SYSTEM_ERROR = 500;

    #[ErrorCode('不存在')]
    case NOT_FOUND = 404;

    #[ErrorCode('验证未通过')]
    case VERIFY_FAILED = 502;

    #[ErrorCode('类型错误')]
    case TYPE_ERROR = 891;

    #[ErrorCode('身份失效，请重新登入')]
    case AUTH_TOKEN_FAILED = 401;

    #[ErrorCode('无操作权限')]
    case PERMISSION_FAILED = 402;

    #[ErrorCode('请先登录')]
    case NOT_LOGIN = 410;

    #[ErrorCode('未授权')]
    case UNAUTHORIZED = 411;

    #[ErrorCode('请求不允许')]
    case FORBIDDEN = 403;

    #[ErrorCode('success')]
    case SUCCESS = 0;
}