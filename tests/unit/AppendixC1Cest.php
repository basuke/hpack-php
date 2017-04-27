<?php

namespace HTTP2\HPACK;

use UnitTester;

class AppendixC1Cest
{
    public function example1(UnitTester $I)
    {
        $I->wantTo('[C.1.1] Literal Header Field with Indexing');

        verify(Representation::encodeInt(10, 5))
            ->equals(B(0b00001010));
    }

    public function example2(UnitTester $I)
    {
        $I->wantTo('[C.1.2] Example 2: Encoding 1337 Using a 5-Bit Prefix');

        verify(Representation::encodeInt(1337, 5))
            ->equals(B(0b00011111, 0b10011010, 0b00001010));
    }

    public function example3(UnitTester $I)
    {
        $I->wantTo('[C.1.3] Example 3: Encoding 42 Starting at an Octet Boundary');

        verify(Representation::encodeInt(42, 8))
            ->equals(B(0b00101010));
    }
}
