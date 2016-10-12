<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-12 14:22:47
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * UsedTimeResponse message
 */
class UsedTimeResponse extends \ProtobufMessage
{
    /* Field index constants */
    const RESPONSE = 1;
    const USEDTIME = 2;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::RESPONSE => array(
            'name' => 'response',
            'required' => true,
            'type' => '\Proto2\Scentrealm\Simple\BaseResponse'
        ),
        self::USEDTIME => array(
            'name' => 'usedTime',
            'repeated' => true,
            'type' => '\Proto2\Scentrealm\Simple\UsedTime'
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
        $this->values[self::RESPONSE] = null;
        $this->values[self::USEDTIME] = array();
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
     * Sets value of 'response' property
     *
     * @param \Proto2\Scentrealm\Simple\BaseResponse $value Property value
     *
     * @return null
     */
    public function setResponse(\Proto2\Scentrealm\Simple\BaseResponse $value=null)
    {
        return $this->set(self::RESPONSE, $value);
    }

    /**
     * Returns value of 'response' property
     *
     * @return \Proto2\Scentrealm\Simple\BaseResponse
     */
    public function getResponse()
    {
        return $this->get(self::RESPONSE);
    }

    /**
     * Appends value to 'usedTime' list
     *
     * @param \Proto2\Scentrealm\Simple\UsedTime $value Value to append
     *
     * @return null
     */
    public function appendUsedTime(\Proto2\Scentrealm\Simple\UsedTime $value)
    {
        return $this->append(self::USEDTIME, $value);
    }

    /**
     * Clears 'usedTime' list
     *
     * @return null
     */
    public function clearUsedTime()
    {
        return $this->clear(self::USEDTIME);
    }

    /**
     * Returns 'usedTime' list
     *
     * @return \Proto2\Scentrealm\Simple\UsedTime[]
     */
    public function getUsedTime()
    {
        return $this->get(self::USEDTIME);
    }

    /**
     * Returns 'usedTime' iterator
     *
     * @return \ArrayIterator
     */
    public function getUsedTimeIterator()
    {
        return new \ArrayIterator($this->get(self::USEDTIME));
    }

    /**
     * Returns element from 'usedTime' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Proto2\Scentrealm\Simple\UsedTime
     */
    public function getUsedTimeAt($offset)
    {
        return $this->get(self::USEDTIME, $offset);
    }

    /**
     * Returns count of 'usedTime' list
     *
     * @return int
     */
    public function getUsedTimeCount()
    {
        return $this->count(self::USEDTIME);
    }
}
}