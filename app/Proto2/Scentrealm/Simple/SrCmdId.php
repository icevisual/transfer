<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-11 10:35:21
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * SrCmdId enum
 */
final class SrCmdId
{
    const SCI_req_mac = 1;
    const SCI_resp_mac = 2;
    const SCI_req_uptime = 3;
    const SCI_resp_uptime = 4;
    const SCI_req_downtime = 5;
    const SCI_resp_downtime = 6;
    const SCI_req_sleep = 7;
    const SCI_resp_sleep = 8;
    const SCI_req_wakeup = 9;
    const SCI_resp_wakeup = 10;
    const SCI_req_usedSeconds = 11;
    const SCI_resp_usedSeconds = 12;
    const SCI_req_playSmell = 13;
    const SCI_resp_playSmell = 14;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'SCI_req_mac' => self::SCI_req_mac,
            'SCI_resp_mac' => self::SCI_resp_mac,
            'SCI_req_uptime' => self::SCI_req_uptime,
            'SCI_resp_uptime' => self::SCI_resp_uptime,
            'SCI_req_downtime' => self::SCI_req_downtime,
            'SCI_resp_downtime' => self::SCI_resp_downtime,
            'SCI_req_sleep' => self::SCI_req_sleep,
            'SCI_resp_sleep' => self::SCI_resp_sleep,
            'SCI_req_wakeup' => self::SCI_req_wakeup,
            'SCI_resp_wakeup' => self::SCI_resp_wakeup,
            'SCI_req_usedSeconds' => self::SCI_req_usedSeconds,
            'SCI_resp_usedSeconds' => self::SCI_resp_usedSeconds,
            'SCI_req_playSmell' => self::SCI_req_playSmell,
            'SCI_resp_playSmell' => self::SCI_resp_playSmell,
        );
    }
}
}