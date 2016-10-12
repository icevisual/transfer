<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-12 14:22:47
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * PlaySmell message
 */
class PlaySmell extends \ProtobufMessage
{
    /* Field index constants */
    const WHEN = 1;
    const PLAY = 2;
    const TRACE = 3;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::WHEN => array(
            'name' => 'when',
            'required' => true,
            'type' => '\Proto2\Scentrealm\Simple\PlayStartTime'
        ),
        self::PLAY => array(
            'name' => 'play',
            'repeated' => true,
            'type' => '\Proto2\Scentrealm\Simple\PlayAction'
        ),
        self::TRACE => array(
            'name' => 'trace',
            'repeated' => true,
            'type' => '\Proto2\Scentrealm\Simple\PlayTrace'
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
        $this->values[self::WHEN] = null;
        $this->values[self::PLAY] = array();
        $this->values[self::TRACE] = array();
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
     * Sets value of 'when' property
     *
     * @param \Proto2\Scentrealm\Simple\PlayStartTime $value Property value
     *
     * @return null
     */
    public function setWhen(\Proto2\Scentrealm\Simple\PlayStartTime $value=null)
    {
        return $this->set(self::WHEN, $value);
    }

    /**
     * Returns value of 'when' property
     *
     * @return \Proto2\Scentrealm\Simple\PlayStartTime
     */
    public function getWhen()
    {
        return $this->get(self::WHEN);
    }

    /**
     * Appends value to 'play' list
     *
     * @param \Proto2\Scentrealm\Simple\PlayAction $value Value to append
     *
     * @return null
     */
    public function appendPlay(\Proto2\Scentrealm\Simple\PlayAction $value)
    {
        return $this->append(self::PLAY, $value);
    }

    /**
     * Clears 'play' list
     *
     * @return null
     */
    public function clearPlay()
    {
        return $this->clear(self::PLAY);
    }

    /**
     * Returns 'play' list
     *
     * @return \Proto2\Scentrealm\Simple\PlayAction[]
     */
    public function getPlay()
    {
        return $this->get(self::PLAY);
    }

    /**
     * Returns 'play' iterator
     *
     * @return \ArrayIterator
     */
    public function getPlayIterator()
    {
        return new \ArrayIterator($this->get(self::PLAY));
    }

    /**
     * Returns element from 'play' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Proto2\Scentrealm\Simple\PlayAction
     */
    public function getPlayAt($offset)
    {
        return $this->get(self::PLAY, $offset);
    }

    /**
     * Returns count of 'play' list
     *
     * @return int
     */
    public function getPlayCount()
    {
        return $this->count(self::PLAY);
    }

    /**
     * Appends value to 'trace' list
     *
     * @param \Proto2\Scentrealm\Simple\PlayTrace $value Value to append
     *
     * @return null
     */
    public function appendTrace(\Proto2\Scentrealm\Simple\PlayTrace $value)
    {
        return $this->append(self::TRACE, $value);
    }

    /**
     * Clears 'trace' list
     *
     * @return null
     */
    public function clearTrace()
    {
        return $this->clear(self::TRACE);
    }

    /**
     * Returns 'trace' list
     *
     * @return \Proto2\Scentrealm\Simple\PlayTrace[]
     */
    public function getTrace()
    {
        return $this->get(self::TRACE);
    }

    /**
     * Returns 'trace' iterator
     *
     * @return \ArrayIterator
     */
    public function getTraceIterator()
    {
        return new \ArrayIterator($this->get(self::TRACE));
    }

    /**
     * Returns element from 'trace' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Proto2\Scentrealm\Simple\PlayTrace
     */
    public function getTraceAt($offset)
    {
        return $this->get(self::TRACE, $offset);
    }

    /**
     * Returns count of 'trace' list
     *
     * @return int
     */
    public function getTraceCount()
    {
        return $this->count(self::TRACE);
    }
}
}