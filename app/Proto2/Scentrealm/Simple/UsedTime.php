<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-11 10:35:21
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * UsedTime message
 */
class UsedTime extends \ProtobufMessage
{
    /* Field index constants */
    const BOTTLE = 1;
    const TIME = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BOTTLE => array(
            'name' => 'bottle',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::TIME => array(
            'name' => 'time',
            'repeated' => true,
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
        $this->values[self::BOTTLE] = null;
        $this->values[self::TIME] = array();
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
     * Sets value of 'bottle' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setBottle($value)
    {
        return $this->set(self::BOTTLE, $value);
    }

    /**
     * Returns value of 'bottle' property
     *
     * @return string
     */
    public function getBottle()
    {
        $value = $this->get(self::BOTTLE);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Appends value to 'time' list
     *
     * @param integer $value Value to append
     *
     * @return null
     */
    public function appendTime($value)
    {
        return $this->append(self::TIME, $value);
    }

    /**
     * Clears 'time' list
     *
     * @return null
     */
    public function clearTime()
    {
        return $this->clear(self::TIME);
    }

    /**
     * Returns 'time' list
     *
     * @return integer[]
     */
    public function getTime()
    {
        return $this->get(self::TIME);
    }

    /**
     * Returns 'time' iterator
     *
     * @return \ArrayIterator
     */
    public function getTimeIterator()
    {
        return new \ArrayIterator($this->get(self::TIME));
    }

    /**
     * Returns element from 'time' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return integer
     */
    public function getTimeAt($offset)
    {
        return $this->get(self::TIME, $offset);
    }

    /**
     * Returns count of 'time' list
     *
     * @return int
     */
    public function getTimeCount()
    {
        return $this->count(self::TIME);
    }
}
}