<?php
/**
 * @author XJ.
 * @Date   2023/8/2 0002
 */

namespace Fatbit\HyperfTools\Commands\Generator;

use Hyperf\Stringable\Str;

class ControllerCommand extends \Hyperf\Devtool\Generator\ControllerCommand
{
    public function qualifyClass(string $name): string
    {
        $name = implode(
            '/',
            array_map(
                fn($v) => Str::studly(Str::snake($v)),
                explode('/', Str::replace('\\', '/', $name))
            )
        );
        $name = Str::studly(Str::snake($name));
        $name = parent::qualifyClass($name);

        return $name . 'Controller';
    }

    protected function getStub(): string
    {
        return $this->getConfig()['stub'] ?? __DIR__ . '/stubs/controller.stub';
    }

}