<?php

use HTTP2\HPACK\Helper\Binary;

$I = new UnitTester($scenario);
$I->wantTo('perform actions and see result');

$bin = new Binary();

$I->assertEquals(0, $bin->length());

$bin->append("Hello world");

$I->assertEquals("Hello world", $bin->peek());
$I->assertEquals("Hel", $bin->peek(3));
$I->assertEquals(false, $bin->peek(20));

$I->assertEquals("Hello ", $bin->read(6));
$I->assertEquals(5, $bin->length());

$I->assertEquals(false, $bin->read(6));
$I->assertEquals("wor", $bin->read(3));

$bin->append("!!!");
$I->assertEquals("ld!!!", $bin->read());

$I->assertTrue($bin->isEmpty());
