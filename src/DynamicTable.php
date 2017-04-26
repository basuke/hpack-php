<?php

namespace HTTP2\HPACK;

class DynamicTable extends Table
{
    /** @var Table */
    private $staticTable;

    /** @var int */
    private $staticSize;

    public function __construct(array $entries = [])
    {
        parent::__construct($entries);

        $this->staticTable = Table::staticTable();
        $this->staticSize = $this->staticTable->count();
    }

    public function get($index)
    {
        if ($index <= $this->staticSize) {
            return $this->staticTable->get($index);
        } else {
            return parent::get($index - $this->staticSize);
        }
    }

    public function add($value)
    {

    }
}