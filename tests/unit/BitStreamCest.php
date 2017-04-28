<?php


use HTTP2\HPACK\Helper\BitWriter;

class BitStreamCest
{
    public function basicReader(UnitTester $I)
    {
        $I->wantToTest("basic BitWriter");

        verify((new BitWriter())
            ->write(0xff, 8)
            ->result())
            ->equals(B(0xff));

        verify((new BitWriter())
            ->write(0xf, 4)
            ->write(0x0, 4)
            ->result())
            ->equals(B(0xf0));

        verify((new BitWriter())
            ->write(0xf, 4)
            ->write(0x0, 2)
            ->write(0x8, 4)
            ->result())
            ->equals(B(0b11110010, 0b00111111));

        verify((new BitWriter())
            ->write(0x122, 9)
            ->result())
            ->equals(B(0x91, 0b01111111));
    }
}
