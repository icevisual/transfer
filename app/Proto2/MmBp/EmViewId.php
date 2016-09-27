<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * EmViewId enum
 */
final class EmViewId
{
    const EVI_deviceChatView = 1;
    const EVI_deviceChatHtmlView = 2;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'EVI_deviceChatView' => self::EVI_deviceChatView,
            'EVI_deviceChatHtmlView' => self::EVI_deviceChatHtmlView,
        );
    }
}
}