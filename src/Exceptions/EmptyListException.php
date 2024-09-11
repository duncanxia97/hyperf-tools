<?php
/**
 * @author XJ.
 * @Date   2023/8/3 0003
 */

namespace Fatbit\HyperfTools\Exceptions;

use Fatbit\HyperfTools\ErrorCodes\SysErrorCode;

class EmptyListException extends ServiceException
{
    public function __construct(array $data = ['list' => [], 'total' => 0])
    {
        parent::__construct(SysErrorCode::SUCCESS->setErrorData($data));
    }

}