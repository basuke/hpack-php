<?php

namespace HTTP2\HPACK;

use UnitTester;

class AppendixC2Cest
{
    public function test1(UnitTester $I)
    {
        $I->wantToTest('[C.2.1] Literal Header Field with Indexing');

        $headerList = [
            ['custom-key', 'custom-header']
        ];

        $encoder = new Encoder();

        verify($encoder->encode($headerList))
            ->equals(B(
                0x40,0x0a,0x63,0x75,0x73,0x74,0x6f,0x6d,0x2d,0x6b,0x65,0x79 ,0x0d,0x63,0x75,0x73,
                0x74,0x6f,0x6d,0x2d,0x68,0x65,0x61,0x64,0x65,0x72
            ));
    }
}