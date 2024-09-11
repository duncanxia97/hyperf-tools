<?php
/**
 * @author XJ.
 * @Date   2023/7/14 0014
 */

namespace Fatbit\HyperfTools\Exceptions\Handlers;

use Fatbit\HyperfTools\ErrorCodes\SysErrorCode;
use Fatbit\HyperfTools\Exceptions\CodeException;
use Fatbit\HyperfTools\Exceptions\ServiceException;
use Hyperf\Coroutine\Exception\ParallelExecutionException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\MethodNotAllowedHttpException;
use Hyperf\HttpMessage\Exception\NotFoundHttpException;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Scjc\manageClient\Exceptions\AuthException;
use Throwable;

class HttpExceptionHandler extends ExceptionHandler
{

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        if ($throwable instanceof ValidationException) {
            // 参数校验错误异常抛出
            $throwable = new ServiceException(
                SysErrorCode::VERIFY_FAILED->setErrorData(
                    [
                        'allErrors' => $throwable->validator->errors()->all(),
                    ]
                )
                    ->setErrorMsg($throwable->validator->errors()->first())
            );
        }
        if ($throwable instanceof MethodNotAllowedHttpException) {
            // 请求类型错误异常抛出
            $throwable = new ServiceException(SysErrorCode::NOT_FOUND);
        }
        if ($throwable instanceof NotFoundHttpException) {
            // 路由不存在异常抛出
            $throwable = new ServiceException(SysErrorCode::NOT_FOUND);
        }
        if (!($throwable instanceof CodeException)) {
            $errorCode = SysErrorCode::SYSTEM_ERROR;
            /** @var ParallelExecutionException $throwable */
            if (!isProdEnv() && $throwable instanceof ParallelExecutionException) {
                $exceptions = [];
                /** @var CodeException $throw */
                foreach ($throwable->getThrowables() as $throw) {
                    $isMyBaseException = $throw instanceof CodeException;
                    $exceptions[]      = [
                        'file'           => $throw->getFile(),
                        'line'           => $throw->getLine(),
                        'message'        => $throw->getMessage(),
                        'exceptionAppId' => $isMyBaseException ? $throw->getThrowAppId() : APP_ID,
                        'traces'         => $isMyBaseException ? $throw->getTraces() : $throw->getTrace(),
                    ];
                    if ($isMyBaseException) {
                        $throw->getErrorCode()->destroyParam();
                    }
                }
                $errorCode->setErrorExt(['exceptions' => $exceptions]);
            }
            $errorCode->setPrevious($throwable);
            $throwable = new ServiceException($errorCode);
        }
        $jcResponse   = make(\Fatbit\HyperfTools\Core\HttpServer\Interfaces\ResponseInterface::class);
        $responseData = $jcResponse->error($throwable->getErrorCode());
        $this->stopPropagation();

        return $jcResponse
            ->json($responseData);
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}