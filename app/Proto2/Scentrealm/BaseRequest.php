<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
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
    const SENDER = 1;
    const TIMESTAMP = 2;
    const IDENTITY = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::SENDER => array(
            'default' => \Proto2\Scentrealm\SrSenderType::SST_controller,
            'name' => 'sender',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::TIMESTAMP => array(
            'name' => 'timestamp',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::IDENTITY => array(
            'name' => 'identity',
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
        $this->values[self::SENDER] = self::$fields[self::SENDER]['default'];
        $this->values[self::TIMESTAMP] = null;
        $this->values[self::IDENTITY] = null;
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
     * Sets value of 'sender' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setSender($value)
    {
        return $this->set(self::SENDER, $value);
    }

    /**
     * Returns value of 'sender' property
     *
     * @return integer
     */
    public function getSender()
    {
        $value = $this->get(self::SENDER);
        return $value === null ? (integer)$value : $value;
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
     * Sets value of 'identity' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setIdentity($value)
    {
        return $this->set(self::IDENTITY, $value);
    }

    /**
     * Returns value of 'identity' property
     *
     * @return string
     */
    public function getIdentity()
    {
        $value = $this->get(self::IDENTITY);
        return $value === null ? (string)$value : $value;
    }
}
}