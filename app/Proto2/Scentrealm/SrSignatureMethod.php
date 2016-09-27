<?php
/**
 * Auto generated from Scentrealm.proto at 2016-09-22 18:24:29
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * SrSignatureMethod enum
 */
final class SrSignatureMethod
{
    const SSM_HMAC_SHA1 = 1;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'SSM_HMAC_SHA1' => self::SSM_HMAC_SHA1,
        );
    }
}
}