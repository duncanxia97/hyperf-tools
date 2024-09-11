<?php
/**
 * Created by XJ.
 * Date: 2021/10/13
 */

namespace Fatbit\HyperfTools\Router;

use Hyperf\HttpServer\Router\DispatcherFactory as BaseDispatcherFactory;
use Hyperf\HttpServer\Router\RouteCollector;
use Symfony\Component\Finder\Finder;

class DispatcherFactory extends BaseDispatcherFactory
{

    protected $routeDir = [
        BASE_PATH . '/routes',
    ];


    public function initConfigRoute()
    {
        parent::initConfigRoute();
        // 过滤有效文件目录
        $routeDir = array_filter($this->routeDir, function ($path) {
            return is_dir($path);
        });
        if (empty($routeDir)) {
            return;
        }
        // 获取路由目录下面有路由文件(只取当前目录的 不会递归获取)
        foreach ((new Finder())->depth(0)->in($routeDir)->files() as $file)
        {
            // 加载路由文件
            require_once $file->getRealPath();
        }
    }
}