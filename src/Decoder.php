<?php

namespace HTTP2\HPACK;

use HTTP2\HPACK\Helper\Binary;
use HTTP2\HPACK\Representation as R;

class Decoder
{
    /** @var DynamicTable */
    protected $table;

    public function __construct()
    {
        $this->table = DynamicTable::table();
    }

    /**
     * @param string $binary
     * @return array
     * @throws DecodeException
     */
    public function decode($binary)
    {
        $headerList = [];
        $stream = new Binary($binary);

        while (!$stream->isEmpty()) {
            list($type, $field) = R::decodeHeaderField($stream);

            switch ($type) {
                case R::INDEXED_HEADER_FIELD:
                    $field = $this->table->get($field);
                    $headerList[] = $field;
                    break;

                case R::LITERAL_INDEXING_HEADER_FIELD:
                    $field = $this->decodeHeaderField($type, ...$field);
                    $headerList[] = $field;
                    $this->table->add(... $field);
                    break;

                case R::LITERAL_NO_INDEXING_HEADER_FIELD:
                case R::LITERAL_NEVER_INDEXING_HEADER_FIELD:
                    $headerList[] = $this->decodeHeaderField($type, ...$field);
                    break;

                case R::TABLE_SIZE_UPDATE:
                default:
                    break;
            }
        }

        return $headerList;
    }

    protected function decodeHeaderField($kind, $name, $value)
    {
        if (is_integer($name)) {
            $name = $this->table->get($name);
            if (is_array($name)) {
                $name = $name[0];
            }
        }

        $field = [$name, $value];

        if ($kind !== R::LITERAL_INDEXING_HEADER_FIELD) {
            $field[] = $kind;
        }

        return $field;
    }
}