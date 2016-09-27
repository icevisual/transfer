<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * EmErrorCode enum
 */
final class EmErrorCode
{
    const EEC_system = 1;
    const EEC_needAuth = 2;
    const EEC_sessionTimeout = 3;
    const EEC_decode = 4;
    const EEC_deviceIsBlock = 5;
    const EEC_serviceUnAvalibleInBackground = 6;
    const EEC_deviceProtoVersionNeedUpdate = 7;
    const EEC_phoneProtoVersionNeedUpdate = 8;
    const EEC_maxReqInQueue = 9;
    const EEC_userExitWxAccount = 10;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'EEC_system' => self::EEC_system,
            'EEC_needAuth' => self::EEC_needAuth,
            'EEC_sessionTimeout' => self::EEC_sessionTimeout,
            'EEC_decode' => self::EEC_decode,
            'EEC_deviceIsBlock' => self::EEC_deviceIsBlock,
            'EEC_serviceUnAvalibleInBackground' => self::EEC_serviceUnAvalibleInBackground,
            'EEC_deviceProtoVersionNeedUpdate' => self::EEC_deviceProtoVersionNeedUpdate,
            'EEC_phoneProtoVersionNeedUpdate' => self::EEC_phoneProtoVersionNeedUpdate,
            'EEC_maxReqInQueue' => self::EEC_maxReqInQueue,
            'EEC_userExitWxAccount' => self::EEC_userExitWxAccount,
        );
    }
}
}