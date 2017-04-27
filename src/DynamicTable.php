<?php

namespace HTTP2\HPACK;

class DynamicTable extends Table
{
    const DEFAULT_MAX_SIZE = 32;

    /** @var Table */
    private $staticTable;

    /** @var int */
    private $staticSize;

    /** @var int */
    private $maxSize;

    protected function __construct(array $entries = [])
    {
        parent::__construct($entries);

        $this->staticTable = Table::staticTable();
        $this->staticSize = $this->staticTable->size();
        $this->maxSize = static::DEFAULT_MAX_SIZE;
    }

    /**
     * get max size of entries
     * @return int
     */
    public function maxSize()
    {
        return $this->maxSize;
    }

    /**
     * get entry at index (1-based)
     * @param $index
     * @return mixed
     * @throws DecodeException
     */
    public function get($index)
    {
        if ($index <= $this->staticSize) {
            return $this->staticTable->get($index);
        } else {
            return parent::get($index - $this->staticSize);
        }
    }

    /**
     * find entry index (1-based)
     * @param string $name
     * @param string|null $value
     * @return int|false value
     */
    public function find($name, $value = null)
    {
        $result = $this->staticTable->find($name, $value);
        if ($result === false) {
            $result = parent::find($name, $value);
            if ($result !== false) {
                $result += $this->staticSize;
            }
        }
        return $result;
    }

    public function add($name, $value = null)
    {
        $entry = $this->entry($name, $value);
        array_unshift($this->entries, $entry);
        return $this->staticSize + 1;
    }
}