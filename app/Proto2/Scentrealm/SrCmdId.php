<?php
/**
 * Auto generated from Scentrealm.proto at 2016-09-22 18:24:29
 *
 * Proto2.Scentrealm package
 */

namespace Proto2\Scentrealm {
/**
 * SrCmdId enum
 */
final class SrCmdId
{
    const SCI_req_auth = 1;
    const SCI_req_connect = 2;
    const SCI_req_sendData = 3;
    const SCI_resp_auth = 4;
    const SCI_resp_connect = 5;
    const SCI_resp_sendData = 6;
    const SCI_push_recvData = 7;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'SCI_req_auth' => self::SCI_req_auth,
            'SCI_req_connect' => self::SCI_req_connect,
            'SCI_req_sendData' => self::SCI_req_sendData,
            'SCI_resp_auth' => self::SCI_resp_auth,
            'SCI_resp_connect' => self::SCI_resp_connect,
            'SCI_resp_sendData' => self::SCI_resp_sendData,
            'SCI_push_recvData' => self::SCI_push_recvData,
        );
    }
}
}