<?php
/**
 * @author XJ.
 * @Date   2023/8/2 0002
 */

namespace Fatbit\HyperfTools\Commands\Generator;

use Hyperf\Command\Annotation\Command;

#[Command]
class CrontabCmdCommand extends JcGeneratorCommand
{
    protected string $classSuffix = 'CrontabCmd';

    public function configure()
    {
        $this->setDescription('Create a new crontab cmd class');

        parent::configure();
    }

    protected function getStub(): string
    {
        return __DIR__ . '/stubs/crontab_command.stub';
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Command\\Crontab';
    }
}
