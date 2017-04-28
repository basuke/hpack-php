<?php
/**
 * Created by PhpStorm.
 * User: basuke
 * Date: 4/27/17
 * Time: 4:09 PM
 */

namespace HTTP2\HPACK;


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
}