<?php
/**
 * Auto generated from Person.pro at 2016-10-12 15:09:08
 *
 * Proto2.Tutorial package
 */

namespace Proto2\Tutorial {
/**
 * AddressBook message
 */
class AddressBook extends \ProtobufMessage
{
    /* Field index constants */
    const PERSON = 1;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::PERSON => array(
            'name' => 'person',
            'repeated' => true,
            'type' => '\Proto2\Tutorial\Person'
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
        $this->values[self::PERSON] = array();
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
     * Appends value to 'person' list
     *
     * @param \Proto2\Tutorial\Person $value Value to append
     *
     * @return null
     */
    public function appendPerson(\Proto2\Tutorial\Person $value)
    {
        return $this->append(self::PERSON, $value);
    }

    /**
     * Clears 'person' list
     *
     * @return null
     */
    public function clearPerson()
    {
        return $this->clear(self::PERSON);
    }

    /**
     * Returns 'person' list
     *
     * @return \Proto2\Tutorial\Person[]
     */
    public function getPerson()
    {
        return $this->get(self::PERSON);
    }

    /**
     * Returns 'person' iterator
     *
     * @return \ArrayIterator
     */
    public function getPersonIterator()
    {
        return new \ArrayIterator($this->get(self::PERSON));
    }

    /**
     * Returns element from 'person' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Proto2\Tutorial\Person
     */
    public function getPersonAt($offset)
    {
        return $this->get(self::PERSON, $offset);
    }

    /**
     * Returns count of 'person' list
     *
     * @return int
     */
    public function getPersonCount()
    {
        return $this->count(self::PERSON);
    }
}
}