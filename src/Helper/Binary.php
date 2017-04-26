<?php

namespace HTTP2\HPACK\Helper;

final class Binary
{
    /** @var string */
    private $string = '';

    /** @var int */
    private $length = 0;

    /**
     * @param string $string
     */
    public function __construct($string = '')
    {
        $this->append($string);
    }

    /**
     * @return int
     */
    public function length()
    {
        return strlen($this->string);
    }

    /**
     * @param string $string
     */
    public function append($string)
    {
        $this->string .= $string;
        $this->length += strlen($string);
    }

    /**
     * @param int $count
     */
    public function peek($count = null)
    {
        if (is_null($count)) {
            return $this->string;
        }

        assert($count > 0);

        if ($this->length >= $count) {
            return substr($this->string, 0, $count);
        } else {
            return false;
        }
    }

    public function read($count = null)
    {
        if (is_null($count)) {
            $count = $this->length;
        }

        assert($count > 0);

        if ($this->length >= $count) {
            $result = substr($this->string, 0, $count);
            $this->string = substr($this->string, $count);
            $this->length -= $count;
            return $result;
        } else {
            return false;
        }
    }

    public function isEmpty()
    {
        return $this->length === 0;
    }

    public function readUInt8()
    {
        $value = $this->read(1);
        if ($value !== false) {
            $value = unpack('C', $value)[1];
        }

        return $value;
    }
}