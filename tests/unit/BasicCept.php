<?php

use HTTP2\HPACK\DecodeException;
use HTTP2\HPACK\Representation;

$I = new UnitTester($scenario);

$encoder = new \HTTP2\HPACK\Encoder();
$decoder = new \HTTP2\HPACK\Decoder();

// =============================================
// [2.3] Indexing Tables

$staticTable = \HTTP2\HPACK\Table::staticTable();

$I->assertEquals(
    [':status', '200'],
    $staticTable->get(8)
);

$I->assertEquals(
    'host',
    $staticTable->get(38)
);

// [2.3.3]
$I->expectException(DecodeException::class, function () use ($staticTable) {
    $staticTable->get(0);
});

$I->expectException(DecodeException::class, function () use ($staticTable) {
    $staticTable->get(62);
});

// =============================================

$table = new \HTTP2\HPACK\DynamicTable();

$I->assertEquals(
    [':status', '200'],
    $table->get(8)
);

$I->expectException(DecodeException::class, function () use ($table) {
    $table->get(0);
});

$I->expectException(DecodeException::class, function () use ($table) {
    $table->get(62);
});

