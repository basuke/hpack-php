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

    public function encodeStringUsingHuffman(UnitTester $I)
    {
        $I->wantToTest("huffman encoding");

        verify(Representation::encodeStr("www.example.com", true))
            ->equals(B(
                0x8c,
                0xf1,0xe3,0xc2,0xe5,0xf2,0x3a,0x6b,0xa0,
                0xab,0x90,0xf4,0xff));
    }

    public function decodeStringWithHuffman(UnitTester $I)
    {
        $I->wantToTest("huffman decoding");

        verify(Representation::decodeStr(BS(
            0x8c,
            0xf1,0xe3,0xc2,0xe5,0xf2,0x3a,0x6b,0xa0,
            0xab,0x90,0xf4,0xff,
            "HHHHHHHHHHH"
        )))
            ->equals("www.example.com");
    }
}
