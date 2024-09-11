<?php
/**
 * @author XJ.
 * @Date   2023/7/19 0019
 */

namespace Fatbit\HyperfTools\HttpServer;

use Fatbit\HyperfTools\Core\ErrorCode\Interfaces\ErrorCodeInterface;
use Fatbit\HyperfTools\Core\HttpServer\Interfaces\ResponseInterface;
use Fatbit\HyperfTools\Enums\RequestLife;
use Fatbit\HyperfTools\Exceptions\CodeException;
use Fatbit\HyperfTools\Params\ResponseParam;
use Fatbit\HyperfTools\Tracer\TracerEntity;
use Hyperf\Contract\Arrayable;
use Hyperf\HttpServer\Response;

class HttpResponse extends Response implements ResponseInterface
{

    /**
     * 返回错误数据
     *
     * @author XJ.
     * @Date   2023/7/24 0024
     *
     * @param ErrorCodeInterface $errorCode
     *
     * @return array
     */
    public function error(ErrorCodeInterface $errorCode): Arrayable
    {
        $ext = $errorCode->getErrorCodeParam()->ext();
        if (!isProdEnv() && $throwable = $errorCode->getErrorCodeParam()->previous()) {
            if (!isset($ext['exceptions'])) {
                $ext['exceptions'] = [];
            }
            $isMyBaseException   = $throwable instanceof CodeException;
            $ext['exceptions'][] = [
                'file'           => $throwable->getFile(),
                'line'           => $throwable->getLine(),
                'message'        => $throwable->getMessage(),
                'exceptionAppId' => $isMyBaseException ? $throwable->getThrowAppId() : APP_ID,
                'traces'         => $isMyBaseException ? $throwable->getTraces() : $throwable->getTrace(),
            ];
        }

        return $this->returnResponseData(
            data: $errorCode->getErrorCodeParam()->data(),
            msg : $errorCode->getErrorCodeParam()->errorMsg(),
            code: $errorCode->getCode(),
            ext : $ext
        );
    }

    /**
     * 返回正确数据
     *
     * @author XJ.
     * @Date   2023/7/24 0024
     *
     * @param        $data
     * @param string $msg
     *
     * @return array
     */
    public function success($data = [], string $msg = 'SUCCESS'): Arrayable
    {
        return $this->returnResponseData(
            data: $data,
            msg : $msg,
        );
    }

    /**
     * 返回数据
     *
     * @author XJ.
     * @Date   2023/7/20 0020
     *
     * @param             $data
     * @param string|null $msg
     * @param int         $code
     * @param array       $ext
     *
     * @return array
     */
    protected function returnResponseData($data = [], string $msg = null, int $code = 0, array $ext = []): Arrayable
    {
        $retData            = compact('data', 'code', 'msg');
        $retData['appId']   = APP_ID;
        $retData['traceId'] = RequestLife::getTraceId();
        if (!isProdEnv()){
            $retData['debug']   = TracerEntity::getInstance()->getDebugInfo();
        }
        $retData['extra']   = $ext;


        return new ResponseParam(
            $retData
        );
    }
}