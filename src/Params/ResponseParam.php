<?php
/**
 * @author XJ.
 * @Date   2023/7/31 0031
 */

namespace Fatbit\HyperfTools\Params;

use Fatbit\HyperfTools\Core\Param\AbstractParam;
use Hyperf\Contract\Arrayable;

class ResponseParam extends AbstractParam
{
    public readonly mixed  $data;

    public readonly int    $code;

    public readonly string $msg;

    public readonly int    $appId;

    public readonly ?string $traceId;

    public readonly ?array $debug;

    public readonly ?array $extra;

    public function __construct(array $data)
    {
        $this->data    = $data['data'];
        $this->code    = $data['code'];
        $this->msg     = $data['msg'];
        $this->appId   = $data['appId'];
        $this->traceId = $data['traceId'] ?? '';
        $this->debug   = $data['debug'];
        $this->extra   = $data['extra'];
    }

    public function toArray(): array
    {
        $retRes = [];
        $result = parent::toArray();
        $retRes += $result['extra'];
        unset($result['extra']);
        if (empty($result['debug'])) {
            unset($result['debug']);
        }
        $retRes         = $result + $retRes;
        $retRes['data'] = $retRes['data'] instanceof Arrayable ? $retRes['data']->toArray() : $retRes['data'];

        return $retRes;
    }


}