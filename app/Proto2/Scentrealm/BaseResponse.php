<?php
/**
 * Auto generated from Scentrealm.proto at 2016-09-22 18:24:29
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * BaseResponse message
 */
class BaseResponse extends \ProtobufMessage
{
    /* Field index constants */
    const TIMESTAMP = 1;
    const ERRCODE = 2;
    const ERRMSG = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::TIMESTAMP => array(
            'name' => 'timestamp',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::ERRCODE => array(
            'name' => 'ErrCode',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::ERRMSG => array(
            'name' => 'ErrMsg',
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
        $this->values[self::TIMESTAMP] = null;
        $this->values[self::ERRCODE] = null;
        $this->values[self::ERRMSG] = null;
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
     * Sets value of 'timestamp' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setTimestamp($value)
    {
        return $this->set(self::TIMESTAMP, $value);
    }

    /**
     * Returns value of 'timestamp' property
     *
     * @return integer
     */
    public function getTimestamp()
    {
        $value = $this->get(self::TIMESTAMP);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'ErrCode' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setErrCode($value)
    {
        return $this->set(self::ERRCODE, $value);
    }

    /**
     * Returns value of 'ErrCode' property
     *
     * @return integer
     */
    public function getErrCode()
    {
        $value = $this->get(self::ERRCODE);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'ErrMsg' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setErrMsg($value)
    {
        return $this->set(self::ERRMSG, $value);
    }

    /**
     * Returns value of 'ErrMsg' property
     *
     * @return string
     */
    public function getErrMsg()
    {
        $value = $this->get(self::ERRMSG);
        return $value === null ? (string)$value : $value;
    }
}
}