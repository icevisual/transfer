<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * EmCmdId enum
 */
final class EmCmdId
{
    const ECI_none = 0;
    const ECI_req_auth = 10001;
    const ECI_req_sendData = 10002;
    const ECI_req_init = 10003;
    const ECI_resp_auth = 20001;
    const ECI_resp_sendData = 20002;
    const ECI_resp_init = 20003;
    const ECI_push_recvData = 30001;
    const ECI_push_switchView = 30002;
    const ECI_push_switchBackgroud = 30003;
    const ECI_err_decode = 29999;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'ECI_none' => self::ECI_none,
            'ECI_req_auth' => self::ECI_req_auth,
            'ECI_req_sendData' => self::ECI_req_sendData,
            'ECI_req_init' => self::ECI_req_init,
            'ECI_resp_auth' => self::ECI_resp_auth,
            'ECI_resp_sendData' => self::ECI_resp_sendData,
            'ECI_resp_init' => self::ECI_resp_init,
            'ECI_push_recvData' => self::ECI_push_recvData,
            'ECI_push_switchView' => self::ECI_push_switchView,
            'ECI_push_switchBackgroud' => self::ECI_push_switchBackgroud,
            'ECI_err_decode' => self::ECI_err_decode,
        );
    }
}
}