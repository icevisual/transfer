<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * EmInitRespFieldFilter enum
 */
final class EmInitRespFieldFilter
{
    const EIRFF_userNickName = 1;
    const EIRFF_platformType = 2;
    const EIRFF_model = 4;
    const EIRFF_os = 8;
    const EIRFF_time = 16;
    const EIRFF_timeZone = 32;
    const EIRFF_timeString = 64;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'EIRFF_userNickName' => self::EIRFF_userNickName,
            'EIRFF_platformType' => self::EIRFF_platformType,
            'EIRFF_model' => self::EIRFF_model,
            'EIRFF_os' => self::EIRFF_os,
            'EIRFF_time' => self::EIRFF_time,
            'EIRFF_timeZone' => self::EIRFF_timeZone,
            'EIRFF_timeString' => self::EIRFF_timeString,
        );
    }
}
}