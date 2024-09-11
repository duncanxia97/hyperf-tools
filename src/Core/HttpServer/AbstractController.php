<?php
/**
 * @author XJ.
 * @Date   2023/7/24 0024
 */

namespace Fatbit\HyperfTools\Core\HttpServer;

use Fatbit\HyperfTools\Core\HttpServer\Interfaces\ResponseInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

abstract class AbstractController
{
    #[Inject]
    protected RequestInterface  $request;

    #[Inject]
    protected ResponseInterface $response;

    /**
     * 获取管理员id
     *
     * @author XJ.
     * @Date   2024/2/5 0005
     * @return int
     */
    public function _getAdminId(): int
    {
        return $this->request->adminId ?? 0;
    }
}