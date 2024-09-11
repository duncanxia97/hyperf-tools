<?php
/**
 * @author XJ.
 * @Date   2023/8/2 0002
 */

namespace Fatbit\HyperfTools\Commands\Generator;

use Hyperf\Devtool\Generator\GeneratorCommand;
use Hyperf\Stringable\Str;

abstract class JcGeneratorCommand extends GeneratorCommand
{
    protected string $classSuffix;

    public function __construct()
    {
        parent::__construct('gen:'.Str::snake($this->classSuffix, '-'));
    }

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

        return $name . $this->classSuffix;
    }

}