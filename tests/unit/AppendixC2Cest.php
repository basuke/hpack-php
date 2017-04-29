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

//        $encoder = new Encoder();

//        verify($encoder->encode($headerList))
//            ->equals(B(
//                0x40,0x0a,
//                'custom-key',
//                    0x0d,
//                'custom-header'
//            ));
    }
}