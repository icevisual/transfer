<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-11 09:45:02
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * PlayStartTime message
 */
class PlayStartTime extends \ProtobufMessage
{
    /* Field index constants */
    const MODE = 1;
    const STARTAT = 2;
    const CIRCULATION = 3;
    const CYCLETIME = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::MODE => array(
            'name' => 'mode',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::STARTAT => array(
            'name' => 'startAt',
            'repeated' => true,
            'type' => '\Proto2\Scentrealm\Simple\TimePoint'
        ),
        self::CIRCULATION => array(
            'name' => 'circulation',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::CYCLETIME => array(
            'name' => 'cycleTime',
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
        $this->values[self::STARTAT] = array();
        $this->values[self::CIRCULATION] = null;
        $this->values[self::CYCLETIME] = null;
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
     * Appends value to 'startAt' list
     *
     * @param \Proto2\Scentrealm\Simple\TimePoint $value Value to append
     *
     * @return null
     */
    public function appendStartAt(\Proto2\Scentrealm\Simple\TimePoint $value)
    {
        return $this->append(self::STARTAT, $value);
    }

    /**
     * Clears 'startAt' list
     *
     * @return null
     */
    public function clearStartAt()
    {
        return $this->clear(self::STARTAT);
    }

    /**
     * Returns 'startAt' list
     *
     * @return \Proto2\Scentrealm\Simple\TimePoint[]
     */
    public function getStartAt()
    {
        return $this->get(self::STARTAT);
    }

    /**
     * Returns 'startAt' iterator
     *
     * @return \ArrayIterator
     */
    public function getStartAtIterator()
    {
        return new \ArrayIterator($this->get(self::STARTAT));
    }

    /**
     * Returns element from 'startAt' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Proto2\Scentrealm\Simple\TimePoint
     */
    public function getStartAtAt($offset)
    {
        return $this->get(self::STARTAT, $offset);
    }

    /**
     * Returns count of 'startAt' list
     *
     * @return int
     */
    public function getStartAtCount()
    {
        return $this->count(self::STARTAT);
    }

    /**
     * Sets value of 'circulation' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setCirculation($value)
    {
        return $this->set(self::CIRCULATION, $value);
    }

    /**
     * Returns value of 'circulation' property
     *
     * @return string
     */
    public function getCirculation()
    {
        $value = $this->get(self::CIRCULATION);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'cycleTime' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setCycleTime($value)
    {
        return $this->set(self::CYCLETIME, $value);
    }

    /**
     * Returns value of 'cycleTime' property
     *
     * @return integer
     */
    public function getCycleTime()
    {
        $value = $this->get(self::CYCLETIME);
        return $value === null ? (integer)$value : $value;
    }
}
}