<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * SrSenderType enum
 */
final class SrSenderType
{
    const SST_controller = 1;
    const SST_device = 2;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'SST_controller' => self::SST_controller,
            'SST_device' => self::SST_device,
        );
    }
}
}