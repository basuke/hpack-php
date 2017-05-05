<?php

namespace HTTP2\HPACK;

use HTTP2\HPACK\Helper\Binary;

class Representation
{
    const INDEXING = 0;
    const DONT_INDEX = 1;
    const NEVER_INDEX = 2;

    const INDEXED_HEADER_FIELD = 1;
    const LITERAL_INDEXING_HEADER_FIELD = 2;
    const LITERAL_NO_INDEXING_HEADER_FIELD = 3;
    const LITERAL_NEVER_INDEXING_HEADER_FIELD = 4;
    const TABLE_SIZE_UPDATE = 5;

    const FLAG_INDEXED_HEADER_FIELD = 0x80;
    const MASK_INDEXED_HEADER_FIELD = 0x80;

    const FLAG_INDEXING_HEADER_FIELD = 0x40;
    const MASK_INDEXING_HEADER_FIELD = 0xc0;

    const FLAG_NO_INDEXING_HEADER_FIELD = 0x00;
    const MASK_NO_INDEXING_HEADER_FIELD = 0xf0;

    const FLAG_NEVER_INDEXING_HEADER_FIELD = 0x10;
    const MASK_NEVER_INDEXING_HEADER_FIELD = 0xf0;

    const FLAG_TABLE_SIZE_UPDATE = 0x20;
    const MASK_TABLE_SIZE_UPDATE = 0xe0;

    /**
     * @param int $kind
     * @param int|array $field
     * @param bool $useHuffmanCoding
     * @return string
     * @internal param $ int|[string] $field
     */
    public static function encodeHeaderField($kind, $field, $useHuffmanCoding = false)
    {
        $result = [];
        switch ($kind) {
            case static::INDEXED_HEADER_FIELD:
                assert(is_integer($field));

                $result[] = static::encodeInt($field, 7, 0x80);
                break;

            case static::LITERAL_INDEXING_HEADER_FIELD:
                $result = static::encodeLiteralHeaderField($field, 6, 0x40, $useHuffmanCoding);
                break;

            case static::LITERAL_NO_INDEXING_HEADER_FIELD:
                $result = static::encodeLiteralHeaderField($field, 4, 0x00, $useHuffmanCoding);
                break;

            case static::LITERAL_NEVER_INDEXING_HEADER_FIELD:
                $result = static::encodeLiteralHeaderField($field, 4, 0x10, $useHuffmanCoding);
                break;

            case static::TABLE_SIZE_UPDATE:
                assert(is_integer($field));

                $result[] = static::encodeInt($field, 5, 0x20);
                break;

            default:
                assert(false);
                break;
        }
        return implode('', $result);
    }

    protected static function encodeLiteralHeaderField($field, $prefixBits, $flags, $useHuffmanCoding)
    {
        assert(is_array($field) && count($field) == 2);

        $result = [];
        list($key, $value) = $field;

        if (is_integer($key)) {
            $result[] = static::encodeInt($key, $prefixBits, $flags);
        } else {
            $result[] = static::encodeInt(0, $prefixBits, $flags);
            $result[] = static::encodeStr($key, $useHuffmanCoding);
        }
        $result[] = static::encodeStr($value, $useHuffmanCoding);
        return $result;
    }

    /**
     * @param Binary $stream
     * @return mixed
     * @throws DecodeException
     */
    public static function decodeHeaderField(Binary $stream)
    {
        $byte = ord($stream->peek(1));

        if (($byte & static::MASK_INDEXED_HEADER_FIELD)
            == static::FLAG_INDEXED_HEADER_FIELD) {
            // Indexed
            return [
                static::INDEXED_HEADER_FIELD,
                static::decodeInt($stream, 7)
            ];
        } elseif (($byte & static::FLAG_INDEXING_HEADER_FIELD)
            == static::FLAG_INDEXING_HEADER_FIELD) {
            // literal with indexing
            return [
                static::LITERAL_INDEXING_HEADER_FIELD,
                static::decodeLiteralHeaderField($stream, 6)
            ];
        } elseif (($byte & static::MASK_NO_INDEXING_HEADER_FIELD)
            == static::FLAG_NO_INDEXING_HEADER_FIELD) {
            // literal without indexing
            return [
                static::LITERAL_NO_INDEXING_HEADER_FIELD,
                static::decodeLiteralHeaderField($stream, 4)
            ];
        } elseif (($byte & static::MASK_NEVER_INDEXING_HEADER_FIELD)
            == static::FLAG_NEVER_INDEXING_HEADER_FIELD) {
            // literal with never indexing
            return [
                static::LITERAL_NEVER_INDEXING_HEADER_FIELD,
                static::decodeLiteralHeaderField($stream, 4)
            ];
        } elseif (($byte & static::MASK_TABLE_SIZE_UPDATE)
            == static::FLAG_TABLE_SIZE_UPDATE) {
            return [
                static::TABLE_SIZE_UPDATE,
                static::decodeInt($stream, 5)
            ];
        } else {
            throw new DecodeException("invalid header field representation");
        }
    }

    protected static function decodeLiteralHeaderField(Binary $source, $prefixBits)
    {
        $index = static::decodeInt($source, $prefixBits);

        if ($index === 0) {
            $key = static::decodeStr($source);
        } else {
            $key = $index;
        }
        $value = static::decodeStr($source);
        return [$key, $value];
    }

    public static function encodeInt($value, $prefixBits, $firstByte = 0)
    {
        assert($prefixBits > 0 && $prefixBits <= 8);

        $max = (1 << $prefixBits) - 1;

        if ($value <= $max) {
            $value |= $firstByte;
            return pack('C', $value);
        } else {
            $bytes = [$max | $firstByte];
            $value -= $max;
            while ($value >= 0x80) {
                $bytes[] = ($value % 0x80) | 0x80;
                $value = floor($value / 0x80);
            }
            $bytes[] = $value;

            return pack('C*', ... $bytes);
        }
    }

    /**
     * @param Binary $source
     * @param $prefixBits
     * @return int
     * @throws DecodeException
     */
    public static function decodeInt(Binary $source, $prefixBits)
    {
        assert($prefixBits > 0 && $prefixBits <= 8);

        $stream = $source->begin();

        $max = (1 << $prefixBits) - 1;

        $value = $stream->readUInt8();
        if ($value === false) {
            throw new DecodeException("don't have header length");
        }

        $value &= $max;

        if ($value == $max) {
            $shift = 0;

            do {
                $byte = $stream->readUInt8();
                if ($byte === false) {
                    throw new DecodeException("don't have enough body for integer");
                }

                $value = $value + (($byte & 0x7f) << $shift);
                $shift += 7;
            } while ($byte >= 0x80);
        }

        $stream->commit();
        return $value;
    }

    /**
     * @param $string
     * @param bool $huffman (default = false)
     * @return string
     */
    public static function encodeStr($string, $huffman = false)
    {
        if ($huffman) {
            $string = Huffman::encode($string);
        }
        return
            static::encodeInt(strlen($string), 7, $huffman ? 0x80 : 0)
            . $string;
    }

    /**
     * @param Binary $source
     * @return string
     * @throws DecodeException
     */
    public static function decodeStr(Binary $source)
    {
        $stream = $source->begin();

        $bits = ord($stream->peek(1)) >> 7;
        $length = static::decodeInt($stream, 7);

        $str = $stream->read($length);
        if ($str === false) {
            throw new DecodeException("don't have enough bytes");
        }

        if ($bits) {
            $str = Huffman::decode($str);
            if ($str === false) {
                throw new DecodeException("invalid huffman data");
            }
        }

        $stream->commit();
        return $str;
    }
}