<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * SwitchBackgroudPush message
 */
class SwitchBackgroudPush extends \ProtobufMessage
{
    /* Field index constants */
    const BASEPUSH = 1;
    const SWITCHBACKGROUNDOP = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASEPUSH => array(
            'name' => 'BasePush',
            'required' => true,
            'type' => '\MmBp\BasePush'
        ),
        self::SWITCHBACKGROUNDOP => array(
            'name' => 'SwitchBackgroundOp',
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
        $this->values[self::BASEPUSH] = null;
        $this->values[self::SWITCHBACKGROUNDOP] = null;
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
     * Sets value of 'BasePush' property
     *
     * @param \MmBp\BasePush $value Property value
     *
     * @return null
     */
    public function setBasePush(\MmBp\BasePush $value=null)
    {
        return $this->set(self::BASEPUSH, $value);
    }

    /**
     * Returns value of 'BasePush' property
     *
     * @return \MmBp\BasePush
     */
    public function getBasePush()
    {
        return $this->get(self::BASEPUSH);
    }

    /**
     * Sets value of 'SwitchBackgroundOp' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setSwitchBackgroundOp($value)
    {
        return $this->set(self::SWITCHBACKGROUNDOP, $value);
    }

    /**
     * Returns value of 'SwitchBackgroundOp' property
     *
     * @return integer
     */
    public function getSwitchBackgroundOp()
    {
        $value = $this->get(self::SWITCHBACKGROUNDOP);
        return $value === null ? (integer)$value : $value;
    }
}
}