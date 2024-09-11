<?php
/**
 * @author XJ.
 * Date: 2023/5/11 0011
 */

namespace Fatbit\HyperfTools\Commands;

use Hyperf\Command\Command;

abstract class AbstractCrontabCommand extends Command
{

    abstract public function exec();

    /**
     * 执行
     * @author XJ.
     * Date: 2023/5/11 0011
     */
    public function handle()
    {
        $this->line('开始执行'.$this->getDescription().'...');
        $this->exec();
        $this->line($this->getDescription().'执行完成!!!');
    }
}