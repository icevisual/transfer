<?php
/**
 * Auto generated from Scentrealm.proto at 2016-09-22 18:24:29
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
    const DEVICEINFO = 2;
    const DISTINCT = 3;
    const DATA = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASEPUSH => array(
            'name' => 'BasePush',
            'required' => true,
            'type' => '\Proto2\Scentrealm\BasePush'
        ),
        self::DEVICEINFO => array(
            'name' => 'DeviceInfo',
            'required' => true,
            'type' => '\Proto2\Scentrealm\DeviceInfo'
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
        $this->values[self::DEVICEINFO] = null;
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
     * Sets value of 'DeviceInfo' property
     *
     * @param \Proto2\Scentrealm\DeviceInfo $value Property value
     *
     * @return null
     */
    public function setDeviceInfo(\Proto2\Scentrealm\DeviceInfo $value=null)
    {
        return $this->set(self::DEVICEINFO, $value);
    }

    /**
     * Returns value of 'DeviceInfo' property
     *
     * @return \Proto2\Scentrealm\DeviceInfo
     */
    public function getDeviceInfo()
    {
        return $this->get(self::DEVICEINFO);
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