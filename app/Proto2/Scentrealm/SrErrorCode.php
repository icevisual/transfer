<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * SrErrorCode enum
 */
final class SrErrorCode
{
    const SEC_success = 0;
    const SEC_error = -1;
    const SEC_unAuth = -2;
    const SEC_sessionTimeout = -3;
    const SEC_protoDecode = -4;
    const SEC_aesDecode = -5;
    const SEC_sign = -6;
    const SEC_deviceBlock = -7;
    const SEC_maxReqInQueue = -8;
    const SEC_deviceUnavailable = -9;
    const SEC_serverUnavailable = -10;
    const SEC_deviceUnconnected = -11;

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
            'SEC_unAuth' => self::SEC_unAuth,
            'SEC_sessionTimeout' => self::SEC_sessionTimeout,
            'SEC_protoDecode' => self::SEC_protoDecode,
            'SEC_aesDecode' => self::SEC_aesDecode,
            'SEC_sign' => self::SEC_sign,
            'SEC_deviceBlock' => self::SEC_deviceBlock,
            'SEC_maxReqInQueue' => self::SEC_maxReqInQueue,
            'SEC_deviceUnavailable' => self::SEC_deviceUnavailable,
            'SEC_serverUnavailable' => self::SEC_serverUnavailable,
            'SEC_deviceUnconnected' => self::SEC_deviceUnconnected,
        );
    }
}
}