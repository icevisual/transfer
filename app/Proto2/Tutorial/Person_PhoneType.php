<?php
/**
 * Auto generated from Person.pro at 2016-10-12 15:09:08
 *
 * Proto2.Tutorial package
 */

namespace Proto2\Tutorial {
/**
 * PhoneType enum embedded in Person message
 */
final class Person_PhoneType
{
    const MOBILE = 0;
    const HOME = 1;
    const WORK = 2;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'MOBILE' => self::MOBILE,
            'HOME' => self::HOME,
            'WORK' => self::WORK,
        );
    }
}
}