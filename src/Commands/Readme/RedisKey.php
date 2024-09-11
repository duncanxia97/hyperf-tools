<?php
/**
 * @author XJ.
 * @Date   2023/8/15 0015
 */

namespace Fatbit\HyperfTools\Commands\Readme;

use Fatbit\HyperfTools\Core\RedisKey\Interfaces\RedisKeyInterface;
use Fatbit\HyperfTools\Enums\Annotations\RedisKeyPrefix;
use Hyperf\Command\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;

#[\Hyperf\Command\Annotation\Command]
class RedisKey extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('gen-readme:redis-key');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Create redis key readme file');
    }

    public function handle()
    {
        $files     = Finder::create()->in([BASE_PATH . '/app/Enums/RedisKeys/'])->files();
        $redisKeys = [];
        $path      = BASE_PATH . '/Rediskey-readme.md';
        $count     = 1;
        foreach ($files as $file) {
            /** @var \UnitEnum $enum */
            $enum = Str::betweenFirst($file->getContents(), 'namespace ', ';') . '\\' . Str::before($file->getFilename(), '.php');
            if (enum_exists($enum)) {
                $enums    = $enum::cases();
                $redisKey = [
                    'cases'  => [],
                    'class'  => $enum,
                    'prefix' => '',
                    'desc'   => '',
                    'ttl'    => 0,
                ];
                /** @var \StringBackedEnum|RedisKeyInterface $enum */
                foreach ($enums as $k => $enum) {
                    if ($k == 0) {
                        $reflection = new \ReflectionEnum($enum);
                        $method     = $reflection->getMethod('getRedisKeyPrefix');
                        /** @var RedisKeyPrefix $redisKeyPrefix */
                        $redisKeyPrefix     = $method->invoke($enum);
                        $redisKey['prefix'] = $redisKeyPrefix?->prefix ?? '';
                        $redisKey['desc']   = $redisKeyPrefix?->desc ?? '';
                        $redisKey['ttl']    = $redisKeyPrefix?->ttl ?? '';

                    }
                    $redisKey['cases'][] = [
                        'name'   => get_class($enum) . '::' . $enum->name,
                        'desc'   => $enum->getDesc(),
                        'ttl'    => $enum->getTtl(),
                        'isNx'   => $enum->getIsNx(),
                        'prefix' => $enum->getPrefix()
                    ];
                }
                $redisKeys[$redisKey['prefix']] = $redisKey;
            }
        }
        ksort($redisKeys);
        file_put_contents($path, '## 项目 redis key目录');
        foreach ($redisKeys as $redisKey) {
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            $str = sprintf(
                '### %s. %s(`%s`, 前缀:%s, 缓存时间:%s)',
                $count++,
                $redisKey['desc'],
                $redisKey['class'],
                $redisKey['prefix'],
                $redisKey['ttl'],
            );
            file_put_contents($path, $str, FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, "---", FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, '| 枚举 | redis 键名前缀 | 缓存注释 | 默认缓存时间 | 默认是否使用nx |', FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, '|:-------|:-------:|-------|-------|-------|', FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            foreach ($redisKey['cases'] as $case) {
                file_put_contents(
                    $path,
                    sprintf(
                        '| `%s` | `%s` | %s | %s | %s |',
                        $case['name'],
                        $case['prefix'],
                        $case['desc'],
                        $case['ttl'],
                        $case['isNx'] ? '是' : '否'
                    ),
                    FILE_APPEND
                );
                file_put_contents($path, "\n", FILE_APPEND);
            }
        }
        $this->output->writeln(sprintf('<info>%s</info>', '创建成功!!!'));


        return Command::SUCCESS;
    }
}