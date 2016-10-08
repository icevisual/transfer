<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * PlayRequest message
 */
class PlayRequest extends \ProtobufMessage
{
    /* Field index constants */
    const BASEREQUEST = 1;
    const DEVICERESOURCEID = 2;
    const CMDSEQ = 3;
    const PLAY = 4;

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
        self::CMDSEQ => array(
            'name' => 'cmdSeq',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::PLAY => array(
            'name' => 'play',
            'required' => true,
            'type' => '\Proto2\Scentrealm\PlaySmell'
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
        $this->values[self::CMDSEQ] = null;
        $this->values[self::PLAY] = null;
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
     * Sets value of 'cmdSeq' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setCmdSeq($value)
    {
        return $this->set(self::CMDSEQ, $value);
    }

    /**
     * Returns value of 'cmdSeq' property
     *
     * @return string
     */
    public function getCmdSeq()
    {
        $value = $this->get(self::CMDSEQ);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'play' property
     *
     * @param \Proto2\Scentrealm\PlaySmell $value Property value
     *
     * @return null
     */
    public function setPlay(\Proto2\Scentrealm\PlaySmell $value=null)
    {
        return $this->set(self::PLAY, $value);
    }

    /**
     * Returns value of 'play' property
     *
     * @return \Proto2\Scentrealm\PlaySmell
     */
    public function getPlay()
    {
        return $this->get(self::PLAY);
    }
}
}