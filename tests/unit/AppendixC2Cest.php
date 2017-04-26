<?php

namespace HTTP2\HPACK;

use UnitTester;

class AppendixC2Cest
{
    public function c21(UnitTester $I)
    {
        $I->wantTo('[C.2.1] Literal Header Field with Indexing');

        $encoded = Representation::encodeInt(10, 5);
        $I->assertEquals(B(0b00001010), $encoded);
    }

    public function example2(UnitTester $I)
    {
        $I->wantTo('[C.1.2] Example 2: Encoding 1337 Using a 5-Bit Prefix');

        $encoded = Representation::encodeInt(1337, 5);
        $I->assertEquals(B(0b00011111, 0b10011010, 0b00001010), $encoded);
    }

    public function example3(UnitTester $I)
    {
        $I->wantTo('[C.1.3] Example 3: Encoding 42 Starting at an Octet Boundary');

        $encoded = Representation::encodeInt(42, 8);
        $I->assertEquals(B(0b00101010), $encoded);
    }
}
