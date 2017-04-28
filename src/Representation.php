<?php

namespace HTTP2\HPACK;

use HTTP2\HPACK\Helper\Binary;

class Representation
{
    public static function encodeInt($value, $prefixBits, $bits = 0)
    {
        assert($prefixBits > 0 && $prefixBits <= 8);

        $max = (1 << $prefixBits) - 1;
        $firstByte = ($bits << $prefixBits) & 0xff;

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

    public static function decodeInt(Binary $binaryStream, $prefixBits)
    {
        assert($prefixBits > 0 && $prefixBits <= 8);

        $stream = $binaryStream->begin();

        $max = (1 << $prefixBits) - 1;

        $value = $stream->readUInt8();
        if ($value === false) {
            return false;
        }

        $value &= $max;

        if ($value == $max) {
            $shift = 0;

            do {
                $byte = $stream->readUInt8();
                if ($byte === false) {
                    return false;
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
            static::encodeInt(strlen($string), 7, $huffman ? 1 : 0)
            . $string;
    }

    public static function decodeStr(Binary $stream)
    {
        $workingStream = $stream->begin();

        $bits = $workingStream->peek(1) >> 7;
        $length = static::decodeInt($workingStream, 7);
        if ($length === false) {
            return false;
        }

        $str = $workingStream->read($length);
        if ($str === false) {
            return false;
        }

        $workingStream->commit();

        if ($bits) {
            $str = Huffman::decode($str);
        }
        return $str;
    }
}