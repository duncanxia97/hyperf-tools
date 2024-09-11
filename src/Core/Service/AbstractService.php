<?php
/**
 * @author XJ.
 * @Date   2023/8/4 0004
 */

namespace Fatbit\HyperfTools\Core\Service;


use Fatbit\HyperfTools\Core\ErrorCode\Interfaces\ErrorCodeInterface;
use Fatbit\HyperfTools\Exceptions\ServiceException;

abstract class AbstractService
{

    /**
     * 抛错
     *
     * @author XJ.
     * @Date   2023/8/29 0029
     *
     * @param ErrorCodeInterface $errorCode
     *
     * @return mixed
     */
    protected function throwError(ErrorCodeInterface $errorCode)
    {
        throw new ServiceException($errorCode);
    }
}