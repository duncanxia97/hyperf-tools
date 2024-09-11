<?php
/**
 * @author XJ.
 * @Date   2023/8/2 0002
 */

namespace Fatbit\HyperfTools\Commands\Generator;

use Hyperf\Command\Annotation\Command;

#[Command]
class ServiceCommand extends JcGeneratorCommand
{
    protected string $classSuffix = 'Service';

    public function configure()
    {
        $this->setDescription('Create a new service class');

        parent::configure();
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/service.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Service';
    }
}
