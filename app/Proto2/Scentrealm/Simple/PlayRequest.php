<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-12 14:22:47
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * PlayRequest message
 */
class PlayRequest extends \ProtobufMessage
{
    /* Field index constants */
    const CMDSEQ = 3;
    const PLAY = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::CMDSEQ => array(
            'name' => 'cmdSeq',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::PLAY => array(
            'name' => 'play',
            'required' => true,
            'type' => '\Proto2\Scentrealm\Simple\PlaySmell'
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
     * @param \Proto2\Scentrealm\Simple\PlaySmell $value Property value
     *
     * @return null
     */
    public function setPlay(\Proto2\Scentrealm\Simple\PlaySmell $value=null)
    {
        return $this->set(self::PLAY, $value);
    }

    /**
     * Returns value of 'play' property
     *
     * @return \Proto2\Scentrealm\Simple\PlaySmell
     */
    public function getPlay()
    {
        return $this->get(self::PLAY);
    }
}
}