<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * EmPlatformType enum
 */
final class EmPlatformType
{
    const EPT_ios = 1;
    const EPT_andriod = 2;
    const EPT_wp = 3;
    const EPT_s60v3 = 4;
    const EPT_s60v5 = 5;
    const EPT_s40 = 6;
    const EPT_bb = 7;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'EPT_ios' => self::EPT_ios,
            'EPT_andriod' => self::EPT_andriod,
            'EPT_wp' => self::EPT_wp,
            'EPT_s60v3' => self::EPT_s60v3,
            'EPT_s60v5' => self::EPT_s60v5,
            'EPT_s40' => self::EPT_s40,
            'EPT_bb' => self::EPT_bb,
        );
    }
}
}