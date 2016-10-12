<?php
/**
 * Auto generated from Simple.proto.js at 2016-10-12 15:41:05
 *
 * Proto2.Scentrealm.Simple package
 */

namespace Proto2\Scentrealm\Simple {
/**
 * SrCycleMode enum
 */
final class SrCycleMode
{
    const SCM_cycle_no = 1;
    const SCM_cycle_yes = 2;
    const SCM_cycle_infinite = 3;

    /**
     * Returns defined enum values
     *
     * @return int[]
     */
    public function getEnumValues()
    {
        return array(
            'SCM_cycle_no' => self::SCM_cycle_no,
            'SCM_cycle_yes' => self::SCM_cycle_yes,
            'SCM_cycle_infinite' => self::SCM_cycle_infinite,
        );
    }
}
}