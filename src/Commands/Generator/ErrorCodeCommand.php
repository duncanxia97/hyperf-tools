<?php
/**
 * @author XJ.
 * @Date   2023/8/2 0002
 */

namespace Fatbit\HyperfTools\Commands\Generator;

use Hyperf\Command\Annotation\Command;

#[Command]
class ErrorCodeCommand extends JcGeneratorCommand
{
    protected string $classSuffix = 'ErrorCode';

    public function configure()
    {
        $this->setDescription('Create a new ErrorCode enum');

        parent::configure();
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/error_code.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Enums\\ErrorCodes';
    }
}
