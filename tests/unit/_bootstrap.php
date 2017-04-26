<?php

use HTTP2\HPACK\Helper\Binary;

function B(... $args)
{
    $data = [];

    foreach ($args as $arg) {
        $data[] = binaryRepresentation($arg);
    }

    return implode('', $data);
}

function BS(... $args)
{
    return new Binary(B(... $args));
}

function binaryRepresentation($arg)
{
    if (is_string($arg)) {
        return $arg;
    } elseif (is_array($arg)) {
        return B(... $arg);
    } elseif (is_int($arg)) {
        return pack('c', $arg);
    } else {
        "BADDATA";
    }
}

