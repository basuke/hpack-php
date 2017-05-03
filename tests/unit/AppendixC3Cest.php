<?php

use HTTP2\HPACK\Representation as R;

class AppendixC3Cest
{
    public function test1(UnitTester $I)
    {
        $I->wantToTest("[C.3] Request Examples without Huffman Codeing");

        $encoder = new \HTTP2\HPACK\MockEncoder();
        $decoder = new \HTTP2\HPACK\MockDecoder();

        // [C.3.1] First Request

        $encoded = $encoder->encode([
            [':method', 'GET'],
            [':scheme', 'http'],
            [':path', '/'],
            [':authority', 'www.example.com'],
        ]);

        verify($encoded)
            ->equals(B(
                0x82, 0x86, 0x84, 0x41,
                    0x0f, "www.example.com"
            ));

        $decoded = $decoder->decode($encoded);

        verify($decoded)
            ->equals([
                [':method', 'GET'],
                [':scheme', 'http'],
                [':path', '/'],
                [':authority', 'www.example.com'],
            ]);

        verify($decoder->getTable()->get(62))
            ->equals([':authority', 'www.example.com']);

        verify($decoder->getTable()->size())
            ->equals(57);

        // [C.3.2] Second Request

        $encoded = $encoder->encode([
            [':method', 'GET'],
            [':scheme', 'http'],
            [':path', '/'],
            [':authority', 'www.example.com'],
            ['cache-control', 'no-cache'],
        ]);

        verify($encoded)
            ->equals(B(
                0x82, 0x86, 0x84, 0xbe,
                0x58, 0x08, "no-cache"
            ));

        $decoded = $decoder->decode($encoded);

        verify($decoded)
            ->equals([
                [':method', 'GET'],
                [':scheme', 'http'],
                [':path', '/'],
                [':authority', 'www.example.com'],
                ['cache-control', 'no-cache'],
            ]);

        verify($decoder->getTable()->get(62))
            ->equals(['cache-control', 'no-cache']);

        verify($decoder->getTable()->get(63))
            ->equals([':authority', 'www.example.com']);

        verify($decoder->getTable()->size())
            ->equals(110);

        // [C.3.3] Third Request

        $encoded = $encoder->encode([
            [':method', 'GET'],
            [':scheme', 'https'],
            [':path', '/index.html'],
            [':authority', 'www.example.com'],
            ['custom-key', 'custom-value'],
        ]);

        verify($encoded)
            ->equals(B(
                0x82, 0x87, 0x85, 0xbf, 0x40, 0x0a,
                "custom-key", 0x0c, "custom-value"
            ));

        $decoded = $decoder->decode($encoded);

        verify($decoded)
            ->equals([
                [':method', 'GET'],
                [':scheme', 'https'],
                [':path', '/index.html'],
                [':authority', 'www.example.com'],
                ['custom-key', 'custom-value'],
            ]);

        verify($decoder->getTable()->get(62))
            ->equals(['custom-key', 'custom-value']);

        verify($decoder->getTable()->get(63))
            ->equals(['cache-control', 'no-cache']);

        verify($decoder->getTable()->get(64))
            ->equals([':authority', 'www.example.com']);

        verify($decoder->getTable()->size())
            ->equals(164);
    }
}
