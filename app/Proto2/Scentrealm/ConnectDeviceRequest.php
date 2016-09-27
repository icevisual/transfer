<?php
/**
 * Auto generated from Scentrealm.proto at 2016-09-22 18:24:29
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * ConnectDeviceRequest message
 */
class ConnectDeviceRequest extends \ProtobufMessage
{
    /* Field index constants */
    const BASEREQUEST = 1;
    const DEVICEID = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASEREQUEST => array(
            'name' => 'BaseRequest',
            'required' => true,
            'type' => '\Proto2\Scentrealm\BaseRequest'
        ),
        self::DEVICEID => array(
            'name' => 'DeviceID',
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
        $this->values[self::DEVICEID] = null;
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
     * Sets value of 'DeviceID' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceID($value)
    {
        return $this->set(self::DEVICEID, $value);
    }

    /**
     * Returns value of 'DeviceID' property
     *
     * @return string
     */
    public function getDeviceID()
    {
        $value = $this->get(self::DEVICEID);
        return $value === null ? (string)$value : $value;
    }
}
}