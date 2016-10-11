<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-11 10:35:21
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * SrErrorCode enum
 */
final class SrErrorCode
{
    const SEC_success = 0;
    const SEC_error = -1;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'SEC_success' => self::SEC_success,
            'SEC_error' => self::SEC_error,
        );
    }
}
}