<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * EmAuthMethod enum
 */
final class EmAuthMethod
{
    const EAM_md5 = 1;
    const EAM_macNoEncrypt = 2;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'EAM_md5' => self::EAM_md5,
            'EAM_macNoEncrypt' => self::EAM_macNoEncrypt,
        );
    }
}
}