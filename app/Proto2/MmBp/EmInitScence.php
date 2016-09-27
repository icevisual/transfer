<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * EmInitScence enum
 */
final class EmInitScence
{
    const EIS_deviceChat = 1;
    const EIS_autoSync = 2;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'EIS_deviceChat' => self::EIS_deviceChat,
            'EIS_autoSync' => self::EIS_autoSync,
        );
    }
}
}