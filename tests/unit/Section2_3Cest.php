<?php

use HTTP2\HPACK\DecodeException;
use HTTP2\HPACK\DynamicTable;
use HTTP2\HPACK\Table;

class Section2_3Cest
{
    public function basicStaticTable(UnitTester $I)
    {
        // [2.3.1] [2.3.3] [Appendix A.]
        $I->wantToTest("basic static table interface");

        $table = Table::staticTable();

        verify($table->length())
            ->equals(61);

        verify($table->get(11))
            ->equals([':status', '304']);

        $I->expectException(
            DecodeException::class,
            function () use ($table) {
                $table->get(0);
            });

        $I->expectException(
            DecodeException::class,
            function () use ($table) {
                $table->get(62);
            });

        verify($table->find('accept-charset'))
            ->equals(15);

        verify($table->find(':status', '200'))
            ->equals(8);

        verify($table->find(':status'))
            ->false();

        verify($table->find(':status', '1024'))
            ->false();
    }

    public function basicDynamicTable(UnitTester $I)
    {
        // [2.3.2] [2.3.3]
        $I->wantToTest("basic dynamic table interface");

        $table = DynamicTable::table();

        verify($table->length())
            ->equals(0);

        verify($table->maxSize())
            ->equals(DynamicTable::DEFAULT_MAX_SIZE);

        verify($table->find('accept-charset'))
            ->equals(15);

        verify($table->find('hello', 'world'))
            ->false();

        verify($table->add('hello', 'world'))
            ->equals(62);

        verify($table->find('hello', 'world'))
            ->equals(62);

        verify($table->find('hello'))
            ->false();

        verify($table->get(62))
            ->equals(['hello', 'world']);
    }

    public function basicDynamicTable2(UnitTester $I)
    {
        // [2.3.2]
        $I->wantToTest("dupulicated entries in dynamic table");

        $table = DynamicTable::table();

        $table->add('hello', 'world');
        $table->add('hello', 'world again');
        $table->add('hello', 'world');

        verify($table->find('hello', 'world'))
            ->equals(62);

        verify($table->get(62))
            ->equals(['hello', 'world']);

        verify($table->get(64))
            ->equals(['hello', 'world']);
    }
}
