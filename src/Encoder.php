<?php

namespace HTTP2\HPACK;

class Encoder
{
    /**
     * @var DynamicTable
     */
    public $table;

    public function __construct()
    {
        $this->table = DynamicTable::table();
    }

    public function encode(array $headerList)
    {
        $result = [];

        foreach ($headerList as $headerField) {
            $result[] = $this->encodeHeaderField(... $headerField);
        }

        return implode('', $result);
    }

    public function encodeHeaderField($name, $value)
    {
        $found = $this->table->find($name, $value);
        if ($found !== false) {

        }

        return "$found";
    }
}