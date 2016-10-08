<?php
/**
 * Auto generated from Scentrealm.proto at 2016-09-23 15:00:51
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * DeviceInfo message
 */
class DeviceInfo extends \ProtobufMessage
{
    /* Field index constants */
    const SRDEVICETYPE = 1;
    const DEVICERESOURCEID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::SRDEVICETYPE => array(
            'default' => \Proto2\Scentrealm\SrDeviceType::SDT_aliyun,
            'name' => 'SrDeviceType',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::DEVICERESOURCEID => array(
            'name' => 'DeviceResourceID',
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
        $this->values[self::SRDEVICETYPE] = self::$fields[self::SRDEVICETYPE]['default'];
        $this->values[self::DEVICERESOURCEID] = null;
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
     * Sets value of 'SrDeviceType' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setSrDeviceType($value)
    {
        return $this->set(self::SRDEVICETYPE, $value);
    }

    /**
     * Returns value of 'SrDeviceType' property
     *
     * @return integer
     */
    public function getSrDeviceType()
    {
        $value = $this->get(self::SRDEVICETYPE);
        return $value === null ? (integer)$value : $value;
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
}
}