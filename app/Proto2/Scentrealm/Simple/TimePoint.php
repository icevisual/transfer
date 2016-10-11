<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-11 10:35:21
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * TimePoint message
 */
class TimePoint extends \ProtobufMessage
{
    /* Field index constants */
    const MODE = 1;
    const VALUE = 2;
    const ENDVALUE = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::MODE => array(
            'name' => 'mode',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::VALUE => array(
            'name' => 'value',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::ENDVALUE => array(
            'name' => 'endValue',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
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
        $this->values[self::MODE] = null;
        $this->values[self::VALUE] = null;
        $this->values[self::ENDVALUE] = null;
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
     * Sets value of 'mode' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setMode($value)
    {
        return $this->set(self::MODE, $value);
    }

    /**
     * Returns value of 'mode' property
     *
     * @return integer
     */
    public function getMode()
    {
        $value = $this->get(self::MODE);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'value' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setValue($value)
    {
        return $this->set(self::VALUE, $value);
    }

    /**
     * Returns value of 'value' property
     *
     * @return integer
     */
    public function getValue()
    {
        $value = $this->get(self::VALUE);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'endValue' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setEndValue($value)
    {
        return $this->set(self::ENDVALUE, $value);
    }

    /**
     * Returns value of 'endValue' property
     *
     * @return integer
     */
    public function getEndValue()
    {
        $value = $this->get(self::ENDVALUE);
        return $value === null ? (integer)$value : $value;
    }
}
}