<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * SwitchViewPush message
 */
class SwitchViewPush extends \ProtobufMessage
{
    /* Field index constants */
    const BASEPUSH = 1;
    const SWITCHVIEWOP = 2;
    const VIEWID = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASEPUSH => array(
            'name' => 'BasePush',
            'required' => true,
            'type' => '\MmBp\BasePush'
        ),
        self::SWITCHVIEWOP => array(
            'name' => 'SwitchViewOp',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::VIEWID => array(
            'name' => 'ViewId',
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
        $this->values[self::SWITCHVIEWOP] = null;
        $this->values[self::VIEWID] = null;
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
     * Sets value of 'SwitchViewOp' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setSwitchViewOp($value)
    {
        return $this->set(self::SWITCHVIEWOP, $value);
    }

    /**
     * Returns value of 'SwitchViewOp' property
     *
     * @return integer
     */
    public function getSwitchViewOp()
    {
        $value = $this->get(self::SWITCHVIEWOP);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'ViewId' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setViewId($value)
    {
        return $this->set(self::VIEWID, $value);
    }

    /**
     * Returns value of 'ViewId' property
     *
     * @return integer
     */
    public function getViewId()
    {
        $value = $this->get(self::VIEWID);
        return $value === null ? (integer)$value : $value;
    }
}
}