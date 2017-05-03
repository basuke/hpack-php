<?php

namespace HTTP2\HPACK;
use HTTP2\HPACK\Representation as R;

class Encoder
{
    /**
     * @var DynamicTable
     */
    protected $table;

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

                $result[] = R::encodeHeaderField($kind, $headerField);

                if ($kind === R::LITERAL_INDEXING_HEADER_FIELD) {
                    $this->table->add($name, $value);
                }
            }
        }

        return implode('', $result);
    }

}