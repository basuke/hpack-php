<?php


use HTTP2\HPACK\Helper\BitReader;
use HTTP2\HPACK\Helper\BitWriter;

class BitStreamCest
{
    public function basicWriter(UnitTester $I)
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

    public function basicReader(UnitTester $I)
    {
        $I->wantToTest("basic BitReader");

        verify((new BitReader(B(0b01011101)))
            ->read(1))
            ->equals(0);

        verify((new BitReader(B(0b01011101)))
            ->read(4))
            ->equals(0b0101);

        verify((new BitReader(B(0b01011101)))
            ->read(8))
            ->equals(0b01011101);

        verify((new BitReader(B(0b01011101)))
            ->read(10))
            ->false();

        verify((new BitReader(B(0b01011101, 0b10101010)))
            ->read(10))
            ->equals(0b0101110110);

        $bs = new BitReader(B(0b01011101));

        verify($bs->read(1))
            ->equals(0);
        verify($bs->read(2))
            ->equals(0b10);
        verify($bs->read(3))
            ->equals(0b111);
        verify($bs->read(3))
            ->false();
        verify($bs->read(2))
            ->equals(1);
    }
}
