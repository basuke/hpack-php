<?php

namespace HTTP2\HPACK;

use UnitTester;

class Section5_1Cest
{
    public function encode1(UnitTester $I)
    {
        verify(Representation::encodeInt(10, 5))
            ->equals(B(0b00001010));

        verify(Representation::encodeInt(1337, 5))
            ->equals(B(0b00011111, 0b10011010, 0b00001010));

        verify(Representation::encodeInt(42, 8))
            ->equals(B(0b00101010));
    }

    public function decode1(UnitTester $I)
    {
        $s = BS(0b00001010);
        verify(Representation::decodeInt($s, 5))
            ->equals(10);

        verify_that($s->isEmpty());

        verify(Representation::decodeInt(BS(0b00011111, 0b10011010, 0b00001010), 5))
            ->equals(1337);

        verify(Representation::decodeInt(BS(0b00101010), 8))
            ->equals(42);

        $I->expectException(DecodeException::class, function () {
            Representation::decodeInt(BS(0b00011111, 0b10011010), 5);
        });
    }
}
