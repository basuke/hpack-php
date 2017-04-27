<?php

namespace HTTP2\HPACK;

class DynamicTable extends Table
{
    const DEFAULT_MAX_SIZE = 1024;
    const ENTRY_SIZE_OVERHEAD = 32;

    /** @var Table */
    private $staticTable;

    /** @var int */
    private $staticSize;

    /** @var int */
    private $maxSize;

    /** @var int */
    private $size;

    protected function __construct(array $entries = [])
    {
        parent::__construct($entries);

        $this->staticTable = Table::staticTable();
        $this->staticSize = $this->staticTable->length();
        $this->maxSize = static::DEFAULT_MAX_SIZE;
        $this->size = $this->calculateTableSize();
    }

    /**
     * return calculated size of entry
     * @param string|array
     */
    public static function entrySize($entry)
    {
        if (is_array($entry)) {
            assert(count($entry) === 2);
            $name = $entry[0];
            $value = $entry[1];
        } else {
            $name = $entry;
            $value = '';
        }

        return strlen($name) + strlen($value) + static::ENTRY_SIZE_OVERHEAD;
    }

    /**
     * get current table size
     */
    public function size()
    {
        return $this->size;
    }

    protected function calculateTableSize()
    {
        $result = 0;
        foreach ($this->entries as $entry) {
            $result += static::entrySize($entry);
        }
        return $result;
    }

    /**
     * get max size of table
     * @return int
     */
    public function maxSize()
    {
        return $this->maxSize;
    }


    /**
     * set max size of table
     * @param int $newMaxSize
     */
    public function setMaxSize($newMaxSize)
    {
        assert($newMaxSize >= 0);

        while ($this->size > $newMaxSize && $this->entries) {
            $entry = array_pop($this->entries);
            $this->size -= static::entrySize($entry);
        }

        $this->maxSize = $newMaxSize;
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
        $this->size += static::entrySize($entry);

        $this->setMaxSize($this->maxSize());
        if ($this->length() === 0) {
            return false;
        }
        return $this->staticSize + 1;
    }
}