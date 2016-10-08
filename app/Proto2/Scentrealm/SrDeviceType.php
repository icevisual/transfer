<?php
/**
 * Auto generated from Scentrealm.proto at 2016-09-23 15:00:51
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * SrDeviceType enum
 */
final class SrDeviceType
{
    const SDT_aliyun = 0;
    const SDT_open = 1;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'SDT_aliyun' => self::SDT_aliyun,
            'SDT_open' => self::SDT_open,
        );
    }
}
}