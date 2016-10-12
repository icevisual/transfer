<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-12 14:22:47
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
    const DURATION = 2;
    const POWER = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BOTTLE => array(
            'name' => 'bottle',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
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
        $this->values[self::DURATION] = null;
        $this->values[self::POWER] = null;
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
}
}