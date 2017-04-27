<?php

namespace HTTP2\HPACK;

use HTTP2\HPACK\Helper\Binary;

class Representation
{
    public static function encodeInt($value, $prefixBits, $currentByte = 0)
    {
        assert($prefixBits > 0 && $prefixBits <= 8);

        $max = (1 << $prefixBits) - 1;

        if ($value <= $max) {
            return pack('C', $value);
        } else {
            $bytes = [$max];
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

    public static function encodeStr($string)
    {
        return
            static::encodeInt(strlen($string), 7)
            . $string;
    }

    public static function decodeStr(Binary $stream)
    {
        $workingStream = $stream->begin();

        $length = static::decodeInt($workingStream, 7);
        if ($length === false) {
            return false;
        }

        $str = $workingStream->read($length);
        if ($str === false) {
            return false;
        }

        $workingStream->commit();
        return $str;
    }
}