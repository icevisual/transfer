<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * PlaySmell message
 */
class PlaySmell extends \ProtobufMessage
{
    /* Field index constants */
    const MODE = 1;
    const SMELL = 2;
    const START = 3;
    const DURATION = 4;
    const END = 5;
    const CIRCULATION = 6;
    const CYCLETIME = 7;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::MODE => array(
            'default' => \Proto2\Scentrealm\SrPlayMode::SOM_relative,
            'name' => 'mode',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::SMELL => array(
            'name' => 'smell',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::START => array(
            'name' => 'start',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::DURATION => array(
            'name' => 'duration',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::END => array(
            'name' => 'end',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
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
        $this->values[self::MODE] = self::$fields[self::MODE]['default'];
        $this->values[self::SMELL] = null;
        $this->values[self::START] = null;
        $this->values[self::DURATION] = null;
        $this->values[self::END] = null;
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
     * Sets value of 'smell' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSmell($value)
    {
        return $this->set(self::SMELL, $value);
    }

    /**
     * Returns value of 'smell' property
     *
     * @return string
     */
    public function getSmell()
    {
        $value = $this->get(self::SMELL);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'start' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setStart($value)
    {
        return $this->set(self::START, $value);
    }

    /**
     * Returns value of 'start' property
     *
     * @return integer
     */
    public function getStart()
    {
        $value = $this->get(self::START);
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
     * Sets value of 'end' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setEnd($value)
    {
        return $this->set(self::END, $value);
    }

    /**
     * Returns value of 'end' property
     *
     * @return string
     */
    public function getEnd()
    {
        $value = $this->get(self::END);
        return $value === null ? (string)$value : $value;
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