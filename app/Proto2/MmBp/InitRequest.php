<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * InitRequest message
 */
class InitRequest extends \ProtobufMessage
{
    /* Field index constants */
    const BASEREQUEST = 1;
    const RESPFIELDFILTER = 2;
    const CHALLENGE = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASEREQUEST => array(
            'name' => 'BaseRequest',
            'required' => true,
            'type' => '\MmBp\BaseRequest'
        ),
        self::RESPFIELDFILTER => array(
            'name' => 'RespFieldFilter',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::CHALLENGE => array(
            'name' => 'Challenge',
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
        $this->values[self::RESPFIELDFILTER] = null;
        $this->values[self::CHALLENGE] = null;
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
     * Sets value of 'RespFieldFilter' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setRespFieldFilter($value)
    {
        return $this->set(self::RESPFIELDFILTER, $value);
    }

    /**
     * Returns value of 'RespFieldFilter' property
     *
     * @return string
     */
    public function getRespFieldFilter()
    {
        $value = $this->get(self::RESPFIELDFILTER);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'Challenge' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setChallenge($value)
    {
        return $this->set(self::CHALLENGE, $value);
    }

    /**
     * Returns value of 'Challenge' property
     *
     * @return string
     */
    public function getChallenge()
    {
        $value = $this->get(self::CHALLENGE);
        return $value === null ? (string)$value : $value;
    }
}
}