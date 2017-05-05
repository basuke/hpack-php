<?php


class AppendixC5Cest
{
    public function test1(UnitTester $I)
    {
        $I->wantToTest("[C.5] Response Examples without Huffman Coding");

        $encoder = new \HTTP2\HPACK\MockEncoder();
        $decoder = new \HTTP2\HPACK\MockDecoder();

        $encoder->setTableMaxSize(256);
        $decoder->setTableMaxSize(256);

        // [C.5.1] First Request

        $encoded = $encoder->encode([
            [':status', '302'],
            ['cache-control', 'private'],
            ['date', 'Mon, 21 Oct 2013 20:13:21 GMT'],
            ['location', 'https://www.example.com'],
        ]);

        verify($encoded)->equals(BH("" .
            "4803 3330 3258 0770 7269 7661 7465 611d | H.302X.privatea.",
            "4d6f 6e2c 2032 3120 4f63 7420 3230 3133 | Mon, 21 Oct 2013",
            "2032 303a 3133 3a32 3120 474d 546e 1768 |  20:13:21 GMTn.h",
            "7474 7073 3a2f 2f77 7777 2e65 7861 6d70 | ttps://www.examp",
            "6c65 2e63 6f6d                          | le.com"));

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

        // [C.5.2] Second Request

        $encoded = $encoder->encode([
            [':status', '307'],
            ['cache-control', 'private'],
            ['date', 'Mon, 21 Oct 2013 20:13:21 GMT'],
            ['location', 'https://www.example.com'],
        ]);

        verify($encoded)
            ->equals(BH(
                "4803 3330 37c1 c0bf                     | H.307..."));

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

        // [C.5.3] Third Request
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
                "88c1 611d 4d6f 6e2c 2032 3120 4f63 7420 | ..a.Mon, 21 Oct",
                "3230 3133 2032 303a 3133 3a32 3220 474d | 2013 20:13:22 GM",
                "54c0 5a04 677a 6970 7738 666f 6f3d 4153 | T.Z.gzipw8foo=AS",
                "444a 4b48 514b 425a 584f 5157 454f 5049 | DJKHQKBZXOQWEOPI",
                "5541 5851 5745 4f49 553b 206d 6178 2d61 | UAXQWEOIU; max-a",
                "6765 3d33 3630 303b 2076 6572 7369 6f6e | ge=3600; version",
                "3d31                                    | =1"
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
