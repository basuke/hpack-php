<?php

namespace HTTP2\HPACK;

use UnitTester;

class Section5_1Cest
{
    public function encode1(UnitTester $I)
    {
        $encoded = Representation::encodeInt(10, 5);
        $I->assertEquals(B(0b00001010), $encoded);

        $encoded = Representation::encodeInt(1337, 5);
        $I->assertEquals(B(0b00011111, 0b10011010, 0b00001010), $encoded);

        $encoded = Representation::encodeInt(42, 8);
        $I->assertEquals(B(0b00101010), $encoded);
    }

    public function decode1(UnitTester $I)
    {
        $s = BS(0b00001010);
        $encoded = Representation::decodeInt($s, 5);
        $I->assertEquals(10, $encoded);
        $I->assertTrue($s->isEmpty());

        $encoded = Representation::decodeInt(BS(0b00011111, 0b10011010, 0b00001010), 5);
        $I->assertEquals(1337, $encoded);

        $encoded = Representation::decodeInt(BS(0b00011111, 0b10011010), 5);
        $I->assertEquals(false, $encoded);

        $encoded = Representation::decodeInt(BS(0b00101010), 8);
        $I->assertEquals(42, $encoded);
    }
}
