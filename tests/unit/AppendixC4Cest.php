<?php


class AppendixC4Cest
{
    public function test1(UnitTester $I)
    {
        $I->wantToTest("[C.4] Request Examples with Huffman Coding");

        $encoder = new \HTTP2\HPACK\MockEncoder();
        $decoder = new \HTTP2\HPACK\MockDecoder();

        $encoder->setHuffmanCoding(true);

        // [C.4.1] First Request

        $encoded = $encoder->encode([
            [':method', 'GET'],
            [':scheme', 'http'],
            [':path', '/'],
            [':authority', 'www.example.com'],
        ]);

        verify($encoded)
            ->equals(BH("8286 8441 8cf1 e3c2 e5f2 3a6b a0ab 90f4 ff"));

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

        // [C.4.2] Second Request

        $encoded = $encoder->encode([
            [':method', 'GET'],
            [':scheme', 'http'],
            [':path', '/'],
            [':authority', 'www.example.com'],
            ['cache-control', 'no-cache'],
        ]);

        verify($encoded)
            ->equals(BH("8286 84be 5886 a8eb 1064 9cbf"));

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

        // [C.4.3] Third Request

        $encoded = $encoder->encode([
            [':method', 'GET'],
            [':scheme', 'https'],
            [':path', '/index.html'],
            [':authority', 'www.example.com'],
            ['custom-key', 'custom-value'],
        ]);

        verify($encoded)
            ->equals(BH(
                "8287 85bf 4088 25a8 49e9 5ba9 7d7f 8925 | ....@.%.I.[.}..%",
                "a849 e95b b8e8 b4bf                     | .I.[...."
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
