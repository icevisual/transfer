<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-12 14:22:47
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * BaseResponse message
 */
class BaseResponse extends \ProtobufMessage
{
    /* Field index constants */
    const CODE = 1;
    const DATA = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::CODE => array(
            'name' => 'code',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::DATA => array(
            'name' => 'data',
            'repeated' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
    );

    /**
     * Constructs new message container and clears its internal state
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Clears message values and sets default ones
     *
     * @return null
     */
    public function reset()
    {
        $this->values[self::CODE] = null;
        $this->values[self::DATA] = array();
    }

    /**
     * Returns field descriptors
     *
     * @return array
     */
    public function fields()
    {
        return self::$fields;
    }

    /**
     * Sets value of 'code' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setCode($value)
    {
        return $this->set(self::CODE, $value);
    }

    /**
     * Returns value of 'code' property
     *
     * @return integer
     */
    public function getCode()
    {
        $value = $this->get(self::CODE);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Appends value to 'data' list
     *
     * @param string $value Value to append
     *
     * @return null
     */
    public function appendData($value)
    {
        return $this->append(self::DATA, $value);
    }

    /**
     * Clears 'data' list
     *
     * @return null
     */
    public function clearData()
    {
        return $this->clear(self::DATA);
    }

    /**
     * Returns 'data' list
     *
     * @return string[]
     */
    public function getData()
    {
        return $this->get(self::DATA);
    }

    /**
     * Returns 'data' iterator
     *
     * @return \ArrayIterator
     */
    public function getDataIterator()
    {
        return new \ArrayIterator($this->get(self::DATA));
    }

    /**
     * Returns element from 'data' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return string
     */
    public function getDataAt($offset)
    {
        return $this->get(self::DATA, $offset);
    }

    /**
     * Returns count of 'data' list
     *
     * @return int
     */
    public function getDataCount()
    {
        return $this->count(self::DATA);
    }
}
}