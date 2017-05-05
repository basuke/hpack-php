<?php

namespace HTTP2\HPACK;
use HTTP2\HPACK\Representation as R;

class Encoder
{
    /**
     * @var DynamicTable
     */
    protected $table;

    /**
     * @var bool
     */
    protected $huffmanCoding;

    public function __construct()
    {
        $this->table = DynamicTable::table();
    }

    public function encode(array $headerList)
    {
        $result = [];

        foreach ($headerList as $headerField) {
            if (count($headerField) == 3) {
                list($name, $value, $kind) = $headerField;
            } else {
                list($name, $value) = $headerField;
                $kind = null;
            }

            if ($kind != R::LITERAL_NO_INDEXING_HEADER_FIELD
                && $kind != R::LITERAL_NEVER_INDEXING_HEADER_FIELD) {
                $index = $this->table->find($name, $value);
                $kind = R::LITERAL_INDEXING_HEADER_FIELD;
            } else {
                $index = false;
            }

            if ($index !== false) {
                $result[] = R::encodeHeaderField(R::INDEXED_HEADER_FIELD, $index);
            } else {
                $index = $this->table->find($name);
                if ($index !== false) {
                    $headerField = [$index, $value];
                } else {
                    $headerField = [$name, $value];
                }

                $useHuffmanCoding = $this->huffmanCoding;
                $result[] = R::encodeHeaderField($kind, $headerField, $useHuffmanCoding);

                if ($kind === R::LITERAL_INDEXING_HEADER_FIELD) {
                    $this->table->add($name, $value);
                }
            }
        }

        return implode('', $result);
    }

    public function setHuffmanCoding($flag)
    {
        $this->huffmanCoding = $flag;
    }

    public function setTableMaxSize($size)
    {
        $this->table->setMaxSize($size);
    }
}