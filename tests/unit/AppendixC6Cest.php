<?php


class AppendixC6Cest
{
    public function test1(UnitTester $I)
    {
        $I->wantToTest("[C.6] Response Examples without Huffman Coding");

        $encoder = new \HTTP2\HPACK\MockEncoder();
        $decoder = new \HTTP2\HPACK\MockDecoder();

        $encoder->setHuffmanCoding(true);
        $encoder->setTableMaxSize(256);
        $decoder->setTableMaxSize(256);

        // [C.6.1] First Request

        $encoded = $encoder->encode([
            [':status', '302'],
            ['cache-control', 'private'],
            ['date', 'Mon, 21 Oct 2013 20:13:21 GMT'],
            ['location', 'https://www.example.com'],
        ]);

        verify($encoded)->equals(BH("" .
            "4882 6402 5885 aec3 771a 4b61 96d0 7abe | H.d.X...w.Ka..z.",
            "9410 54d4 44a8 2005 9504 0b81 66e0 82a6 | ..T.D. .....f...",
            "2d1b ff6e 919d 29ad 1718 63c7 8f0b 97c8 | -..n..)...c.....",
            "e9ae 82ae 43d3                          | ....C."
        ));

        $decoded = $decoder->decode($encoded);

        verify($decoded)->equals([
            [':status', '302'],
            ['cache-control', 'private'],
            ['date', 'Mon, 21 Oct 2013 20:13:21 GMT'],
            ['location', 'https://www.example.com'],
        ]);

        verify($decoder->getTable()->get(62))
            ->equals(['location', 'https://www.example.com']);

        verify($decoder->getTable()->get(63))
            ->equals(['date', 'Mon, 21 Oct 2013 20:13:21 GMT']);

        verify($decoder->getTable()->get(64))
            ->equals(['cache-control', 'private']);

        verify($decoder->getTable()->get(65))
            ->equals([':status', '302']);

        verify($decoder->getTable()->size())
            ->equals(222);

        // [C.6.2] Second Request

        $encoded = $encoder->encode([
            [':status', '307'],
            ['cache-control', 'private'],
            ['date', 'Mon, 21 Oct 2013 20:13:21 GMT'],
            ['location', 'https://www.example.com'],
        ]);

        verify($encoded)
            ->equals(BH(
                "4883 640e ffc1 c0bf                     | H.d....."));

        $decoded = $decoder->decode($encoded);

        verify($decoded)->equals([
            [':status', '307'],
            ['cache-control', 'private'],
            ['date', 'Mon, 21 Oct 2013 20:13:21 GMT'],
            ['location', 'https://www.example.com'],
        ]);

        verify($decoder->getTable()->get(62))
            ->equals([':status', '307']);

        verify($decoder->getTable()->get(63))
            ->equals(['location', 'https://www.example.com']);

        verify($decoder->getTable()->get(64))
            ->equals(['date', 'Mon, 21 Oct 2013 20:13:21 GMT']);

        verify($decoder->getTable()->get(65))
            ->equals(['cache-control', 'private']);

        verify($decoder->getTable()->size())
            ->equals(222);

        verify($decoder->getTable()->length())
            ->equals(4);

        // [C.6.3] Third Request
        /*
           :status: 200
           cache-control: private
           date: Mon, 21 Oct 2013 20:13:22 GMT
           location: https://www.example.com
           content-encoding: gzip
           set-cookie: foo=ASDJKHQKBZXOQWEOPIUAXQWEOIU; max-age=3600; version=1

         */
        $encoded = $encoder->encode([
            [':status', '200'],
            ['cache-control', 'private'],
            ['date', 'Mon, 21 Oct 2013 20:13:22 GMT'],
            ['location', 'https://www.example.com'],
            ['content-encoding', 'gzip'],
            ['set-cookie', 'foo=ASDJKHQKBZXOQWEOPIUAXQWEOIU; max-age=3600; version=1'],
        ]);

        verify($encoded)
            ->equals(BH(
                "88c1 6196 d07a be94 1054 d444 a820 0595 | ..a..z...T.D. ..",
                "040b 8166 e084 a62d 1bff c05a 839b d9ab | ...f...-...Z....",
                "77ad 94e7 821d d7f2 e6c7 b335 dfdf cd5b | w..........5...[",
                "3960 d5af 2708 7f36 72c1 ab27 0fb5 291f | 9`..'..6r..'..).",
                "9587 3160 65c0 03ed 4ee5 b106 3d50 07   | ..1`e...N...=P."
            ));

        $decoded = $decoder->decode($encoded);

        verify($decoded)->equals([
            [':status', '200'],
            ['cache-control', 'private'],
            ['date', 'Mon, 21 Oct 2013 20:13:22 GMT'],
            ['location', 'https://www.example.com'],
            ['content-encoding', 'gzip'],
            ['set-cookie', 'foo=ASDJKHQKBZXOQWEOPIUAXQWEOIU; max-age=3600; version=1'],
        ]);

        verify($decoder->getTable()->get(62))
            ->equals(['set-cookie', 'foo=ASDJKHQKBZXOQWEOPIUAXQWEOIU; max-age=3600; version=1']);

        verify($decoder->getTable()->get(63))
            ->equals(['content-encoding', 'gzip']);

        verify($decoder->getTable()->get(64))
            ->equals(['date', 'Mon, 21 Oct 2013 20:13:22 GMT']);

        verify($decoder->getTable()->size())
            ->equals(215);
    }
}
