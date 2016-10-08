<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * SrPlayMode enum
 */
final class SrPlayMode
{
    const SOM_relative = 1;
    const SOM_absolute = 2;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'SOM_relative' => self::SOM_relative,
            'SOM_absolute' => self::SOM_absolute,
        );
    }
}
}