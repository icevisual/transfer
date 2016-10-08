<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * RecvDataPush message
 */
class RecvDataPush extends \ProtobufMessage
{
    /* Field index constants */
    const BASEPUSH = 1;
    const DEVICERESOURCEID = 2;
    const DISTINCT = 3;
    const DATA = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASEPUSH => array(
            'name' => 'BasePush',
            'required' => true,
            'type' => '\Proto2\Scentrealm\BasePush'
        ),
        self::DEVICERESOURCEID => array(
            'name' => 'DeviceResourceID',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::DISTINCT => array(
            'name' => 'Distinct',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::DATA => array(
            'name' => 'Data',
            'required' => true,
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
        $this->values[self::BASEPUSH] = null;
        $this->values[self::DEVICERESOURCEID] = null;
        $this->values[self::DISTINCT] = null;
        $this->values[self::DATA] = null;
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
     * @param \Proto2\Scentrealm\BasePush $value Property value
     *
     * @return null
     */
    public function setBasePush(\Proto2\Scentrealm\BasePush $value=null)
    {
        return $this->set(self::BASEPUSH, $value);
    }

    /**
     * Returns value of 'BasePush' property
     *
     * @return \Proto2\Scentrealm\BasePush
     */
    public function getBasePush()
    {
        return $this->get(self::BASEPUSH);
    }

    /**
     * Sets value of 'DeviceResourceID' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceResourceID($value)
    {
        return $this->set(self::DEVICERESOURCEID, $value);
    }

    /**
     * Returns value of 'DeviceResourceID' property
     *
     * @return string
     */
    public function getDeviceResourceID()
    {
        $value = $this->get(self::DEVICERESOURCEID);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'Distinct' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDistinct($value)
    {
        return $this->set(self::DISTINCT, $value);
    }

    /**
     * Returns value of 'Distinct' property
     *
     * @return string
     */
    public function getDistinct()
    {
        $value = $this->get(self::DISTINCT);
        return $value === null ? (string)$value : $value;
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
}
}