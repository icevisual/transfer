<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-12 15:41:05
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * PlayTrace message
 */
class PlayTrace extends \ProtobufMessage
{
    /* Field index constants */
    const ACTIONID = 1;
    const BEFORESTART = 2;
    const CYCLEMODE = 3;
    const INTERVAL = 4;
    const CYCLETIME = 5;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::ACTIONID => array(
            'name' => 'actionId',
            'repeated' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::BEFORESTART => array(
            'name' => 'beforeStart',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::CYCLEMODE => array(
            'name' => 'cycleMode',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::INTERVAL => array(
            'name' => 'interval',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
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
        $this->values[self::ACTIONID] = array();
        $this->values[self::BEFORESTART] = null;
        $this->values[self::CYCLEMODE] = null;
        $this->values[self::INTERVAL] = null;
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
     * Appends value to 'actionId' list
     *
     * @param integer $value Value to append
     *
     * @return null
     */
    public function appendActionId($value)
    {
        return $this->append(self::ACTIONID, $value);
    }

    /**
     * Clears 'actionId' list
     *
     * @return null
     */
    public function clearActionId()
    {
        return $this->clear(self::ACTIONID);
    }

    /**
     * Returns 'actionId' list
     *
     * @return integer[]
     */
    public function getActionId()
    {
        return $this->get(self::ACTIONID);
    }

    /**
     * Returns 'actionId' iterator
     *
     * @return \ArrayIterator
     */
    public function getActionIdIterator()
    {
        return new \ArrayIterator($this->get(self::ACTIONID));
    }

    /**
     * Returns element from 'actionId' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return integer
     */
    public function getActionIdAt($offset)
    {
        return $this->get(self::ACTIONID, $offset);
    }

    /**
     * Returns count of 'actionId' list
     *
     * @return int
     */
    public function getActionIdCount()
    {
        return $this->count(self::ACTIONID);
    }

    /**
     * Sets value of 'beforeStart' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setBeforeStart($value)
    {
        return $this->set(self::BEFORESTART, $value);
    }

    /**
     * Returns value of 'beforeStart' property
     *
     * @return integer
     */
    public function getBeforeStart()
    {
        $value = $this->get(self::BEFORESTART);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'cycleMode' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setCycleMode($value)
    {
        return $this->set(self::CYCLEMODE, $value);
    }

    /**
     * Returns value of 'cycleMode' property
     *
     * @return integer
     */
    public function getCycleMode()
    {
        $value = $this->get(self::CYCLEMODE);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'interval' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setInterval($value)
    {
        return $this->set(self::INTERVAL, $value);
    }

    /**
     * Returns value of 'interval' property
     *
     * @return integer
     */
    public function getInterval()
    {
        $value = $this->get(self::INTERVAL);
        return $value === null ? (integer)$value : $value;
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