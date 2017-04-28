<?php
/**
 * Created by PhpStorm.
 * User: basuke
 * Date: 4/28/17
 * Time: 9:21 AM
 */

namespace HTTP2\HPACK\Helper;


class BitReader
{
    private $bytes;
    private $bitsRemains;

    /**
     * BitReader constructor.
     * @param string $binary
     */
    public function __construct($binary)
    {
        $this->bytes = array_map('ord', str_split($binary));
        $this->bitsRemains = 8;
    }

    public function read($bitsWanted)
    {
        assert($bitsWanted > 0);
        if ($bitsWanted > $this->length()) {
            return false;
        }

        $value = 0;
        $remains = $this->bitsRemains;

        while ($bitsWanted > 0) {
            $byte = $this->bytes[0] & ((1 << $remains) - 1);
            if ($bitsWanted >= $remains) {
                $value = ($value << $remains) | $byte;
                $bitsWanted -= $remains;
                $remains = 0;
            } else {
                $remains -= $bitsWanted;
                $value = ($value << $bitsWanted) | ($byte >> $remains);
                $bitsWanted = 0;
            }

            if ($remains == 0) {
                array_shift($this->bytes);
                $remains = 8;
            }
        }

        $this->bitsRemains = $remains;
        return $value;
    }

    public function length()
    {
        if (!$this->bytes) {
            return 0;
        }

        return (count($this->bytes) - 1) * 8 + $this->bitsRemains;

    }
}