<?php
/**
 * @author XJ.
 * @Date   2023/7/19 0019
 */

namespace Fatbit\HyperfTools\Utils;


use Fatbit\HyperfTools\ErrorCodes\SysErrorCode;
use Fatbit\HyperfTools\Exceptions\SystemException;

class ArrayList implements \ArrayAccess, \Countable, \IteratorAggregate
{

    /** @var mixed[] */
    protected $list = [];

    public static function isList($value): bool
    {
        return is_array($value) && (PHP_VERSION_ID < 80100
                ? !$value || array_keys($value) === range(0, count($value) - 1)
                : array_is_list($value)
            );
    }

    /**
     * Transforms array to ArrayList.
     *
     * @param array<mixed> $array
     *
     * @return static
     */
    public static function from(array $array)
    {
        if (!static::isList($array)) {
            throw new SystemException(SysErrorCode::TYPE_ERROR->setErrorMsg('Array is not valid list.'));
        }

        $obj       = new static;
        $obj->list = $array;

        return $obj;
    }


    /**
     * Returns an iterator over all items.
     *
     * @return \ArrayIterator<int, mixed>
     */
    public function &getIterator(): \Iterator
    {
        foreach ($this->list as &$item) {
            yield $item;
        }
        unset($item);
    }


    /**
     * Returns items count.
     */
    public function count(): int
    {
        return count($this->list);
    }


    /**
     * Replaces or appends a item.
     *
     * @param int|null $index
     * @param mixed        $value
     *
     */
    public function offsetSet($index, $value): void
    {
        if ($index === null) {
            $this->list[] = $value;

        } elseif (!is_int($index) || $index < 0 || $index >= count($this->list)) {
            throw new SystemException(SysErrorCode::TYPE_ERROR->setErrorMsg('Offset invalid or out of range'));

        } else {
            $this->list[$index] = $value;
        }
    }


    /**
     * Returns a item.
     *
     * @param int $index
     *
     * @return mixed
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($index)
    {
        if (!is_int($index) || $index < 0 || $index >= $this->count()) {
            throw new SystemException(SysErrorCode::TYPE_ERROR->setErrorMsg('Offset invalid or out of range'));
        }

        return $this->list[$index];
    }


    /**
     * Determines whether a item exists.
     *
     * @param int $index
     */
    public function offsetExists($index): bool
    {
        return is_int($index) && $index >= 0 && $index < $this->count();
    }


    /**
     * Removes the element at the specified position in this list.
     *
     * @param int $index
     *
     */
    public function offsetUnset($index): void
    {
        if (!is_int($index) || $index < 0 || $index >= $this->count()) {
            throw new SystemException(SysErrorCode::TYPE_ERROR->setErrorMsg('Offset invalid or out of range'));
        }

        array_splice($this->list, $index, 1);
    }


    /**
     * Prepends a item.
     *
     * @param mixed $value
     */
    public function prepend($value): void
    {
        $first = array_slice($this->list, 0, 1);
        $this->offsetSet(0, $value);
        array_splice($this->list, 1, 0, $first);
    }
}