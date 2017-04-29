<?php


use HTTP2\HPACK\Representation as R;

class Section6Cest
{
    public function sec6_1(UnitTester $I)
    {
        $I->wantToTest("[6.1] Indexed Header Field Representation");

        verify(R::encodeHeaderField(R::INDEXED_HEADER_FIELD, 15))
            ->equals(B(0x80 | 15));

        verify(R::encodeHeaderField(R::INDEXED_HEADER_FIELD, 150))
            ->equals(B(0x80 | 127, (150 - 127)));

        verify(R::decodeHeaderField(BS(0x80 | 15)))
            ->equals([R::INDEXED_HEADER_FIELD, 15]);

        verify(R::decodeHeaderField(BS(0x80 | 127, (150 - 127))))
            ->equals([R::INDEXED_HEADER_FIELD, 150]);
    }

    public function sec6_2_1(UnitTester $I)
    {
        $I->wantToTest("[6.2.1] Literal Header Field with Incremental Indexing");

        verify(R::encodeHeaderField(
            R::LITERAL_INDEXING_HEADER_FIELD,
            [34, "5d6e"]))
            ->equals(B(0x40 | 34, 4, "5d6e"));

        verify(R::encodeHeaderField(
            R::LITERAL_INDEXING_HEADER_FIELD,
            ["foo", "bar"]))
            ->equals(B(0x40, 3, "foo", 3, "bar"));

        verify(R::decodeHeaderField(BS(0x40 | 34, 4, "5d6e")))
            ->equals([R::LITERAL_INDEXING_HEADER_FIELD, [34, "5d6e"]]);

        verify(R::decodeHeaderField(BS(0x40, 3, "foo", 3, "bar")))
            ->equals([R::LITERAL_INDEXING_HEADER_FIELD, ["foo", "bar"]]);
    }

    public function sec6_2_2(UnitTester $I)
    {
        $I->wantToTest("[6.2.2] Literal Header Field without Indexing");

        verify(R::encodeHeaderField(
            R::LITERAL_NO_INDEXING_HEADER_FIELD,
            [34, "5d6e"]))
            ->equals(B(0x00 | 0x0f, (34 - 0x0f), 4, "5d6e"));

        verify(R::encodeHeaderField(
            R::LITERAL_NO_INDEXING_HEADER_FIELD,
            ["foo", "bar"]))
            ->equals(B(0x00, 3, "foo", 3, "bar"));

        verify(R::decodeHeaderField(BS(0x00 | 0x0f, (34 - 0x0f), 4, "5d6e")))
            ->equals([R::LITERAL_NO_INDEXING_HEADER_FIELD, [34, "5d6e"]]);

        verify(R::decodeHeaderField(BS(0x00, 3, "foo", 3, "bar")))
            ->equals([R::LITERAL_NO_INDEXING_HEADER_FIELD, ["foo", "bar"]]);
    }

    public function sec6_2_3(UnitTester $I)
    {
        $I->wantToTest("[6.2.3] Literal Header Field Never Indexed");

        verify(R::encodeHeaderField(
            R::LITERAL_NEVER_INDEXING_HEADER_FIELD,
            [34, "5d6e"]))
            ->equals(B(0x10 | 0x0f, (34 - 0x0f), 4, "5d6e"));

        verify(R::encodeHeaderField(
            R::LITERAL_NEVER_INDEXING_HEADER_FIELD,
            ["foo", "bar"]))
            ->equals(B(0x10, 3, "foo", 3, "bar"));

        verify(R::decodeHeaderField(BS(0x10 | 0x0f, (34 - 0x0f), 4, "5d6e")))
            ->equals([R::LITERAL_NEVER_INDEXING_HEADER_FIELD, [34, "5d6e"]]);

        verify(R::decodeHeaderField(BS(0x10, 3, "foo", 3, "bar")))
            ->equals([R::LITERAL_NEVER_INDEXING_HEADER_FIELD, ["foo", "bar"]]);
    }

    public function sec6_3(UnitTester $I)
    {
        $I->wantToTest("[6.3] Dynamic Table Size Update");

        verify(R::encodeHeaderField(R::TABLE_SIZE_UPDATE, 15))
            ->equals(B(0x20 | 15));

        verify(R::encodeHeaderField(R::TABLE_SIZE_UPDATE, 150))
            ->equals(B(0x20 | 0x1f, (150 - 0x1f)));

        verify(R::decodeHeaderField(BS(0x20 | 15)))
            ->equals([R::TABLE_SIZE_UPDATE, 15]);

        verify(R::decodeHeaderField(BS(0x20 | 0x1f, (150 - 0x1f))))
            ->equals([R::TABLE_SIZE_UPDATE, 150]);
    }
}
