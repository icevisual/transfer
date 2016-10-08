<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * AuthResponse message
 */
class AuthResponse extends \ProtobufMessage
{
    /* Field index constants */
    const BASERESPONSE = 1;
    const AESSESSIONKEY = 2;
    const EXPIRESECOND = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASERESPONSE => array(
            'name' => 'BaseResponse',
            'required' => true,
            'type' => '\Proto2\Scentrealm\BaseResponse'
        ),
        self::AESSESSIONKEY => array(
            'name' => 'AesSessionKey',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::EXPIRESECOND => array(
            'name' => 'expireSecond',
            'required' => true,
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
        $this->values[self::BASERESPONSE] = null;
        $this->values[self::AESSESSIONKEY] = null;
        $this->values[self::EXPIRESECOND] = null;
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
     * Sets value of 'AesSessionKey' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setAesSessionKey($value)
    {
        return $this->set(self::AESSESSIONKEY, $value);
    }

    /**
     * Returns value of 'AesSessionKey' property
     *
     * @return string
     */
    public function getAesSessionKey()
    {
        $value = $this->get(self::AESSESSIONKEY);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'expireSecond' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setExpireSecond($value)
    {
        return $this->set(self::EXPIRESECOND, $value);
    }

    /**
     * Returns value of 'expireSecond' property
     *
     * @return integer
     */
    public function getExpireSecond()
    {
        $value = $this->get(self::EXPIRESECOND);
        return $value === null ? (integer)$value : $value;
    }
}
}