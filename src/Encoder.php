<?php

namespace HTTP2\HPACK;

class Encoder
{
    public function encode(array $headerList)
    {
        $result = [];


        return implode('', $result);
    }

    public function encodeHeaderField($name, $value)
    {
        $found = $this->table->find($name, $value);
    }
}