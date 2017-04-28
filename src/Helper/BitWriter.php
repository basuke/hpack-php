<?php
/**
 * Created by PhpStorm.
 * User: basuke
 * Date: 4/27/17
 * Time: 6:30 PM
 */

namespace HTTP2\HPACK\Helper;


class BitWriter
{
    private $chars;

    private $currentValue = 0;
    private $currentBits = 0;

    /**
     * BitStream constructor.
     */
    public function __construct()
    {
        $this->chars = [];
    }

    public function result()
    {
        if ($this->currentBits > 0) {
            $bits = (8 - $this->currentBits);
            $pad = (1 << $bits) - 1;
            $ascii = ($this->currentValue << $bits) | $pad;
            $this->chars[] = chr($ascii);
        }
        return implode('', $this->chars);
    }

    /**
     * @param $value
     * @param $bits
     * @return static
     */
    public function write($value, $bits)
    {
        if ($this->currentBits) {
            $value = ($this->currentValue << $bits) | $value;
            $bits += $this->currentBits;
        }

        while ($bits >= 8) {
            $ascii = ($value >> ($bits - 8)) & 0xff;
            $bits -= 8;
            $value &= (1 << $bits) - 1;

            $this->chars[] = chr($ascii);
        }

        $this->currentBits = $bits;
        $this->currentValue = $value;

        return $this;
    }
}