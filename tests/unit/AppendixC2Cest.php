<?php

namespace HTTP2\HPACK;

use UnitTester;
use HTTP2\HPACK\Representation as R;

class AppendixC2Cest
{
    public function test1(UnitTester $I)
    {
        $I->wantToTest('[C.2.1] Literal Header Field with Indexing');

        $headerList = [
            ['custom-key', 'custom-header']
        ];

        $encoder = new MockEncoder();

        verify($encoder->encode($headerList))
            ->equals(B(
                0x40,0x0a,
                'custom-key',
                0x0d,
                'custom-header'
            ));

        verify($encoder->getTable()->get(62))
            ->equals(['custom-key', 'custom-header']);

        verify($encoder->getTable()->size())
            ->equals(55);

        $decoder = new MockDecoder();

        verify($decoder->decode(B(
            0x40,0x0a,
            'custom-key',
            0x0d,
            'custom-header'
        )))
            ->equals([
                ['custom-key', 'custom-header']
            ]);

        verify($decoder->getTable()->get(62))
            ->equals(['custom-key', 'custom-header']);

        verify($decoder->getTable()->size())
            ->equals(55);
    }

    public function test2(UnitTester $I)
    {
        $I->wantToTest('[C.2.2] Literal Header Field without Indexing');

        $headerList = [
            [':path', '/sample/path', R::LITERAL_NO_INDEXING_HEADER_FIELD]
        ];

        $encoder = new MockEncoder();

        verify($encoder->encode($headerList))
            ->equals(B(
                0x04,0x0c,
                '/sample/path'
            ));

        verify($encoder->getTable()->size())
            ->equals(0);

        $decoder = new MockDecoder();

        verify($decoder->decode(B(
            0x04,0x0c,
            '/sample/path'
        )))
            ->equals([
                [
                    ':path',
                    '/sample/path',
                    R::LITERAL_NO_INDEXING_HEADER_FIELD
                ]
            ]);

        verify($decoder->getTable()->size())
            ->equals(0);
    }

    public function test3(UnitTester $I)
    {
        $I->wantToTest('[C.2.3] Literal Header Field Never Indexed');

        $headerList = [
            ['password', 'secret', R::LITERAL_NEVER_INDEXING_HEADER_FIELD]
        ];

        $encoder = new MockEncoder();

        verify($encoder->encode($headerList))
            ->equals(B(
                0x10,0x08,
                'password',
                0x06,
                'secret'
            ));

        verify($encoder->getTable()->size())
            ->equals(0);

        $decoder = new MockDecoder();

        verify($decoder->decode(B(
            0x10,0x08,
            'password',
            0x06,
            'secret'
        )))
            ->equals([
                ['password', 'secret', R::LITERAL_NEVER_INDEXING_HEADER_FIELD]
            ]);

        verify($decoder->getTable()->size())
            ->equals(0);
    }

    public function test4(UnitTester $I)
    {
        $I->wantToTest('[C.2.4] Indexed Header Field');

        $headerList = [
            [':method', 'GET']
        ];

        $encoder = new MockEncoder();

        verify($encoder->encode($headerList))
            ->equals(B(
                0x82
            ));

        verify($encoder->getTable()->size())
            ->equals(0);

        $decoder = new MockDecoder();

        verify($decoder->decode(B(
            0x82
        )))
            ->equals([
                [':method', 'GET']
            ]);

        verify($decoder->getTable()->size())
            ->equals(0);
    }
}