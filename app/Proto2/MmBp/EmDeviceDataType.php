<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * EmDeviceDataType enum
 */
final class EmDeviceDataType
{
    const EDDT_manufatureSvr = 0;
    const EDDT_wxWristBand = 1;
    const EDDT_wxDeviceHtmlChatView = 10001;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'EDDT_manufatureSvr' => self::EDDT_manufatureSvr,
            'EDDT_wxWristBand' => self::EDDT_wxWristBand,
            'EDDT_wxDeviceHtmlChatView' => self::EDDT_wxDeviceHtmlChatView,
        );
    }
}
}