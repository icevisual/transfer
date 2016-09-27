<?php
/**
 * Auto generated from Scentrealm.proto at 2016-09-22 18:24:29
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * ConnectDeviceResponse message
 */
class ConnectDeviceResponse extends \ProtobufMessage
{
    /* Field index constants */
    const BASERESPONSE = 1;
    const DEVICERESOURCEID = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASERESPONSE => array(
            'name' => 'BaseResponse',
            'required' => true,
            'type' => '\Proto2\Scentrealm\BaseResponse'
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
        $this->values[self::BASERESPONSE] = null;
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
     * Sets value of 'BaseResponse' property
     *
     * @param \Proto2\Scentrealm\BaseResponse $value Property value
     *
     * @return null
     */
    public function setBaseResponse(\Proto2\Scentrealm\BaseResponse $value=null)
    {
        return $this->set(self::BASERESPONSE, $value);
    }

    /**
     * Returns value of 'BaseResponse' property
     *
     * @return \Proto2\Scentrealm\BaseResponse
     */
    public function getBaseResponse()
    {
        return $this->get(self::BASERESPONSE);
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