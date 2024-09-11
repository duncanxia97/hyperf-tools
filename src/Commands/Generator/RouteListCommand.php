<?php
/**
 * @author XJ.
 * @Date   2023/8/1 0001
 */

namespace Fatbit\HyperfTools\Commands\Generator;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpServer\MiddlewareManager;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\HttpServer\Router\Handler;
use Hyperf\HttpServer\Router\RouteCollector;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Hyperf\Command\Annotation\Command;
use function Symfony\Component\String\b;

#[Command]
class RouteListCommand extends HyperfCommand
{
    public function __construct(private ContainerInterface $container, private ConfigInterface $config)
    {
        parent::__construct('gen:route-list');
    }


    public function handle()
    {
        $path   = $this->input->getOption('path');
        $server = $this->input->getOption('server');

        $factory   = $this->container->get(DispatcherFactory::class);
        $router    = $factory->getRouter($server);
        $routeList = $this->analyzeRouter($server, $router, $path);
        $routers   = [];
        foreach ($routeList as $route) {
            $action = $route['action'];
            switch (is_string($action)) {
                case strpos($route['action'], '@') !== false:
                    $action = explode('@', $route['action']);
                    break;
                case strpos($route['action'], '::') !== false:
                    $action = explode('::', $route['action']);
                    break;
            }
            $routers[$route['server']][$route['uri']] = [
                'method'     => implode('|', $route['method']),
                'action'     => $action,
                'middleware' => $route['middleware'],
            ];
        }
        $path = $this->makeDirectory(BASE_PATH . '/runtime/container/route_list.php');
        file_put_contents(
            $path,
            "<?php \n\nreturn " . Str::replace(
                ['0 => ', '1 => ', "array (", "),", "\n)"],
                ['', '', '[', '],', "\n]"],
                var_export($routers, true)
            ) . ";"
        );
    }

    protected function makeDirectory(string $path): string
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        return $path;
    }

    protected function configure()
    {
        $this->setDescription('Describe the routes information.')
            ->addOption('path', 'p', InputOption::VALUE_OPTIONAL, 'Get the detail of the specified route information by path')
            ->addOption('server', 'S', InputOption::VALUE_OPTIONAL, 'Which server you want to describe routes.', 'http');
    }

    protected function analyzeRouter(string $server, RouteCollector $router, ?string $path)
    {
        $data = [];
        [$staticRouters, $variableRouters] = $router->getData();
        foreach ($staticRouters as $method => $items) {
            foreach ($items as $handler) {
                $this->analyzeHandler($data, $server, $method, $path, $handler);
            }
        }
        foreach ($variableRouters as $method => $items) {
            foreach ($items as $item) {
                if (is_array($item['routeMap'] ?? false)) {
                    foreach ($item['routeMap'] as $routeMap) {
                        $this->analyzeHandler($data, $server, $method, $path, $routeMap[0]);
                    }
                }
            }
        }

        return $data;
    }

    protected function analyzeHandler(array &$data, string $serverName, string $method, ?string $path, Handler $handler)
    {
        $uri = $handler->route;
        if (!is_null($path) && !Str::contains($uri, $path)) {
            return;
        }
        if (is_array($handler->callback)) {
            $action = $handler->callback[0] . '::' . $handler->callback[1];
        } elseif (is_string($handler->callback)) {
            $action = $handler->callback;
        } else {
            $action = 'Closure';
        }
        $unique = "{$serverName}|{$uri}|{$action}";
        if (isset($data[$unique])) {
            $data[$unique]['method'][] = $method;
        } else {
            // method,uri,name,action,middleware
            $registeredMiddlewares = MiddlewareManager::get($serverName, $uri, $method);
            $middlewares           = $this->config->get('middlewares.' . $serverName, []);

            $middlewares   = array_merge($middlewares, $registeredMiddlewares);
            $data[$unique] = [
                'server'     => $serverName,
                'method'     => [$method],
                'uri'        => $uri,
                'action'     => $action,
                'middleware' => implode(PHP_EOL, array_unique($middlewares)),
            ];
        }
    }

    private function show(array $data, OutputInterface $output)
    {
        $rows = [];
        foreach ($data as $route) {
            $route['method'] = implode('|', $route['method']);
            $rows[]          = $route;
            $rows[]          = new TableSeparator();
        }
        $rows  = array_slice($rows, 0, count($rows) - 1);
        $table = new Table($output);
        $table
            ->setHeaders(['Server', 'Method', 'URI', 'Action', 'Middleware'])
            ->setRows($rows);
        $table->render();
    }
}