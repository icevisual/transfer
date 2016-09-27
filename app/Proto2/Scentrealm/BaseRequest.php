<?php
/**
 * Auto generated from Scentrealm.proto at 2016-09-22 18:24:29
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * BaseRequest message
 */
class BaseRequest extends \ProtobufMessage
{
    /* Field index constants */
    const TIMESTAMP = 1;
    const ACCESSKEY = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::TIMESTAMP => array(
            'name' => 'timestamp',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::ACCESSKEY => array(
            'name' => 'accessKey',
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
        $this->values[self::TIMESTAMP] = null;
        $this->values[self::ACCESSKEY] = null;
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
     * Sets value of 'accessKey' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setAccessKey($value)
    {
        return $this->set(self::ACCESSKEY, $value);
    }

    /**
     * Returns value of 'accessKey' property
     *
     * @return string
     */
    public function getAccessKey()
    {
        $value = $this->get(self::ACCESSKEY);
        return $value === null ? (string)$value : $value;
    }
}
}