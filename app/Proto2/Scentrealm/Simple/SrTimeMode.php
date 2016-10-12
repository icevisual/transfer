<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-12 15:41:05
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * SrTimeMode enum
 */
final class SrTimeMode
{
    const STM_relative = 1;
    const STM_absolute = 2;
    const STM_daytime = 3;
    const STM_weekday = 4;
    const STM_monthday = 5;
    const STM_month = 6;
    const STM_year = 7;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'STM_relative' => self::STM_relative,
            'STM_absolute' => self::STM_absolute,
            'STM_daytime' => self::STM_daytime,
            'STM_weekday' => self::STM_weekday,
            'STM_monthday' => self::STM_monthday,
            'STM_month' => self::STM_month,
            'STM_year' => self::STM_year,
        );
    }
}
}