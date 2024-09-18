<?php
/**
 * @author XJ.
 * Date: 2023/6/30 0030
 */
declare(strict_types=1);


use Fatbit\HyperfTools\Utils\Enums\DateGroupEnums;
use Fatbit\HyperfTools\Utils\Locker;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Coroutine\Concurrent;
use Hyperf\Coroutine\Parallel;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Snowflake\IdGeneratorInterface;
use Hyperf\Stringable\Str;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

if (!function_exists('container')) {
    /**
     * @author XJ.
     * Date: 2023/7/3 0003
     * @return ContainerInterface
     */
    function container(): \Psr\Container\ContainerInterface
    {
        return ApplicationContext::getContainer();
    }

}

if (!function_exists('di')) {
    /**
     * Created by XJ.
     * Date: 2021-07-14
     *
     * @param string|null $id
     *
     * @return mixed|ContainerInterface
     */
    function di(?string $id = null)
    {
        $container = container();
        if ($id) {
            return $container->get($id);
        }

        return $container;
    }
}

if (!function_exists('redis')) {
    /**
     * 获取redis 实例
     *
     * @author XJ.
     * Date: 2023/2/2 0002
     * @return \Hyperf\Redis\Redis
     */
    function redis(): \Hyperf\Redis\Redis
    {
        return di(\Hyperf\Redis\Redis::class);
    }
}

if (!function_exists('logger')) {

    /**
     * 获取logger 实例
     *
     * @author XJ.
     * Date: 2023/2/2 0002
     *
     * @param string $id
     *
     * @return \Psr\Log\LoggerInterface
     */
    function logger(string $id = 'log'): LoggerInterface
    {
        return di(LoggerFactory::class)->get($id);
    }
}

if (!function_exists('console')) {
    /**
     * 获取控制台实例
     *
     * @author XJ.
     * Date: 2023/2/2 0002
     * @return StdoutLoggerInterface
     */
    function console(): StdoutLoggerInterface
    {
        return di(StdoutLoggerInterface::class);
    }
}

if (!function_exists('context_set')) {
    /**
     * 设置上下文数据
     *
     * @author XJ.
     * Date: 2023/7/3 0003
     *
     * @param string $key
     * @param        $data
     *
     * @return bool
     */
    function context_set(string|callable $key, $data): bool
    {
        return (bool)\Hyperf\Context\Context::set(value($key), $data);
    }
}

if (!function_exists('context_get')) {
    /**
     * 获取上下文数据
     *
     * @author XJ.
     * Date: 2023/7/3 0003
     *
     * @param string $key
     *
     * @return mixed
     */
    function context_get(string|callable $key)
    {
        return \Hyperf\Context\Context::get(value($key));
    }
}

if (!function_exists('context_destroy')) {
    /**
     * 销毁上下文
     *
     * @author XJ.
     * Date: 2023/7/3 0003
     *
     * @param string|callable $key
     *
     */
    function context_destroy(string|callable $key, ?int $coroutineId = null)
    {
        \Hyperf\Context\Context::destroy(value($key), $coroutineId);
    }
}

if (!function_exists('context_has')) {
    /**
     *
     * @author XJ.
     * Date: 2023/7/3 0003
     *
     * @param string|callable $key
     *
     * @return bool
     *
     */
    function context_has(string|callable $key, ?int $coroutineId = null): bool
    {
        return (bool)\Hyperf\Context\Context::has(value($key), $coroutineId);
    }
}

if (!function_exists('event')) {
    /**
     * @author XJ.
     * Date: 2023/7/3 0003
     *
     * @param object $dispatch
     *
     * @return object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function event(object $dispatch): object
    {
        return container()->get(EventDispatcherInterface::class)->dispatch($dispatch);
    }
}

if (!function_exists('getCache')) {
    /**
     * Created by XJ.
     * Date: 2022/5/19
     *
     * @param string|callable $key      缓存键
     * @param callable        $callback 数据回调
     * @param int|callable    $ttl      缓存时间
     * @param callable|bool   $isForce  是否强制刷新缓存
     * @param bool            $isNx     是否开启nx
     *
     * @return array|float|int|object|string|null
     */
    function getCache(string|callable $key, callable $callback, int|callable $ttl = 0, callable|bool $isForce = false, bool $isNx = false)
    {
        $redis   = make(Redis::class);
        $key     = value($key);
        $isForce = value($isForce);
        if ($isForce && $isNx) {
            // 删除存在的键
            $redis->del($key);
        }
        if ($isForce || !$redis->exists($key)) {
            // 缓存不存在或强制刷新
            $ttl  = value($ttl);
            $data = $callback();
            if (is_null($data)) {
                return $data;
            }
            $timeout = $ttl < 1 ? null : $ttl;
            if ($isNx) {
                $timeout   = is_null($timeout) ? [] : ['EX' => $timeout];
                $timeout[] = 'NX';
            }
            $redis->set($key, arr2json($data), $timeout);

            return $data;
        }

        return json2arr($redis->get($key));
    }
}

if (!function_exists('to_number')) {
    /**
     * 转换数字格式
     * Created by XJ.
     * Date: 2020/11/19.
     *
     * @param string|float|int $var                 数值
     * @param int              $decimals            小数位
     * @param string           $thousands_separator 千分位
     *
     * @return string|null
     */
    function to_number($var, int $decimals = 4, string $thousands_separator = ''): ?string
    {
        if (!is_numeric($var)) {
            return null;
        }
        if (is_string($var)) {
            // 千分位替换
            $var = str_replace(',', '', $var);
        }

        return number_format((float)$var, $decimals, '.', $thousands_separator);
    }
}

if (!function_exists('arr2json')) {
    /**
     * 序列化
     * Created by XJ.
     * Date: 2018/11/21.
     *
     * @param array|object $val
     * @param int          $flag = JSON_UNESCAPED_UNICODE
     *
     * @return null|string
     */
    function arr2json($val, int $flag = 256)
    {
        return is_array($val) || is_object($val) ? json_encode($val, $flag) : $val;
    }
}

if (!function_exists('json2arr')) {
    /**
     * 反序列化
     * Created by XJ.
     * Date: 2018/11/21.
     *
     * @param string|array|object $val
     *
     * @return null|array|object|string
     */
    function json2arr($val)
    {
        if (is_array($val) || $val === null || is_object($val) || is_numeric($val) || is_bool($val)) {
            return $val;
        }
        $tempVal = json_decode($val, true);

        return $tempVal ?? (is_string($val) ? $val : []);
    }
}

if (!function_exists('quick_sort')) {
    /**
     * 快速排序
     * Created by XJ.
     * Date: 2019/2/21.
     *
     * @param bool|int $order
     */
    function quick_sort(array $arr, $order = 0): array
    {
        if (!isset($arr[1])) {
            return $arr;
        }
        $mid        = $arr[0];
        $leftArray  = [];
        $rightArray = [];
        foreach ($arr as $v) {
            if ($v > $mid) {
                $rightArray[] = $v;
            }
            if ($v < $mid) {
                $leftArray[] = $v;
            }
        }
        $leftArray   = quick_sort($leftArray, $order);
        $leftArray[] = $mid;
        $rightArray  = quick_sort($rightArray, $order);
        if ($order) {
            $sortArr = array_reverse([...$leftArray, ...$rightArray]);
        } else {
            $sortArr = [...$leftArray, ...$rightArray];
        }

        return $sortArr;
    }
}

if (!function_exists('multi_quick_sort')) {
    /**
     * 多维数组快速排序
     * Created by XJ.
     * Date: 2019/2/21.
     *
     * @param $arr   array 需要排序的数组
     * @param $key   string 根据那个字段进行排序
     * @param $order mixed 正序还是倒序
     */
    function multi_quick_sort(array $arr, string $key, bool $order = false): array
    {
        if (!isset($arr[1][$key])) {
            return $arr;
        }
        $mid = $arr[0];
        unset($arr[0]);
        $leftArray  = [];
        $rightArray = [];
        foreach ($arr as $v) {
            if ($v[$key] > $mid[$key]) {
                $rightArray[] = $v;
            } else {
                $leftArray[] = $v;
            }
        }
        $leftArray   = multi_quick_sort($leftArray, $key, $order);
        $leftArray[] = $mid;
        unset($mid);
        $rightArray = multi_quick_sort($rightArray, $key, $order);
        if ($order) {
            $sortArr = array_reverse([...$leftArray, ...$rightArray]);
        } else {
            $sortArr = [...$leftArray, ...$rightArray];
        }

        return $sortArr;
    }
}

if (!function_exists('img2base64')) {
    /**
     * 将图片转变为base64
     * Created by XJ.
     * Date: 2019/3/21.
     *
     * @param string $img
     *
     * @return false|string
     */
    function img2base64(string $img = '')
    {
        $file = file_get_contents($img);
        if ($file === false) {
            return false;
        }
        $imageInfo = getimagesize($img);

        return 'data:' . $imageInfo['mime'] . ';base64,' . chunk_split(base64_encode($file));
    }
}

if (!function_exists('fill_zero')) {
    /**
     * 数值填充零
     * Created by XJ.
     * Date: 2019/9/6.
     *
     * @param     $val  string|int|float 数值
     * @param     $bit  int 位数
     * @param int $type 0|1 类型(0: 左填充，1：右填充)
     *
     * @return string
     */
    function fill_zero(&$val, int $bit, int $type = 0): string
    {
        $val = str_pad((string)$val, $bit, '0', $type);

        return $val;
    }
}

if (!function_exists('hex2rgb')) {
    /**
     * hex转rgb
     * Created by XJ.
     * Date: 2019/9/27.
     *
     * @param $colour    string 颜色值
     * @param $is_string bool 是否返回字符串
     *
     * @return array|false|string
     */
    function hex2rgb(string $colour, bool $is_string = false)
    {
        if ($colour[0] === '#') {
            $colour = substr($colour, 1);
        }
        if (strlen($colour) === 6) {
            [$r, $g, $b] = [$colour[0] . $colour[1], $colour[2] . $colour[3], $colour[4] . $colour[5]];
        } elseif (strlen($colour) === 3) {
            [$r, $g, $b] = [$colour[0] . $colour[0], $colour[1] . $colour[1], $colour[2] . $colour[2]];
        } else {
            return $colour;
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);

        return $is_string ? 'rgb(' . $r . ',' . $g . ',' . $b . ')' : ['r' => $r, 'g' => $g, 'b' => $b];
    }
}

if (!function_exists('rgb2hex')) {
    /**
     * rgb转hex
     * Created by XJ.
     * Date: 2019/9/27.
     *
     * @param $rgb string rgb颜色值
     */
    function rgb2hex(string $rgb): string
    {
        if ($rgb[0] === '#') {
            return $rgb;
        }
        $regexp = '/^rgb\\(([0-9]{0,3}),\\s*([0-9]{0,3}),\\s*([0-9]{0,3})\\)/';
        preg_match($regexp, $rgb, $match);
        array_shift($match);
        $hexColor = '#';
        $hex      = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];
        for ($i = 0; $i < 3; ++$i) {
            $c     = $match[$i];
            $hexAr = [];
            while ($c > 16) {
                $r       = $c % 16;
                $c       = ($c / 16) >> 0;
                $hexAr[] = $hex[$r];
            }
            $hexAr[] = $hex[$c];
            $ret     = array_reverse($hexAr);
            $item    = implode('', $ret);
            fill_zero($item, 2, STR_PAD_LEFT);
            $hexColor .= $item;
        }

        return $hexColor;
    }
}

if (!function_exists('b642dec')) {
    /**
     * 64进制转10进制
     * Created by XJ.
     * Date: 2019/9/29.
     *
     * @param string $b64
     *
     * @return int
     */
    function b642dec(string $b64): int
    {
        $map = [
            '0' => 0,
            '1' => 1,
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            'A' => 10,
            'B' => 11,
            'C' => 12,
            'D' => 13,
            'E' => 14,
            'F' => 15,
            'G' => 16,
            'H' => 17,
            'I' => 18,
            'J' => 19,
            'K' => 20,
            'L' => 21,
            'M' => 22,
            'N' => 23,
            'O' => 24,
            'P' => 25,
            'Q' => 26,
            'R' => 27,
            'S' => 28,
            'T' => 29,
            'U' => 30,
            'V' => 31,
            'W' => 32,
            'X' => 33,
            'Y' => 34,
            'Z' => 35,
            'a' => 36,
            'b' => 37,
            'c' => 38,
            'd' => 39,
            'e' => 40,
            'f' => 41,
            'g' => 42,
            'h' => 43,
            'i' => 44,
            'j' => 45,
            'k' => 46,
            'l' => 47,
            'm' => 48,
            'n' => 49,
            'o' => 50,
            'p' => 51,
            'q' => 52,
            'r' => 53,
            's' => 54,
            't' => 55,
            'u' => 56,
            'v' => 57,
            'w' => 58,
            'x' => 59,
            'y' => 60,
            'z' => 61,
            '_' => 62,
            '=' => 63,
        ];
        $dec = 0;
        $len = strlen($b64);
        for ($i = 0; $i < $len; ++$i) {
            $b = $map[$b64[$i]];
            if ($b === null) {
                return 0;
            }
            $j   = $len - $i - 1;
            $dec += ($j === 0 ? $b : (2 << (6 * $j - 1)) * $b);
        }

        return $dec;
    }
}

if (!function_exists('dec2b64')) {
    /**
     * 10进制转64进制
     * Created by XJ.
     * Date: 2019/9/29.
     *
     * @param int|string $dec
     */
    function dec2b64($dec): string
    {
        if ($dec < 0) {
            return '';
        }
        $map = [
            0  => '0',
            1  => '1',
            2  => '2',
            3  => '3',
            4  => '4',
            5  => '5',
            6  => '6',
            7  => '7',
            8  => '8',
            9  => '9',
            10 => 'A',
            11 => 'B',
            12 => 'C',
            13 => 'D',
            14 => 'E',
            15 => 'F',
            16 => 'G',
            17 => 'H',
            18 => 'I',
            19 => 'J',
            20 => 'K',
            21 => 'L',
            22 => 'M',
            23 => 'N',
            24 => 'O',
            25 => 'P',
            26 => 'Q',
            27 => 'R',
            28 => 'S',
            29 => 'T',
            30 => 'U',
            31 => 'V',
            32 => 'W',
            33 => 'X',
            34 => 'Y',
            35 => 'Z',
            36 => 'a',
            37 => 'b',
            38 => 'c',
            39 => 'd',
            40 => 'e',
            41 => 'f',
            42 => 'g',
            43 => 'h',
            44 => 'i',
            45 => 'j',
            46 => 'k',
            47 => 'l',
            48 => 'm',
            49 => 'n',
            50 => 'o',
            51 => 'p',
            52 => 'q',
            53 => 'r',
            54 => 's',
            55 => 't',
            56 => 'u',
            57 => 'v',
            58 => 'w',
            59 => 'x',
            60 => 'y',
            61 => 'z',
            62 => '_',
            63 => '=',
        ];
        $b64 = '';
        do {
            $b64 = $map[($dec % 64)] . $b64;
            $dec /= 64;
        } while ($dec >= 1);

        return $b64;
    }
}

if (!function_exists('array_sequence')) {
    /**
     * 用于对二维数组某个key值排序
     * Created by XJ.
     * Date: 2020/11/19.
     *
     * @param array  $array 二维数组
     * @param string $field key值
     * @param int    $sort  排序方式，默认降序
     */
    function array_sequence(array $array, string $field, int $sort = SORT_DESC): array
    {
        $arrSort = [];
        foreach ($array as $uniqid => $row) {
            foreach ($row as $key => $value) {
                $arrSort[$key][$uniqid] = $value;
            }
        }
        array_multisort($arrSort[$field], $sort, $array);

        return $array;
    }
}

if (!function_exists('get_size')) {
    /**
     * Created by XJ.
     * Date: 2020/11/19.
     *
     * @param int|string $size   大小
     * @param string     $format 类型
     *
     * @return string
     */
    function get_size($size, string $format): string
    {
        $format = strtolower($format);
        $conf   = [
            'kb' => 1,
            'mb' => 2,
            'gb' => 3,
            'tb' => 4,
            'pb' => 5,
        ];
        $size   /= 1024 ** ($conf[$format] ?? 0);

        return to_number($size, 4);
    }
}

if (!function_exists('get_seconds')) {
    /**
     * Created by XJ.
     * Date: 2020/11/25.
     *
     * @param string $mark ['m', 'month', 'h']
     */
    function get_seconds(string $mark = 'm'): int
    {
        $mark = $mark === 'M' ? $mark : strtolower($mark);

        switch ($mark) {
            default:
                return 0;
            case 'm':
            case 'minute':
                return 60;
            case 'h':
            case 'hour':
                return 3600;
            case 'd':
            case 'day':
                // 3600*24
                return 86400;
            case 'w':
            case 'week':
                // 3600*24*7
                return 604800;
            case 'M':
            case 'month':
                // 3600*24*31
                return 2678400;
            case 'y':
            case 'year':
                // 3600*24*365
                return 31536000;
        }
    }
}

if (!function_exists('array_excess')) {
    /**
     * 去除数组多余数据
     * Created by XJ.
     * Date: 2020/11/25.
     */
    function array_excess(array $arr): array
    {
        return array_filter(array_unique($arr));
    }
}

if (!function_exists('to_arr_key_val')) {
    /**
     * 转换数组键对值
     * Created by XJ.
     * Date: 2021-05-24
     *
     * @param array|string      $fromField
     * @param array|string|null $toField
     *
     * @return array
     */
    function to_arr_key_val($fromField, $toField = null): array
    {
        $fieldArr  = [];
        $fromField = is_string($fromField) ? explode(',', $fromField) : $fromField;
        if (is_array($fromField) && $toField === null) {
            foreach ($fromField as $k => $v) {
                if (is_numeric($k)) {
                    $fieldArr[$v] = $v;
                } else {
                    $fieldArr[$k] = $v;
                }
            }
        }
        if (is_array($fromField) && is_array($toField)) {
            $fieldArr = array_combine($fromField, $toField);
        }
        if (is_array($fromField) && is_string($toField)) {
            $fieldArr = array_combine($fromField, array_pad([], count($fromField), $toField));
        }

        return $fieldArr;
    }
}

if (!function_exists('from_field2arr')) {
    /**
     * 根据字段创建一个数组
     * Created by XJ.
     * Date: 2021-05-25
     *
     * @param array           $list
     * @param string|string[] $fromField
     *
     * @return array|null
     */
    function from_field2arr(array $list, $fromField): ?array
    {
        $fromField = array_excess(is_string($fromField) ? explode(',', $fromField) : $fromField);
        if (count($fromField) === 0) {
            return null;
        }
        $arr = [];
        foreach ($fromField as $v) {
            $arr = [...$arr, ...array_column($list, $v)];
        }

        return $arr;
    }
}

if (!function_exists('arr2tree')) {
    /**
     * 数组转树形结构
     * Created by XJ.
     * Date: 2021-05-31
     *
     * @param array      $list
     * @param int|string $pid
     * @param string     $pidField
     * @param string     $pkField
     * @param int|null   $maxLevel
     * @param string     $childrenName
     * @param int        $level
     *
     * @return array|null
     */
    function arr2tree(
        array     $list,
                  $pid = 0,
        string    $pidField = 'pid',
        string    $pkField = 'id',
        ?int      $maxLevel = null,
        string    $childrenName = 'children',
        int       $level = 1,
        ?callable $toVal = null
    ): ?array
    {
        return __arr2tree($list, $pid, $pidField, $pkField, $maxLevel, $childrenName, $level, $toVal);
    }

    /**
     * 遍历优化(优化后是之前得一倍)
     * Created by XJ.
     * Date: 2021-05-31
     *
     * @param array      $list
     * @param int|string $pid
     * @param string     $pidField
     * @param string     $pkField
     * @param int|null   $maxLevel
     * @param string     $childrenName
     * @param int        $level
     *
     * @return array|null
     */
    function __arr2tree(
        array     &$list,
                  $pid = 0,
        string    $pidField = 'pid',
        string    $pkField = 'id',
        ?int      $maxLevel = null,
        string    $childrenName = 'children',
        int       $level = 1,
        ?callable $toVal = null
    ): ?array
    {
        if ($maxLevel !== null && $level > $maxLevel) {
            return [];
        }
        $data = [];
        foreach ($list as $k => $val) {
            if (!isset($val[$pidField], $val[$pkField])) {
                continue;
            }
            if ($val[$pidField] === $pid) {
                $temp                                     = $val + [
                        $childrenName . 'Level' => $level,
                        $childrenName           => __arr2tree(
                            $list,
                            $val[$pkField],
                            $pidField,
                            $pkField,
                            $maxLevel,
                            $childrenName,
                            $level + 1
                        ),
                    ];
                $temp[Str::camel('has_' . $childrenName)] = count($temp[$childrenName]) > 0;
                if (is_callable($toVal)) {
                    $temp = $toVal($temp) + $temp;
                }
                $data[] = $temp;
                unset($list[$k]);
            }
        }

        return $data;
    }
}

if (!function_exists('tree2arr')) {
    /**
     * 树形结构转数组结构
     * Created by XJ.
     * Date: 2021-06-17
     *
     * @param array  $tree
     * @param string $childrenName
     * @param int    $level
     * @param array  $arr
     *
     * @return array|null
     */
    function tree2arr(array $tree, string $childrenName = 'children', int $level = 1, array &$arr = []): ?array
    {
        foreach ($tree as $val) {
            $children = $val[$childrenName] ?? [];
            unset($val[$childrenName]);
            $arr[] = array_merge($val, [$childrenName . 'Level' => $level]);
            if ($children) {
                tree2arr($children, $childrenName, $level + 1, $arr);
            }
        }

        return $arr;
    }
}

if (!function_exists('getParents')) {
    /**
     * 获取父级
     * Created by XJ.
     * Date: 2021-06-21
     *
     * @param          $parentId
     * @param array    $list       数组
     * @param string   $pidField   pid字段
     * @param string   $pkField    主键字段
     * @param int|null $maxLevel   最大深度
     * @param bool     $isTree     是否返回树形结构
     * @param string   $parentName 父级字段名称
     * @param int      $level      当前层级
     * @param array    $data       数据
     *
     * @return array|null
     */
    function getParents(
        $parentId,
        array $list,
        string $pidField = 'pid',
        string $pkField = 'id',
        ?int $maxLevel = null,
        bool $isTree = true,
        string $parentName = 'parent',
        int $level = 1,
        array &$data = []
    ): ?array
    {
        if ($maxLevel && $maxLevel < $level) {
            return [];
        }
        $temp = [];
        foreach ($list as &$val) {
            if ($parentId === $val[$pkField]) {
                $temp_  = array_merge($val, [$parentName . 'Level' => $level]);
                $data[] = $temp_;
                unset($val);
                $res = getParents(
                    $temp_[$pidField],
                    $list,
                    $pidField,
                    $pkField,
                    $maxLevel,
                    $isTree,
                    $parentName,
                    $level + 1,
                    $data
                );
                if ($isTree) {
                    $temp_[$parentName] = $res;
                    $temp[]             = $temp_;
                }
            }
        }

        return $isTree ? $temp : $data;
    }
}

if (!function_exists('remove_empty_str')) {
    /**
     * 去除空字符串
     * Created by XJ.
     * Date: 2021-07-07
     *
     * @param string          $str
     * @param string[]|string $remove
     *
     * @return string
     */
    function remove_empty_str(string $str, $remove = [' ', '\n', '\r', '\t']): string
    {
        return str_replace($remove, '', $str);
    }
}

if (!function_exists('to_timestamp')) {
    /**
     * 转时间戳
     * Created by XJ.
     * Date: 2021-07-07
     *
     * @param string|false|int $time
     * @param int              $baseTimestamp
     *
     * @return int|null
     */
    function to_timestamp($time = false, int $baseTimestamp = 0): ?int
    {
        $baseTimestamp = $baseTimestamp ?: time();
        if (is_bool($time)) {
            return $baseTimestamp;
        }

        return (int)(is_numeric($time) ? $time : strtotime($time, $baseTimestamp));
    }
}

if (!function_exists('get_time_range')) {
    /**
     * Created by XJ.
     * Date: 2021-07-07
     *
     * @param string          $timeFlag 时间
     * @param bool|int|string $nowFlag  当前时间
     *
     * @return int[]|null
     */
    function get_time_range(string $timeFlag, $nowFlag = true): ?array
    {
        $timeGroup = explode(' - ', $timeFlag);
        if (isset($timeGroup[1])) {
            return [to_timestamp($timeGroup[0]), to_timestamp($timeGroup[1])];
        }
        unset($timeGroup);
        $timeArr = [
            'today'      => ['today', 'tomorrow -1second'],
            'yesterday'  => ['yesterday', 'today -1second'],
            'week'       => ['this week 00:00:00', 'next week 00:00:00 -1second'],
            'last week'  => ['last week 00:00:00', 'this week 00:00:00 -1second'],
            'month'      => ['first Day of this month 00:00:00', 'first Day of next month 00:00:00 -1second'],
            'last month' => ['first Day of last month 00:00:00', 'first Day of this month 00:00:00 -1second'],
            'year'       => ['this year 1/1', 'next year 1/1 -1second'],
            'last year'  => ['last year 1/1', 'this year 1/1 -1second'],
        ];
        if (is_string($nowFlag) && preg_match('/[-|+](.*?)month/', $timeFlag, $arr)) {
            $nowFlag = is_numeric(remove_empty_str($arr[1])) ? date('Y-m -') . $arr[1] . 'month' : $nowFlag;
        }
        $nowFlag = to_timestamp($nowFlag);
        if (isset($timeArr[$timeFlag])) {
            return [
                to_timestamp($timeArr[$timeFlag][0], $nowFlag),
                (int)to_timestamp($timeArr[$timeFlag][1], $nowFlag)
            ];
        }
        if (preg_match('/[-|+](.*?)month/', $timeFlag, $arr)) {
            $timeFlag = is_numeric(remove_empty_str($arr[1])) ? date('Y-m -') . $arr[1] . 'month' : $timeFlag;
        }
        $timeFlag = to_timestamp($timeFlag);

        return [$nowFlag > $timeFlag ? $timeFlag : $nowFlag, $timeFlag > $nowFlag ? $timeFlag : $nowFlag];
    }
}

if (!function_exists('to_array')) {
    /**
     * 任何类型转数组(去重)
     * Created by XJ.
     * Date: 2021-07-20
     *
     * @param mixed|callable $var
     *
     * @return array
     */
    function to_array($var): array
    {
        $var = value($var);

        return array_excess(is_array($var) ? $var : explode(',', $var));
    }
}

if (!function_exists('arrOnlyKSort')) {
    /**
     * Get a subset of the items from the given array. and key sort
     * Created by XJ.
     * Date: 2021/8/23
     *
     * @param array           $array
     * @param array           $keys
     * @param int|string|null $fillNotExistKeyVal 填充不存在的键值(null:不填充)
     *
     * @return array|null
     */
    function arrOnlyKSort(array $array, array $keys, $fillNotExistKeyVal = null): ?array
    {
        $keys  = array_fill_keys($keys, $fillNotExistKeyVal);
        $array = array_intersect_key($array, $keys);
        if (null !== $fillNotExistKeyVal) {
            $array = $array + $keys;
        }
        ksort($array);

        return $array;
    }
}

if (!function_exists('arrIndexBy')) {
    /**
     * 数组索引
     * Created by XJ.
     * Date: 2021/8/17
     *
     * @param array                 $arr                数组
     * @param string|array|string[] $keys               索引值
     * @param bool                  $isAppend           是否追加数组
     * @param bool                  $columnKeys         数据集键
     * @param string                $keyGlue            联合索引拼接字符串
     * @param int|string|null       $fillNotExistKeyVal 填充不存在的键值(null:不填充)
     *
     * @return array|null
     */
    function arrIndexBy($arr, $keys, bool $isAppend = false, $columnKeys = true, string $keyGlue = '-', $fillNotExistKeyVal = 0): ?array
    {
        $keys = to_array($keys);
        if (empty($keys)) {
            return null;
        }
        /**
         * @var array $columnKeys
         */
        if ($columnKeys !== true) {
            $columnKeys = to_array($columnKeys);
            if (empty($columnKeys)) {
                $columnKeys = true;
            }
        }
        $result = [];
        foreach ($arr as $value) {
            if (!is_array($value)) {
                $value = json_decode(json_encode($value), true);
            }
            $resultKey = implode($keyGlue, arrOnlyKSort($value, $keys, $fillNotExistKeyVal));
            if (is_array($columnKeys)) {
                $value = arrOnlyKSort($value, $columnKeys);
            }
            if ($isAppend) {
                $result[$resultKey] = [...($result[$resultKey] ?? []), ...[$value]];
            } else {
                $result[$resultKey] = $value;
            }
        }

        return $result;
    }
}

if (!function_exists('snowflake')) {
    /**
     * @author XJ.
     * @Date   2023/7/31 0031
     * @return IdGeneratorInterface|mixed|ContainerInterface
     */
    function snowflake(): IdGeneratorInterface
    {
        return di(IdGeneratorInterface::class);
    }
}

if (!function_exists('sfId')) {
    /**
     * 生成雪花id
     *
     * @author     XJ.
     * Date: 2023/7/3 0003
     *
     * @param int $count 生成次数(大于0则返回多个)
     *
     * @return int[]|int
     */
    function sfId(int $count = 0): int|array
    {
        $sfIdArr = [];
        do {
            $sfIdArr[] = snowflake()->generate();
        } while ($count--);
        if (count($sfIdArr) == 1) {
            return $sfIdArr[0];
        }

        return $sfIdArr;
    }
}

if (!function_exists('arrSum')) {
    /**
     * 数组计算
     * Created by XJ.
     * Date: 2021/11/25
     *
     * @param array                      $arr
     * @param string|callable|array|null $field
     * @param bool                       $isReturnArray
     *
     * @return float|int|array|float[]|int[]|mixed
     */
    function arrSum(array $arr, string|null|callable|array $field = null, bool $isReturnArray = false)
    {
        if (is_string($field)) {
            return array_sum(array_column($arr, $field));
        }
        if (is_array($field)) {
            // 批量计算数组模式
            $res       = [];
            $flipFiled = array_flip($field);
            foreach ($arr as $item) {
                $newItem = array_intersect_key($item, $flipFiled);
                foreach ($newItem as $k => $v) {
                    !isset($res[$k]) && $res[$k] = 0;
                    $res[$k] += $v;
                }
            }

            return $res;
        }
        if ($isReturnArray && is_callable($field)) {
            // 返回数组模式
            return array_reduce(
                $arr,
                function ($res, $val) use ($field) {
                    $tmp = $field($val);
                    foreach ($tmp as $k => $v) {
                        !isset($res[$k]) && $res[$k] = 0;
                        $res[$k] += $v;
                    }

                    return $res;
                },
                []
            );
        }

        return Collection::make($arr)->sum($field);
    }
}

if (!function_exists('arrSortBy')) {
    /**
     * 数组排序
     * Created by XJ.
     * Date: 2021/12/15
     *
     * @param array           $arr        数组
     * @param string|callable $field      排序字段或者排序方法
     * @param int             $option     排序规则
     * @param bool            $descending 倒序
     *
     * @return array
     */
    function arrSortBy(array $arr, string|callable $field, int $option = SORT_REGULAR, bool $descending = false): array
    {
        $sortArr    = [];
        $isCallable = !is_string($field) && is_callable($field);
        $concurrent = new Concurrent(10);
        foreach ($arr as $k => $v) {
            $concurrent->create(
                function () use (&$sortArr, $isCallable, $field, $v, $k) {
                    $sortArr[$k] = $isCallable ? $field($v, $k) : data_get($v, $field);
                }
            );
        }
        $descending ? arsort($sortArr, $option) : asort($sortArr, $option);
        $res = [];
        foreach ($sortArr as $key => $v) {
            $concurrent->create(
                function () use (&$res, $arr, $key) {
                    $res[$key] = $arr[$key];
                }
            );
        }

        return $res;
    }
}

if (!function_exists('arrColumn')) {
    /**
     * 对数组取一列数据或多列数据
     * Created by XJ.
     * Date: 2021/12/27
     *
     * @param array                      $arr    数组
     * @param callable|string|array|null $column 列
     * @param callable|string|array|null $key    列的key值
     *
     * @return array
     */
    function arrColumn(array $arr, callable|string|array|null $column, callable|string|array|null $key = null): array
    {
        $colIsStr = is_string($column);
        if ($colIsStr) {
            return array_column($arr, $column);
        }
        if (($colIsStr || is_null($column)) && is_string($key)) {
            return array_column($arr, $column, $key);
        }
        $res        = [];
        $colIsArr   = is_array($column);
        $keyIsArr   = is_array($key);
        $colCall    = is_callable($column) ? $column : (fn($v, $k) => $colIsArr ? Arr::only(
            $v,
            $column
        ) : $v);
        $keyCall    = is_callable($key) ? $key : (fn($v, $k) => $keyIsArr ? implode(
            '-',
            array_reduce(
                $key,
                fn($v1, $v2) => [...$v1, ...[$v[$v2] ?? '']],
                []
            )
        ) : (is_null($key) ? $k : $v[$key]));
        $concurrent = new Concurrent(10);
        foreach ($arr as $k => $v) {
            $concurrent->create(
                function () use (&$res, $k, $v, $keyCall, $colCall) {
                    $key       = $keyCall($v, $k);
                    $val       = $colCall($v, $k);
                    $res[$key] = $val;
                }
            );
        }

        return $res;
    }
}

if (!function_exists('arrKeyConvert')) {
    /**
     * 数组key 转换
     * Created by XJ.
     * Date: 2022/1/10
     *
     * @param array        $arr    数组
     * @param int|callable $toType 转换类型 (1: 蛇形key, 2: 小驼峰key, 3: 大驼峰key)
     *
     * @return array
     */
    function arrKeyConvert(array $arr, int|callable $toType = 1): array
    {
        $toType     = is_callable($toType) ? $toType : fn($v, $k) => match ($toType) {
            1       => Str::snake($k),
            2       => Str::camel($k),
            3       => Str::studly($k),
            default => $k,
        };
        $res        = [];
        $concurrent = new Concurrent(5);
        foreach ($arr as $key => $val) {
            $concurrent->create(
                function () use (&$res, $val, $key, $toType) {
                    $res[$toType($val, $key)] = $val;
                }
            );
        }

        return $res;
    }
}

if (!function_exists('array_demote')) {
    /**
     * 数组降级
     *
     * @author XJ.
     * Date: 2023/2/17 0017
     *
     * @param array $arr
     *
     * @return array
     */
    function array_demote(array $arr): array
    {
        $newArr = [];
        foreach ($arr as $val) {
            $val = $val instanceof Collection ? $val->all() : $val;
            if (is_array($val)) {
                $newArr = [...$newArr, ...$val];
            } else {
                $newArr[] = $val;
            }
        }

        return $newArr;
    }
}

if (!function_exists('arrCoMap')) {
    /**
     * 数组协程map
     * Created by XJ.
     * Date: 2022/1/10
     *
     * @param array    $arr   数组
     * @param callable $call  回调
     * @param int      $limit 协程数
     *
     * @return array
     */
    function arrCoMap(array $arr, callable $call, int $limit = 5): array
    {
        $parallel = new Parallel($limit);
        foreach ($arr as $key => $val) {
            $parallel->add(fn() => $call($val, $key), $key);
        }
        $res = $parallel->wait();

        return $res;
    }
}

if (!function_exists('time2Format')) {
    /**
     * 时间格式化
     *
     * @author XJ.
     * Date: 2022/9/16 0016
     *
     * @param int             $time
     * @param string|callable $format
     *
     * @return string
     */
    function time2Format(int $time, string|callable $format): string
    {
        return is_callable($format) ? $format($time) : date($format, $time);
    }
}

if (!function_exists('dateGroup')) {
    /**
     * 日期分组
     *
     * @author XJ.
     * Date: 2022/9/16 0016
     *
     * @param int                  $startTime
     * @param int                  $endTime
     * @param DateGroupEnums       $type
     * @param string|callable|null $groupFormat
     * @param int                  $step
     *
     * @return array
     */
    function dateGroup(int $startTime, int $endTime, DateGroupEnums $type, null|string|callable $groupFormat = null, int $step = 1)
    {
        if (is_null($groupFormat)) {
            $groupFormat = match ($type) {
                DateGroupEnums::HOUR    => 'Y-m-d H',
                DateGroupEnums::DAY     => 'Y-m-d',
                DateGroupEnums::WEEK    => 'Y年第W周',
                DateGroupEnums::MONTH   => 'Y-m',
                DateGroupEnums::QUARTER => fn(int $time) => date('Y', $time) . '第' . ceil(date('n', $time) / 3) . '季度',
                DateGroupEnums::YEAR    => 'Y',
            };
        }
        $res = [];
        $i   = 0;
        switch ($type) {
            default:
            case DateGroupEnums::DAY:
                $tempTime = strtotime(date('Y-m-d', $startTime));
                $endTime  = strtotime(date('Y-m-d', strtotime("- {$step} day +1 day", $endTime)));
                do {
                    $tempTime = strtotime('+' . $i . ' day', $startTime);
                    $res[]    = [
                        'name'  => time2Format($tempTime, $groupFormat),
                        'start' => strtotime(date('Y-m-d 00:00:00', $tempTime)),
                        'end'   => strtotime(date('Y-m-d 23:59:59', $tempTime)),
                    ];
                    $i        += $step;
                } while ($tempTime < $endTime);
                break;
            case DateGroupEnums::HOUR:
                $tempTime = strtotime(date('Y-m-d H' . ':00:00', $startTime));
                $endTime  = strtotime(date('Y-m-d H' . ':00:00', strtotime("- {$step} hour +1 hour", $endTime)));
                do {
                    $tempTime = strtotime('+' . $i . ' hour', $startTime);
                    $res[]    = [
                        'name'  => time2Format($tempTime, $groupFormat),
                        'start' => strtotime(date('Y-m-d H:00:00', $tempTime)),
                        'end'   => strtotime(date('Y-m-d H:59:59', $tempTime)),
                    ];
                    $i        += $step;
                } while ($tempTime < $endTime);
                break;
            case DateGroupEnums::WEEK:
                $tempTime = strtotime('this week Monday', $startTime);
                $endTime  = strtotime('this week Sunday', strtotime("-{$step} week +1 week", $endTime));
                do {
                    $week     = strtotime('this week Monday +' . $i . ' week', $startTime);
                    $tempTime = strtotime('this week Sunday 23:59:59', $week);
                    $res[]    = [
                        'name'  => time2Format($week, $groupFormat),
                        'start' => strtotime('this week Monday', $week),
                        'end'   => $tempTime
                    ];
                    $i        += $step;
                } while ($tempTime < $endTime);
                break;
            case DateGroupEnums::MONTH:
                $tempTime = strtotime(date('Y-m-01', $startTime));
                $endTime  = strtotime(date('Y-m-t', strtotime("- {$step} month +1 month", $endTime)));
                do {
                    $month    = strtotime('first day of +' . $i . ' month', $startTime);
                    $tempTime = strtotime(date('Y-m-t 23:59:59', $month));
                    $res[]    = [
                        'name'  => time2Format($month, $groupFormat),
                        'start' => strtotime(date('Y-m-01', $month)),
                        'end'   => $tempTime,
                    ];
                    $i        += $step;
                } while ($tempTime < $endTime);
                break;
            case DateGroupEnums::QUARTER:
                $tempTime    = strtotime(date('Y-m', $startTime));
                $quarterStep = $step * 3;
                $endTime     = date('Y-m', strtotime("- {$quarterStep} month +3 month", $endTime));
                do {
                    $quarter  = strtotime('first day of +' . $i . ' month', $startTime);
                    $q        = (int)ceil(date('n', $quarter) / 3);
                    $tempTime = strtotime(date('Y-m-t H:i:s', mktime(23, 59, 59, $q * 3, 1, (int)date('Y', $quarter))));
                    $res[]    = [
                        'name'  => time2Format($quarter, $groupFormat),
                        'start' => strtotime(date('Y-m-01', mktime(0, 0, 0, $q * 3 - 3 + 1, 1, (int)date('Y', $quarter)))),
                        'end'   => $tempTime,
                    ];
                    $i        += 3 + $step;
                } while ($tempTime < $endTime);
                break;
            case DateGroupEnums::YEAR:
                $tempTime = strtotime(date('Y-01-01', $startTime));
                $endTime  = strtotime(date('Y-12-31 23:59:59', strtotime("- {$step} year +1 year", $endTime)));
                do {
                    $year     = strtotime('+' . $i . ' year', $startTime);
                    $tempTime = strtotime(date('Y-12-31 23:59:59', $year));
                    $res[]    = [
                        'name'  => time2Format($year, $groupFormat),
                        'start' => strtotime(date('Y-01-01', $year)),
                        'end'   => strtotime(date('Y-12-31 23:59:59', $year)),
                    ];
                    $i        += $step;
                } while ($tempTime < $endTime);
                break;
        }

        return $res;
    }
}

if (!function_exists('redisLocker')) {
    /**
     * 获取locker
     *
     * @author XJ.
     * Date: 2023/6/16 0016
     *
     * @param string|callable|null $key
     *
     * @return Locker
     */
    function redisLocker(string|callable|null $key = null): Locker
    {
        return new Locker($key);
    }
}

if (!function_exists('isProdEnv')) {
    /**
     * 是否生产环境
     *
     * @author XJ.
     * @Date   2024/9/11 星期三
     * @return bool
     */
    function isProdEnv(): bool
    {
        return Str::startsWith(config('app_env', 'dev'), 'prod');
    }
}

if (!function_exists('spinLock')) {
    /**
     * 自旋锁
     *
     * @author XJ.
     * @Date   2024/9/18 星期三
     *
     * @param string   $lockKey   锁键名
     * @param callable $call      回调函数
     * @param int      $spinSleep 自旋等待时间
     * @param int      $timeout   回调超时时间
     *
     * @return mixed
     * @throws RedisException
     */
    function spinLock(string $lockKey, callable $call, int $spinSleep = 0, int $timeout = 60)
    {
        $lockKey = 's:spin_lock:' . $lockKey;
        $flag    = false;
        try {
            $endTime = time() + $timeout;
            spin_start:
            $flag = redis()->set($lockKey, 1, ['nx', 'ex' => $timeout]);
            if (!$flag || time() > $endTime) {
                $spinSleep && usleep($spinSleep * 1000);
                goto spin_start;
            }

            return $call();
        } finally {
            if ($flag) {
                redis()->del($lockKey);
            }
        }
    }
}