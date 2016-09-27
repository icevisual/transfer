<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * InitResponse message
 */
class InitResponse extends \ProtobufMessage
{
    /* Field index constants */
    const BASERESPONSE = 1;
    const USERIDHIGH = 2;
    const USERIDLOW = 3;
    const CHALLEANGEANSWER = 4;
    const INITSCENCE = 5;
    const AUTOSYNCMAXDURATIONSECOND = 6;
    const USERNICKNAME = 11;
    const PLATFORMTYPE = 12;
    const MODEL = 13;
    const OS = 14;
    const TIME = 15;
    const TIMEZONE = 16;
    const TIMESTRING = 17;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::BASERESPONSE => array(
            'name' => 'BaseResponse',
            'required' => true,
            'type' => '\MmBp\BaseResponse'
        ),
        self::USERIDHIGH => array(
            'name' => 'UserIdHigh',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::USERIDLOW => array(
            'name' => 'UserIdLow',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::CHALLEANGEANSWER => array(
            'name' => 'ChalleangeAnswer',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::INITSCENCE => array(
            'name' => 'InitScence',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::AUTOSYNCMAXDURATIONSECOND => array(
            'name' => 'AutoSyncMaxDurationSecond',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::USERNICKNAME => array(
            'name' => 'UserNickName',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::PLATFORMTYPE => array(
            'name' => 'PlatformType',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::MODEL => array(
            'name' => 'Model',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::OS => array(
            'name' => 'Os',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::TIME => array(
            'name' => 'Time',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::TIMEZONE => array(
            'name' => 'TimeZone',
            'required' => false,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::TIMESTRING => array(
            'name' => 'TimeString',
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
        $this->values[self::USERIDHIGH] = null;
        $this->values[self::USERIDLOW] = null;
        $this->values[self::CHALLEANGEANSWER] = null;
        $this->values[self::INITSCENCE] = null;
        $this->values[self::AUTOSYNCMAXDURATIONSECOND] = null;
        $this->values[self::USERNICKNAME] = null;
        $this->values[self::PLATFORMTYPE] = null;
        $this->values[self::MODEL] = null;
        $this->values[self::OS] = null;
        $this->values[self::TIME] = null;
        $this->values[self::TIMEZONE] = null;
        $this->values[self::TIMESTRING] = null;
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
     * Sets value of 'UserIdHigh' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setUserIdHigh($value)
    {
        return $this->set(self::USERIDHIGH, $value);
    }

    /**
     * Returns value of 'UserIdHigh' property
     *
     * @return integer
     */
    public function getUserIdHigh()
    {
        $value = $this->get(self::USERIDHIGH);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'UserIdLow' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setUserIdLow($value)
    {
        return $this->set(self::USERIDLOW, $value);
    }

    /**
     * Returns value of 'UserIdLow' property
     *
     * @return integer
     */
    public function getUserIdLow()
    {
        $value = $this->get(self::USERIDLOW);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'ChalleangeAnswer' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setChalleangeAnswer($value)
    {
        return $this->set(self::CHALLEANGEANSWER, $value);
    }

    /**
     * Returns value of 'ChalleangeAnswer' property
     *
     * @return integer
     */
    public function getChalleangeAnswer()
    {
        $value = $this->get(self::CHALLEANGEANSWER);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'InitScence' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setInitScence($value)
    {
        return $this->set(self::INITSCENCE, $value);
    }

    /**
     * Returns value of 'InitScence' property
     *
     * @return integer
     */
    public function getInitScence()
    {
        $value = $this->get(self::INITSCENCE);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'AutoSyncMaxDurationSecond' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setAutoSyncMaxDurationSecond($value)
    {
        return $this->set(self::AUTOSYNCMAXDURATIONSECOND, $value);
    }

    /**
     * Returns value of 'AutoSyncMaxDurationSecond' property
     *
     * @return integer
     */
    public function getAutoSyncMaxDurationSecond()
    {
        $value = $this->get(self::AUTOSYNCMAXDURATIONSECOND);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'UserNickName' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setUserNickName($value)
    {
        return $this->set(self::USERNICKNAME, $value);
    }

    /**
     * Returns value of 'UserNickName' property
     *
     * @return string
     */
    public function getUserNickName()
    {
        $value = $this->get(self::USERNICKNAME);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'PlatformType' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setPlatformType($value)
    {
        return $this->set(self::PLATFORMTYPE, $value);
    }

    /**
     * Returns value of 'PlatformType' property
     *
     * @return integer
     */
    public function getPlatformType()
    {
        $value = $this->get(self::PLATFORMTYPE);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'Model' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setModel($value)
    {
        return $this->set(self::MODEL, $value);
    }

    /**
     * Returns value of 'Model' property
     *
     * @return string
     */
    public function getModel()
    {
        $value = $this->get(self::MODEL);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'Os' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setOs($value)
    {
        return $this->set(self::OS, $value);
    }

    /**
     * Returns value of 'Os' property
     *
     * @return string
     */
    public function getOs()
    {
        $value = $this->get(self::OS);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'Time' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setTime($value)
    {
        return $this->set(self::TIME, $value);
    }

    /**
     * Returns value of 'Time' property
     *
     * @return integer
     */
    public function getTime()
    {
        $value = $this->get(self::TIME);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'TimeZone' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setTimeZone($value)
    {
        return $this->set(self::TIMEZONE, $value);
    }

    /**
     * Returns value of 'TimeZone' property
     *
     * @return integer
     */
    public function getTimeZone()
    {
        $value = $this->get(self::TIMEZONE);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'TimeString' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setTimeString($value)
    {
        return $this->set(self::TIMESTRING, $value);
    }

    /**
     * Returns value of 'TimeString' property
     *
     * @return string
     */
    public function getTimeString()
    {
        $value = $this->get(self::TIMESTRING);
        return $value === null ? (string)$value : $value;
    }
}
}