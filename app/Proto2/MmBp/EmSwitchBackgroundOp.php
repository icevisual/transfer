<?php
/**
 * Auto generated from aa.proto at 2016-09-21 10:31:25
 *
 * MmBp package
 */

namespace MmBp {
/**
 * EmSwitchBackgroundOp enum
 */
final class EmSwitchBackgroundOp
{
    const ESBO_enterBackground = 1;
    const ESBO_enterForground = 2;
    const ESBO_sleep = 3;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'ESBO_enterBackground' => self::ESBO_enterBackground,
            'ESBO_enterForground' => self::ESBO_enterForground,
            'ESBO_sleep' => self::ESBO_sleep,
        );
    }
}
}