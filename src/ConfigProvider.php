<?php

namespace Fatbit\HyperfTools;

use Fatbit\HyperfTools\Core\Model\AbstractModel;
use Fatbit\HyperfTools\Database\Commands\HyperfModelCommand;
use Fatbit\HyperfTools\Exceptions\EmptyListException;
use Fatbit\HyperfTools\Exceptions\Handlers\HttpExceptionHandler;
use Fatbit\HyperfTools\HttpServer\HttpRequest;
use Fatbit\HyperfTools\HttpServer\HttpResponse;
use Fatbit\HyperfTools\HttpServer\Middleware\HttpTraceMiddleware;
use Fatbit\HyperfTools\Params\ReturnPageListParam;
use Hyperf\Collection\Arr;
use Hyperf\Database\Commands\ModelCommand;
use Hyperf\Database\Commands\ModelOption;
use Hyperf\Database\Model\Builder;
use Hyperf\Devtool\Generator\ControllerCommand;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\Stringable\Str;

/**
 * @author XJ.
 * Date: 2023/7/7 0007
 */
class ConfigProvider
{
    public function __invoke(): array
    {
        $this->define();
        $this->loadMethods();

        return [
            'dependencies' => [
                RequestInterface::class                             => HttpRequest::class,
                ResponseInterface::class                            => HttpResponse::class,
                Core\HttpServer\Interfaces\ResponseInterface::class => HttpResponse::class,
                DispatcherFactory::class                            => Router\DispatcherFactory::class,
                ModelCommand::class                                 => HyperfModelCommand::class,
                ControllerCommand::class                            => Commands\Generator\ControllerCommand::class,
            ],
            'annotations'  => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'exceptions'   => [
                'handler' => [
                    'http' => [
                        HttpExceptionHandler::class
                    ],
                ]
            ],
            'middlewares'  => [
                'http' => [
                    HttpTraceMiddleware::class,
                ],
            ],
            'databases'    => [
                'default' => [
                    'commands' => [
                        'gen:model' => [
                            //需要删除掉自己项目中的该配置项，否则会报错误
                            'uses'             => AbstractModel::class,
                            'refresh_fillable' => true,
                            'force_casts'      => true,
                            'inheritance'      => 'AbstractModel',
                            'property_case'    => ModelOption::PROPERTY_CAMEL_CASE,
                            'with_comments'    => true,
                            'suffix'           => 'model',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * 定义
     *
     * @author XJ.
     * @Date   2023/7/24 0024
     */
    public function define()
    {
        define('APP_ID', env('APP_ID', 100));
    }

    /**
     * 加载方法
     *
     * @author XJ.
     * @Date   2023/7/24 0024
     */
    public function loadMethods()
    {
        // 模型分页方法
        Builder::macro(
            'pageData',
            function (array|string $columns = ['*'], bool $isThrow = true): ReturnPageListParam {
                $page     = make(RequestInterface::class)?->input('page', 1) ?: 1;
                $pageSize = make(RequestInterface::class)?->input('pageSize', 15) ?: 15;
                $total    = null;
                if ($page == 1) {
                    /** @var Builder $this */
                    $total = $this->toBase()->getCountForPagination();
                    if ($total == 0) {
                        $isThrow && throw new EmptyListException();

                        return new ReturnPageListParam(
                            [
                                'list'  => [],
                                'total' => 0,
                            ]
                        );
                    }
                }

                return new ReturnPageListParam(
                    [
                        'list'  => $this->forPage($page, $pageSize)->get($columns)->all(),
                        'total' => $total,
                    ]
                );
            }
        );

        // 增加数组帮助类方法
        Arr::macro(
            'sum',
            function (array $arr, string|null|callable|array $field = null, bool $isReturnArray = false) {
                return arrSum($arr, $field, $isReturnArray);
            }
        );
        Arr::macro(
            'indexBy',
            function (
                $arr,
                $keys,
                bool $isAppend = false,
                $columnKeys = true,
                string $keyGlue = '-',
                $fillNotExistKeyVal = 0
            ): ?array {
                return arrIndexBy($arr, $keys, $isAppend, $columnKeys, $keyGlue, $fillNotExistKeyVal);
            }
        );
        Arr::macro(
            'sortBy',
            function (array $arr, string|callable $field, int $option = SORT_REGULAR, bool $descending = false): array {
                return arrSortBy($arr, $field, $option, $descending);
            }
        );
        Arr::macro(
            'column',
            function (array $arr, callable|string|array|null $column, callable|string|array|null $key = null): array {
                return arrColumn($arr, $column, $key);
            }
        );
        Arr::macro(
            'keyConvert',
            function (array $arr, int|callable $toType = 1) {
                return arrKeyConvert($arr, $toType);
            }
        );
        Arr::macro(
            'coMap',
            function (array $arr, callable $call, int $limit = 5) {
                return arrCoMap($arr, $call, $limit);
            }
        );
        Arr::macro(
            'demote',
            function (array $arr): array {
                return array_demote($arr);
            }
        );
        Arr::macro(
            'toTree',
            function (
                array     $list,
                          $pid = 0,
                string    $pidField = 'pid',
                string    $pkField = 'id',
                ?int      $maxLevel = null,
                string    $childrenName = 'children',
                int       $level = 1,
                ?callable $toVal = null
            ): ?array {
                return arr2tree($list, $pid, $pidField, $pkField, $maxLevel, $childrenName, $level, $toVal);
            }
        );
        Arr::macro(
            'fromTree',
            function (array $tree, string $childrenName = 'children', int $level = 1, array &$arr = []): ?array {
                return tree2arr($tree, $childrenName, $level, $arr);
            }
        );
        Str::macro(
            'betweenFirst',
            function ($subject, $from, $to) {
                if ($from === '' || $to === '') {
                    return $subject;
                }

                return Str::before(Str::after($subject, $from), $to);
            }
        );

    }

}