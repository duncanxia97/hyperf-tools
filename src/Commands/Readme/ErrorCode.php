<?php
/**
 * @author XJ.
 * @Date   2023/8/15 0015
 */

namespace Fatbit\HyperfTools\Commands\Readme;

use Fatbit\Enums\Annotations\ErrorCodePrefix;
use Fatbit\HyperfTools\Core\ErrorCode\Interfaces\ErrorCodeInterface;
use Hyperf\Command\Command;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;

#[\Hyperf\Command\Annotation\Command]
class ErrorCode extends HyperfCommand
{

    public function __construct(protected ContainerInterface $container)
    {
        parent::__construct('gen-readme:error-code');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Create error code readme file');
    }

    public function handle()
    {
        $files      = Finder::create()->in([BASE_PATH . '/app/Enums/ErrorCodes/'])->files();
        $errorCodes = [];
        $path       = BASE_PATH . '/ErrorCode-readme.md';
        $count      = 1;
        foreach ($files as $file) {
            /** @var \UnitEnum|ErrorCodeInterface $enum */
            $enum = Str::betweenFirst($file->getContents(), 'namespace ', ';') . '\\' . Str::before($file->getFilename(), '.php');
            if (enum_exists($enum)) {
                $enums     = $enum::cases();
                $errorCode = [
                    'cases'  => [],
                    'class'  => $enum,
                    'prefix' => '',
                    'desc'   => '',
                ];
                /** @var \IntBackedEnum|ErrorCodeInterface $enum */
                foreach ($enums as $k => $enum) {
                    if ($k == 0) {
                        $reflection = new \ReflectionEnum($enum);
                        $method     = $reflection->getMethod('getErrorCodePrefix');
                        /** @var ErrorCodePrefix $redisKeyPrefix */
                        $errorCodePrefix     = $method->invoke($enum);
                        $errorCode['prefix'] = $errorCodePrefix?->prefix ?? '';
                        $errorCode['desc']   = $errorCodePrefix?->desc ?? '';

                    }
                    $errorCode['cases'][] = [
                        'name'     => get_class($enum) . '::' . $enum->name,
                        'code'     => $enum->getCode(),
                        'errorMsg' => $enum->getErrorMsg(),
                    ];
                }
                $errorCodes[$errorCode['prefix']] = $errorCode;
            }
        }
        ksort($errorCodes);
        file_put_contents($path, '## 项目 Error code目录');
        file_put_contents($path, "\n", FILE_APPEND);
        file_put_contents($path, ">\n", FILE_APPEND);
        file_put_contents($path, "> 环境全局`APP_ID`是 `".APP_ID."` (错误码组合方式: `APP_ID+错误码前缀+实际错误码`)", FILE_APPEND);
        foreach ($errorCodes as $errorCode) {
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            $str = sprintf(
                '### %s. %s(`%s`, 前缀:%s)',
                $count++,
                $errorCode['desc'],
                $errorCode['class'],
                $errorCode['prefix'],
            );
            file_put_contents($path, $str, FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, "---", FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, '| 枚举 | 错误码 | 错误信息 |', FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            file_put_contents($path, '|:-------|:-------:|:-------:|', FILE_APPEND);
            file_put_contents($path, "\n", FILE_APPEND);
            foreach ($errorCode['cases'] as $case) {
                file_put_contents(
                    $path,
                    sprintf(
                        '| `%s` | %s | %s |',
                        $case['name'],
                        $case['code'],
                        $case['errorMsg'],
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