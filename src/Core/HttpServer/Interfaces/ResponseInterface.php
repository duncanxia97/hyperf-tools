<?php
/**
 * @author XJ.
 * @Date   2023/7/19 0019
 */

namespace Fatbit\HyperfTools\Core\HttpServer\Interfaces;

use Fatbit\HyperfTools\Core\ErrorCode\Interfaces\ErrorCodeInterface;
use Hyperf\Contract\Arrayable;

interface ResponseInterface extends \Hyperf\HttpServer\Contract\ResponseInterface
{
    public function error(ErrorCodeInterface $errorCode): Arrayable;

    public function success($data = [], string $msg = 'SUCCESS'): Arrayable;

}