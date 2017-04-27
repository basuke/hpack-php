<?php


use HTTP2\HPACK\DynamicTable;

class Section4Cest
{
    public function tableSizeCalculation(UnitTester $I)
    {
        $I->wantToTest("table size calculation");
        // [4.1]

        verify(DynamicTable::entrySize(['hello', 'world']))
            ->equals(5 + 5 + 32);

        verify(DynamicTable::entrySize('hello world'))
            ->equals(11 + 32);

        $table = DynamicTable::table([
            ['hello', 'world'],
            ['help', 'wanted?']
        ]);
        verify($table->size())
            ->equals((5 + 5 + 32) + (4 + 7 + 32));
    }

    public function entryEviction(UnitTester $I)
    {
        $I->wantToTest("Entry Eviction When Dynamic Table Size Changes");
        // [4.3]

        $table = DynamicTable::table([
            ['hello', 'world'],
            ['help', 'wanted?']
        ]);
        verify($table->size())
            ->greaterThan(50);

        $table->setMaxSize(50);

        verify($table->length())
            ->equals(1);
        verify($table->get(61 + 1))
            ->equals(['hello', 'world']);

        $table->setMaxSize(0);
        verify($table->length())
            ->equals(0);
    }

    public function entryEviction2(UnitTester $I)
    {
        $I->wantToTest("Entry Eviction When Adding New Entries");
        // [4.4]

        $table = DynamicTable::table([
            ['hello', 'world'],
        ]);
        $table->setMaxSize(50);

        verify($table->length())
            ->equals(1);

        $index = $table->add('help', 'wanted???');

        verify($table->length())
            ->equals(1);
        verify($table->get($index))
            ->equals(['help', 'wanted???']);
    }
}
