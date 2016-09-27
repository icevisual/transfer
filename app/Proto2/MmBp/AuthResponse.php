<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * AuthResponse message
 */
class AuthResponse extends \ProtobufMessage
{
    /* Field index constants */
    const BASERESPONSE = 1;
    const AESSESSIONKEY = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASERESPONSE => array(
            'name' => 'BaseResponse',
            'required' => true,
            'type' => '\MmBp\BaseResponse'
        ),
        self::AESSESSIONKEY => array(
            'name' => 'AesSessionKey',
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
        $this->values[self::AESSESSIONKEY] = null;
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
     * @param \MmBp\BaseResponse $value Property value
     *
     * @return null
     */
    public function setBaseResponse(\MmBp\BaseResponse $value=null)
    {
        return $this->set(self::BASERESPONSE, $value);
    }

    /**
     * Returns value of 'BaseResponse' property
     *
     * @return \MmBp\BaseResponse
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
}
}