<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * SendDataRequest message
 */
class SendDataRequest extends \ProtobufMessage
{
    /* Field index constants */
    const BASEREQUEST = 1;
    const DEVICERESOURCEID = 2;
    const DATA = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASEREQUEST => array(
            'name' => 'BaseRequest',
            'required' => true,
            'type' => '\Proto2\Scentrealm\BaseRequest'
        ),
        self::DEVICERESOURCEID => array(
            'name' => 'DeviceResourceID',
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
        $this->values[self::BASEREQUEST] = null;
        $this->values[self::DEVICERESOURCEID] = null;
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
     * Sets value of 'BaseRequest' property
     *
     * @param \Proto2\Scentrealm\BaseRequest $value Property value
     *
     * @return null
     */
    public function setBaseRequest(\Proto2\Scentrealm\BaseRequest $value=null)
    {
        return $this->set(self::BASEREQUEST, $value);
    }

    /**
     * Returns value of 'BaseRequest' property
     *
     * @return \Proto2\Scentrealm\BaseRequest
     */
    public function getBaseRequest()
    {
        return $this->get(self::BASEREQUEST);
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