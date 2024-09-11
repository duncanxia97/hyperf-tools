<?php
/**
 * @author XJ.
 * @Date   2023/8/15 0015
 */

namespace Hyperf\Stringable;

use Hyperf\Stringable\Str as Base;

/**
 * @author XJ.
 * @Date   2023/8/15 0015
 * @mixin Base
 */
class Str
{
    /**
     * Get the smallest possible portion of a string between two given values.
     *
     * @param string $subject
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    public static function betweenFirst($subject, $from, $to)
    {
        if ($from === '' || $to === '') {
            return $subject;
        }

        return static::before(static::after($subject, $from), $to);
    }
}