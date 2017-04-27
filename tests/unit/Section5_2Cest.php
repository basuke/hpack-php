<?php

namespace HTTP2\HPACK;

use UnitTester;

class Section5_2Cest
{
    protected $long_str;

    public function _before()
    {
        $this->long_str = str_repeat("hello world", 50);
        // length is 11 x 50 = 550

    }

    public function encodeString(UnitTester $I)
    {
        $I->wantToTest("string literal encoding");

        verify(Representation::encodeStr("Hello world"))
            ->equals(B(11, "Hello world"));

        verify(Representation::encodeStr($this->long_str))
            ->equals(B(
                0x7f,
                0x80 | (550 - 0x7f) % 0x80, // = 0x80 | 39
                3,
                $this->long_str));
    }

    public function decodeString(UnitTester $I)
    {
        $I->wantToTest("string literal decoding");

        verify(Representation::decodeStr(BS(11, "Hello world!!!!!")))
            ->equals("Hello world");

        verify(Representation::decodeStr(BS(
            0x7f,
            0x80 | (550 - 0x7f) % 0x80, // = 0x80 | 39
            3,
            $this->long_str)))
            ->equals($this->long_str);

        verify(Representation::decodeStr(BS(11, "Hello")))
            ->false();
    }
}
