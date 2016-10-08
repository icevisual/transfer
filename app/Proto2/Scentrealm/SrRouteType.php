<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * SrRouteType enum
 */
final class SrRouteType
{
    const SRT_req_auth = 1;
    const SRT_req_connect = 2;
    const SRT_req_sendData = 3;
    const SRT_resp_auth = 4;
    const SRT_resp_connect = 5;
    const SRT_resp_sendData = 6;
    const SRT_push_recvData = 7;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'SRT_req_auth' => self::SRT_req_auth,
            'SRT_req_connect' => self::SRT_req_connect,
            'SRT_req_sendData' => self::SRT_req_sendData,
            'SRT_resp_auth' => self::SRT_resp_auth,
            'SRT_resp_connect' => self::SRT_resp_connect,
            'SRT_resp_sendData' => self::SRT_resp_sendData,
            'SRT_push_recvData' => self::SRT_push_recvData,
        );
    }
}
}