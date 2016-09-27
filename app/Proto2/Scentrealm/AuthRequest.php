<?php
/**
 * Auto generated from Scentrealm.proto at 2016-09-22 18:24:29
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * AuthRequest message
 */
class AuthRequest extends \ProtobufMessage
{
    /* Field index constants */
    const BASEREQUEST = 1;
    const SIGNATUREMETHOD = 4;
    const SIGNATURENONCE = 5;
    const SIGNATURE = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASEREQUEST => array(
            'name' => 'BaseRequest',
            'required' => true,
            'type' => '\Proto2\Scentrealm\BaseRequest'
        ),
        self::SIGNATUREMETHOD => array(
            'default' => \Proto2\Scentrealm\SrSignatureMethod::SSM_HMAC_SHA1,
            'name' => 'SignatureMethod',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::SIGNATURENONCE => array(
            'name' => 'SignatureNonce',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::SIGNATURE => array(
            'name' => 'Signature',
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
        $this->values[self::BASEREQUEST] = null;
        $this->values[self::SIGNATUREMETHOD] = self::$fields[self::SIGNATUREMETHOD]['default'];
        $this->values[self::SIGNATURENONCE] = null;
        $this->values[self::SIGNATURE] = null;
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
     * Sets value of 'SignatureMethod' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setSignatureMethod($value)
    {
        return $this->set(self::SIGNATUREMETHOD, $value);
    }

    /**
     * Returns value of 'SignatureMethod' property
     *
     * @return integer
     */
    public function getSignatureMethod()
    {
        $value = $this->get(self::SIGNATUREMETHOD);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'SignatureNonce' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSignatureNonce($value)
    {
        return $this->set(self::SIGNATURENONCE, $value);
    }

    /**
     * Returns value of 'SignatureNonce' property
     *
     * @return string
     */
    public function getSignatureNonce()
    {
        $value = $this->get(self::SIGNATURENONCE);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'Signature' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setSignature($value)
    {
        return $this->set(self::SIGNATURE, $value);
    }

    /**
     * Returns value of 'Signature' property
     *
     * @return string
     */
    public function getSignature()
    {
        $value = $this->get(self::SIGNATURE);
        return $value === null ? (string)$value : $value;
    }
}
}