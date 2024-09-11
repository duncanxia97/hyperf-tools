<?php
/**
 * @author XJ.
 * @Date   2023/8/1 0001
 */

namespace Fatbit\HyperfTools\Params;

use Fatbit\HyperfTools\Core\ErrorCode\Interfaces\ErrorCodeInterface;
use Fatbit\HyperfTools\Core\Param\AbstractParam;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\TranslatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @author XJ.
 * @Date   2023/8/1 0001
 * @method ErrorCodeInterface   errorCode()
 * @method array                ext()
 * @method null|array           data()
 * @method bool                 enableRecordError()
 * @method null|\Throwable      previous()
 */
class ErrorCodeParam extends AbstractParam
{
    protected ErrorCodeInterface $errorCode;

    protected ?string            $errorMsg          = null;

    protected array              $ext               = [];

    protected ?array             $data              = null;

    protected array              $msgArgs           = [];

    protected bool               $enableRecordError = true;

    protected ?\Throwable        $previous          = null;

    /**
     * @param array|null $data
     */
    public function setData($key, mixed $data): void
    {
        $this->{$key} = $data;
    }

    public function errorMsg(): string
    {
        $message = $this->errorMsg ?: $this->errorCode->getErrorMsg();
        $result  = $this->translate($message, $this->msgArgs);
        if ($result && $result !== $message) {
            return $result;
        }

        return sprintf($message, ...$this->msgArgs);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @see \Hyperf\Constants\GetterTrait
     */
    protected function translate(string $key, array $arguments): array|string|null
    {
        if (!ApplicationContext::hasContainer() || !ApplicationContext::getContainer()->has(TranslatorInterface::class)) {
            return null;
        }

        $replace = array_shift($arguments) ?? [];
        if (!is_array($replace)) {
            return null;
        }

        $translator = ApplicationContext::getContainer()->get(TranslatorInterface::class);

        return $translator->trans($key, $replace);
    }

}