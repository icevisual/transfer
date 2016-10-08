<?php
/**
 * Auto generated from Scentrealm.proto at 2016-10-08 15:04:40
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
    const SCI_req_mac = 3;
    const SCI_req_uptime = 4;
    const SCI_req_downtime = 5;
    const SCI_req_sleep = 6;
    const SCI_req_wakeup = 7;
    const SCI_req_usedSeconds = 8;
    const SCI_req_enableSmell = 9;
    const SCI_req_playSmell = 10;
    const SCI_resp_auth = 11;
    const SCI_resp_connect = 12;
    const SCI_push_recvData = 13;

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
            'SCI_req_mac' => self::SCI_req_mac,
            'SCI_req_uptime' => self::SCI_req_uptime,
            'SCI_req_downtime' => self::SCI_req_downtime,
            'SCI_req_sleep' => self::SCI_req_sleep,
            'SCI_req_wakeup' => self::SCI_req_wakeup,
            'SCI_req_usedSeconds' => self::SCI_req_usedSeconds,
            'SCI_req_enableSmell' => self::SCI_req_enableSmell,
            'SCI_req_playSmell' => self::SCI_req_playSmell,
            'SCI_resp_auth' => self::SCI_resp_auth,
            'SCI_resp_connect' => self::SCI_resp_connect,
            'SCI_push_recvData' => self::SCI_push_recvData,
        );
    }
}
}