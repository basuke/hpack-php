<?php
/**
 * Created by PhpStorm.
 * User: basuke
 * Date: 4/27/17
 * Time: 4:09 PM
 */

namespace HTTP2\HPACK;


use HTTP2\HPACK\Helper\BitReader;
use HTTP2\HPACK\Helper\BitWriter;

class Huffman
{

    public static function encode($string)
    {
        $bs = new BitWriter();

        $len = strlen($string);
        for ($i = 0; $i < $len; $i++) {
            $code = ord($string[$i]);
            list($value, $bits) = HuffmanCodes::$encodeHash[$code];
            $bs->write($value, $bits);
        }
        return $bs->result();
    }

    public static function decode($binary)
    {
        $bs = new BitReader($binary);
        $chars = [];

        while ($bs->length() > 0) {
            $char = static::decodeChar($bs);
            if ($char === false) {
                return false;
            } elseif ($char === '') {
                break;
            } else {
                $chars[] = $char;
            }
        }

        return implode('', $chars);
    }

    protected static function decodeChar(BitReader $bs)
    {
        $currentBits = 0;
        $value = 0;

        foreach (HuffmanCodes::$decodeHash as $size => $codes) {
            $bitsToRead = $size - $currentBits;

            $newBits = $bs->read($bitsToRead);
            if ($newBits === false) {
                if ($value === ((1 << $currentBits) - 1)) {
                    return '';
                } else {
                    return false;
                }
            }

            $value = ($value << $bitsToRead) | $newBits;
            $currentBits = $size;

            if (isset($codes[$value])) {
                return chr($codes[$value]);
            }
        }
        return false;
    }
}