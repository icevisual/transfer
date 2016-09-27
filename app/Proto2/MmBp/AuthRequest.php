<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * AuthRequest message
 */
class AuthRequest extends \ProtobufMessage
{
    /* Field index constants */
    const BASEREQUEST = 1;
    const MD5DEVICETYPEANDDEVICEID = 2;
    const PROTOVERSION = 3;
    const AUTHPROTO = 4;
    const AUTHMETHOD = 5;
    const AESSIGN = 6;
    const MACADDRESS = 7;
    const TIMEZONE = 10;
    const LANGUAGE = 11;
    const DEVICENAME = 12;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASEREQUEST => array(
            'name' => 'BaseRequest',
            'required' => true,
            'type' => '\MmBp\BaseRequest'
        ),
        self::MD5DEVICETYPEANDDEVICEID => array(
            'name' => 'Md5DeviceTypeAndDeviceId',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::PROTOVERSION => array(
            'name' => 'ProtoVersion',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::AUTHPROTO => array(
            'name' => 'AuthProto',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::AUTHMETHOD => array(
            'name' => 'AuthMethod',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::AESSIGN => array(
            'name' => 'AesSign',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::MACADDRESS => array(
            'name' => 'MacAddress',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::TIMEZONE => array(
            'name' => 'TimeZone',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::LANGUAGE => array(
            'name' => 'Language',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::DEVICENAME => array(
            'name' => 'DeviceName',
            'required' => false,
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
        $this->values[self::MD5DEVICETYPEANDDEVICEID] = null;
        $this->values[self::PROTOVERSION] = null;
        $this->values[self::AUTHPROTO] = null;
        $this->values[self::AUTHMETHOD] = null;
        $this->values[self::AESSIGN] = null;
        $this->values[self::MACADDRESS] = null;
        $this->values[self::TIMEZONE] = null;
        $this->values[self::LANGUAGE] = null;
        $this->values[self::DEVICENAME] = null;
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
     * @param \MmBp\BaseRequest $value Property value
     *
     * @return null
     */
    public function setBaseRequest(\MmBp\BaseRequest $value=null)
    {
        return $this->set(self::BASEREQUEST, $value);
    }

    /**
     * Returns value of 'BaseRequest' property
     *
     * @return \MmBp\BaseRequest
     */
    public function getBaseRequest()
    {
        return $this->get(self::BASEREQUEST);
    }

    /**
     * Sets value of 'Md5DeviceTypeAndDeviceId' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setMd5DeviceTypeAndDeviceId($value)
    {
        return $this->set(self::MD5DEVICETYPEANDDEVICEID, $value);
    }

    /**
     * Returns value of 'Md5DeviceTypeAndDeviceId' property
     *
     * @return string
     */
    public function getMd5DeviceTypeAndDeviceId()
    {
        $value = $this->get(self::MD5DEVICETYPEANDDEVICEID);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'ProtoVersion' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setProtoVersion($value)
    {
        return $this->set(self::PROTOVERSION, $value);
    }

    /**
     * Returns value of 'ProtoVersion' property
     *
     * @return integer
     */
    public function getProtoVersion()
    {
        $value = $this->get(self::PROTOVERSION);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'AuthProto' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setAuthProto($value)
    {
        return $this->set(self::AUTHPROTO, $value);
    }

    /**
     * Returns value of 'AuthProto' property
     *
     * @return integer
     */
    public function getAuthProto()
    {
        $value = $this->get(self::AUTHPROTO);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'AuthMethod' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setAuthMethod($value)
    {
        return $this->set(self::AUTHMETHOD, $value);
    }

    /**
     * Returns value of 'AuthMethod' property
     *
     * @return integer
     */
    public function getAuthMethod()
    {
        $value = $this->get(self::AUTHMETHOD);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'AesSign' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setAesSign($value)
    {
        return $this->set(self::AESSIGN, $value);
    }

    /**
     * Returns value of 'AesSign' property
     *
     * @return string
     */
    public function getAesSign()
    {
        $value = $this->get(self::AESSIGN);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'MacAddress' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setMacAddress($value)
    {
        return $this->set(self::MACADDRESS, $value);
    }

    /**
     * Returns value of 'MacAddress' property
     *
     * @return string
     */
    public function getMacAddress()
    {
        $value = $this->get(self::MACADDRESS);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'TimeZone' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setTimeZone($value)
    {
        return $this->set(self::TIMEZONE, $value);
    }

    /**
     * Returns value of 'TimeZone' property
     *
     * @return string
     */
    public function getTimeZone()
    {
        $value = $this->get(self::TIMEZONE);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'Language' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setLanguage($value)
    {
        return $this->set(self::LANGUAGE, $value);
    }

    /**
     * Returns value of 'Language' property
     *
     * @return string
     */
    public function getLanguage()
    {
        $value = $this->get(self::LANGUAGE);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'DeviceName' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setDeviceName($value)
    {
        return $this->set(self::DEVICENAME, $value);
    }

    /**
     * Returns value of 'DeviceName' property
     *
     * @return string
     */
    public function getDeviceName()
    {
        $value = $this->get(self::DEVICENAME);
        return $value === null ? (string)$value : $value;
    }
}
}