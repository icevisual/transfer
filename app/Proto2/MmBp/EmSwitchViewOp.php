<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * EmSwitchViewOp enum
 */
final class EmSwitchViewOp
{
    const ESVO_enter = 1;
    const ESVO_exit = 2;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'ESVO_enter' => self::ESVO_enter,
            'ESVO_exit' => self::ESVO_exit,
        );
    }
}
}