<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * SendDataResponse message
 */
class SendDataResponse extends \ProtobufMessage
{
    /* Field index constants */
    const BASERESPONSE = 1;
    const DATA = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASERESPONSE => array(
            'name' => 'BaseResponse',
            'required' => true,
            'type' => '\MmBp\BaseResponse'
        ),
        self::DATA => array(
            'name' => 'Data',
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
        $this->values[self::BASERESPONSE] = null;
        $this->values[self::DATA] = null;
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
     * Sets value of 'Data' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setData($value)
    {
        return $this->set(self::DATA, $value);
    }

    /**
     * Returns value of 'Data' property
     *
     * @return string
     */
    public function getData()
    {
        $value = $this->get(self::DATA);
        return $value === null ? (string)$value : $value;
    }
}
}