<?php
/**
 * Auto generated from Person.pro at 2016-10-12 15:09:08
 *
 * Proto2.Tutorial package
 */

namespace Proto2\Tutorial {
/**
 * Person message
 */
class Person extends \ProtobufMessage
{
    /* Field index constants */
    const NAME = 1;
    const ID = 2;
    const EMAIL = 3;
    const PHONE = 4;

    /* @var array Field descriptors */
    protected static $fields = array(
        self::NAME => array(
            'name' => 'name',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::ID => array(
            'name' => 'id',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_INT,
        ),
        self::EMAIL => array(
            'name' => 'email',
            'required' => true,
            'type' => \ProtobufMessage::PB_TYPE_STRING,
        ),
        self::PHONE => array(
            'name' => 'phone',
            'repeated' => true,
            'type' => '\Proto2\Tutorial\Person_PhoneNumber'
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
        $this->values[self::NAME] = null;
        $this->values[self::ID] = null;
        $this->values[self::EMAIL] = null;
        $this->values[self::PHONE] = array();
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
     * Sets value of 'name' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setName($value)
    {
        return $this->set(self::NAME, $value);
    }

    /**
     * Returns value of 'name' property
     *
     * @return string
     */
    public function getName()
    {
        $value = $this->get(self::NAME);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Sets value of 'id' property
     *
     * @param integer $value Property value
     *
     * @return null
     */
    public function setId($value)
    {
        return $this->set(self::ID, $value);
    }

    /**
     * Returns value of 'id' property
     *
     * @return integer
     */
    public function getId()
    {
        $value = $this->get(self::ID);
        return $value === null ? (integer)$value : $value;
    }

    /**
     * Sets value of 'email' property
     *
     * @param string $value Property value
     *
     * @return null
     */
    public function setEmail($value)
    {
        return $this->set(self::EMAIL, $value);
    }

    /**
     * Returns value of 'email' property
     *
     * @return string
     */
    public function getEmail()
    {
        $value = $this->get(self::EMAIL);
        return $value === null ? (string)$value : $value;
    }

    /**
     * Appends value to 'phone' list
     *
     * @param \Proto2\Tutorial\Person_PhoneNumber $value Value to append
     *
     * @return null
     */
    public function appendPhone(\Proto2\Tutorial\Person_PhoneNumber $value)
    {
        return $this->append(self::PHONE, $value);
    }

    /**
     * Clears 'phone' list
     *
     * @return null
     */
    public function clearPhone()
    {
        return $this->clear(self::PHONE);
    }

    /**
     * Returns 'phone' list
     *
     * @return \Proto2\Tutorial\Person_PhoneNumber[]
     */
    public function getPhone()
    {
        return $this->get(self::PHONE);
    }

    /**
     * Returns 'phone' iterator
     *
     * @return \ArrayIterator
     */
    public function getPhoneIterator()
    {
        return new \ArrayIterator($this->get(self::PHONE));
    }

    /**
     * Returns element from 'phone' list at given offset
     *
     * @param int $offset Position in list
     *
     * @return \Proto2\Tutorial\Person_PhoneNumber
     */
    public function getPhoneAt($offset)
    {
        return $this->get(self::PHONE, $offset);
    }

    /**
     * Returns count of 'phone' list
     *
     * @return int
     */
    public function getPhoneCount()
    {
        return $this->count(self::PHONE);
    }
}
}