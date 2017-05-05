<?php

namespace HTTP2\HPACK;

use Helper\Unit;
use UnitTester;

class HuffmanEncodingCest
{
    public function test1(UnitTester $I)
    {
        $I->wantToTest("huffman encoding");

        verify(Huffman::encode("www.example.com"))
            ->equals(B(
                0xf1,0xe3,0xc2,0xe5,0xf2,0x3a,0x6b,0xa0,
                0xab,0x90,0xf4,0xff));

        verify(Huffman::encode("no-cache"))
            ->equals(B(0xa8,0xeb,0x10,0x64,0x9c,0xbf));
    }

    public function test2(UnitTester $I)
    {
        $I->wantToTest("huffman decoding");

        verify(Huffman::decode(B(
            0xf1,0xe3,0xc2,0xe5,0xf2,0x3a,0x6b,0xa0,
            0xab,0x90,0xf4,0xff)))
            ->equals("www.example.com");

        verify(Huffman::decode(B(0xa8,0xeb,0x10,0x64,0x9c,0xbf)))
            ->equals("no-cache");
    }

    public function test3(UnitTester $I)
    {
        $I->wantToTest("huffman encoding and decoding with many caes");
        $cases = [
            [
                BH("25a8 49e9 5ba9 7d7f"),
                "custom-key"
            ],
            [
                BH("25a8 49e9 5bb8 e8b4 bf"),
                "custom-value"
            ],
            [
                BH("6402"),
                "302"
            ],
            [
                BH("aec3 771a 4b"),
                "private"
            ],
            [
                BH(
                    "d07a be94 1054 d444 a820 0595 040b 8166" .
                    "e082 a62d 1bff"
                ),
                "Mon, 21 Oct 2013 20:13:21 GMT"
            ],
            [
                BH(
                    "9d29 ad17 1863 c78f 0b97 c8e9 ae82 ae43" .
                    "d3"
                ),
                "https://www.example.com"
            ],
            [
                BH("640e ff"),
                "307"
            ],
            [
                BH(
                    "d07a be94 1054 d444 a820 0595 040b 8166" .
                    "e084 a62d 1bff"
                ),
                "Mon, 21 Oct 2013 20:13:22 GMT"
            ],
            [
                BH("9bd9 ab"),
                "gzip"
            ],
            [
                BH(
                    "94e7 821d d7f2 e6c7 b335 dfdf cd5b 3960" .
                    "d5af 2708 7f36 72c1 ab27 0fb5 291f 9587" .
                    "3160 65c0 03ed 4ee5 b106 3d50 07"
                ),
                "foo=ASDJKHQKBZXOQWEOPIUAXQWEOIU; max-age=3600; version=1"
            ],
            [
                BH("f1e3 c2e5 f23a 6ba0 ab90 f4ff"),
                "www.example.com"
            ],
            [
                BH("a8eb 1064 9cbf"),
                "no-cache"
            ],
        ];

        foreach ($cases as $case) {
            list($encoded, $decoded) = $case;

            verify(Huffman::encode($decoded))
                ->equals($encoded);

            verify(Huffman::decode($encoded))
                ->equals($decoded);

        }
    }
}
