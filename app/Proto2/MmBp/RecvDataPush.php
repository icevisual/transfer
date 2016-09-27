<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * RecvDataPush message
 */
class RecvDataPush extends \ProtobufMessage
{
    /* Field index constants */
    const BASEPUSH = 1;
    const DATA = 2;
    const TYPE = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASEPUSH => array(
            'name' => 'BasePush',
            'required' => true,
            'type' => '\MmBp\BasePush'
        ),
        self::DATA => array(
            'name' => 'Data',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::TYPE => array(
            'name' => 'Type',
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
        $this->values[self::BASEPUSH] = null;
        $this->values[self::DATA] = null;
        $this->values[self::TYPE] = null;
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
     * Sets value of 'BasePush' property
     *
     * @param \MmBp\BasePush $value Property value
     *
     * @return null
     */
    public function setBasePush(\MmBp\BasePush $value=null)
    {
        return $this->set(self::BASEPUSH, $value);
    }

    /**
     * Returns value of 'BasePush' property
     *
     * @return \MmBp\BasePush
     */
    public function getBasePush()
    {
        return $this->get(self::BASEPUSH);
    }

    /**
     * Sets value of 'Data' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setData($value)
    {
        return $this->set(self::DATA, $value);
    }

    /**
     * Returns value of 'Data' property
     *
     * @return string
     */
    public function getData()
    {
        $value = $this->get(self::DATA);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'Type' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setType($value)
    {
        return $this->set(self::TYPE, $value);
    }

    /**
     * Returns value of 'Type' property
     *
     * @return integer
     */
    public function getType()
    {
        $value = $this->get(self::TYPE);
        return $value === null ? (integer)$value : $value;
    }
}
}