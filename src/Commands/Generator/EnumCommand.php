<?php
/**
 * @author XJ.
 * @Date   2023/8/2 0002
 */

namespace Fatbit\HyperfTools\Commands\Generator;


use Hyperf\Command\Annotation\Command;

#[Command]
class EnumCommand extends JcGeneratorCommand
{
    protected string $classSuffix = 'Enum';

    public function configure()
    {
        $this->setDescription('Create a new enum');

        parent::configure();
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/enum.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Enums';
    }
}
