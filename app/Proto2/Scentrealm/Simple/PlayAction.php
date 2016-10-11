<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-11 09:45:02
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * PlayAction message
 */
class PlayAction extends \ProtobufMessage
{
    /* Field index constants */
    const BOTTLE = 1;
    const BEFORESTART = 2;
    const DURATION = 3;
    const POWER = 4;
    const CIRCULATION = 5;
    const INTERVAL = 6;
    const CYCLETIME = 7;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BOTTLE => array(
            'name' => 'bottle',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::BEFORESTART => array(
            'name' => 'beforeStart',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::DURATION => array(
            'name' => 'duration',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::POWER => array(
            'name' => 'power',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::CIRCULATION => array(
            'name' => 'circulation',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
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
        $this->values[self::BOTTLE] = null;
        $this->values[self::BEFORESTART] = null;
        $this->values[self::DURATION] = null;
        $this->values[self::POWER] = null;
        $this->values[self::CIRCULATION] = null;
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
     * Sets value of 'duration' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setDuration($value)
    {
        return $this->set(self::DURATION, $value);
    }

    /**
     * Returns value of 'duration' property
     *
     * @return integer
     */
    public function getDuration()
    {
        $value = $this->get(self::DURATION);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'power' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setPower($value)
    {
        return $this->set(self::POWER, $value);
    }

    /**
     * Returns value of 'power' property
     *
     * @return integer
     */
    public function getPower()
    {
        $value = $this->get(self::POWER);
        return $value === null ? (integer)$value : $value;
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