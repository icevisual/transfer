<?php

if (! function_exists ( 'mark' )) {

    /**
     * Calculates the time difference between two marked points.
     *
     * @param unknown $point1
     * @param string $point2
     * @param number $decimals
     * @return string|multitype:NULL
     */
    function mark($point1, $point2 = '', $decimals = 4) {
        static $marker = [ ];

        if ($point2 && $point1) {
            if (! isset ( $marker [$point1] ))
                return false;
            if (! isset ( $marker [$point2] )) {
                $marker [$point2] = microtime ();
            }
            	
            list ( $sm, $ss ) = explode ( ' ', $marker [$point1] );
            list ( $em, $es ) = explode ( ' ', $marker [$point2] );
            	
            return number_format ( ($em + $es) - ($sm + $ss), $decimals );
        } else if ($point1) {
            if ($point1 == '[clear]') {
                $marker = [ ];
            } else {
                $marker [$point1] = microtime ();
            }
        } else {
            return $marker;
        }
    }

    /**
     * Calculates the Memory difference between two marked points.
     *
     * @param unknown $point1
     * @param string $point2
     * @param number $decimals
     * @return string|multitype:NULL
     */
    function memory_mark($point1 = '', $point2 = '', $unit = 'KB', $decimals = 2) {
        static $marker = [ ];

        $units = [
            'B' => 1,
            'KB' => 1024,
            'MB' => 1048576,
            'GB' => 1073741824
        ];
        $unit = isset ( $units [$unit] ) ? $unit : 'KB';
        if ($point2 && $point1) {
            // 取件间隔
            if (! isset ( $marker [$point1] ))
                return false;
            if (! isset ( $marker [$point2] )) {
                $marker [$point2] = memory_get_usage ();
            }
            	
            return number_format ( ($marker [$point2] - $marker [$point1]) / $units [$unit], $decimals ); // .' '.$unit;
        } else if ($point1) {
            // 设记录点
            if ($point1 == '[clear]') {
                $marker = [ ];
            } else {
                $marker [$point1] = memory_get_usage ();
            }
        } else {
            // 返回所有
            return $marker;
        }
    }

    if (! function_exists ( 'mt_mark' )) {

        /**
         * Calculates the Memory & Time difference between two marked points.
         *
         * @param unknown $point1
         * @param string $point2
         * @param number $decimals
         * @return string|multitype:NULL
         */
        function mt_mark($point1 = '', $point2 = '', $unit = 'KB', $decimals = 4) {
            static $marker = [ ];

            $units = [
                'B' => 1,
                'KB' => 1024,
                'MB' => 1048576,
                'GB' => 1073741824
            ];
            $unit = isset ( $units [$unit] ) ? $unit : 'KB';
            if ($point2 && $point1) {
                // 取件间隔
                if (! isset ( $marker [$point1] ))
                    return false;
                if (! isset ( $marker [$point2] )) {
                    $marker [$point2] = [
                        'm' => memory_get_usage (),
                        't' => microtime ()
                    ];
                }
                	
                list ( $sm, $ss ) = explode ( ' ', $marker [$point1] ['t'] );
                list ( $em, $es ) = explode ( ' ', $marker [$point2] ['t'] );
                	
                return [
                    't' => number_format ( ($em + $es) - ($sm + $ss), $decimals ),
                    'm' => number_format ( ($marker [$point2] ['m'] - $marker [$point1] ['m']) / $units [$unit], $decimals )
                ];
            } else if ($point1) {
                // 设记录点
                if ($point1 == '[clear]') {
                    $marker = [ ];
                } else {
                    $marker [$point1] = [
                        'm' => memory_get_usage (),
                        't' => microtime ()
                    ];
                }
            } else {
                // 返回所有
                return $marker;
            }
        }

    }
    function dmt_mark($point1 = '', $point2 = '', $unit = 'MB', $decimals = 4) {
        redline ( $point1 . ' - ' . $point2 );
        $res = mt_mark ( $point1, $point2, $unit, $decimals );
        dump ( $res );
    }

    /**
     *
     * @param array $xAxis
     *        	['categories' => range(1,20,1)];
     * @param array $series
     *        	['name' => '','data' =>[]];
     * @param string $yAxis_title
     * @param string $title
     * @param string $subtitle
     * @return \Illuminate\View\$this
     */
    function chart(array $xAxis, array $series, $title = 'title', $subtitle = 'subtitle', $yAxis_title = 'yAxis_title') {
        $chartData = [
            'title' => $title,
            'subtitle' => $subtitle,
            'xAxis' => json_encode ( $xAxis ),
            'yAxis_title' => $yAxis_title,
            'series' => json_encode ( $series )
        ];
        return \View::make ( 'localtest.chart' )->with ( 'chartData', $chartData );
    }
    function statisticsExecTime($func, array $params, $xAxis) {
        set_time_limit ( 170 );
        $func_name = '';
        if (is_array ( $func )) {
            if (! method_exists ( $func [0], $func [1] )) {
                return false;
            }
            $func_name = object_name ( $func [0] ) . '->' . $func [1];
        } else if (is_string ( $func )) {
            if (! function_exists ( $func )) {
                return false;
            }
            $func_name = $func;
        } else if (is_callable ( $func )) {
            // if(! function_exists($func)){
            // return false;
            // }
            $func_name = 'Closure';
        } else {
            return false;
        }

        $mem = [ ];
        $time = [ ];
        foreach ( $params as $v ) {
            mark ( 'start' );
            	
            $result = call_user_func_array ( $func, ( array ) $v );
            	
            $time [] = floatval ( mark ( 'start', 'end' ) );
            $memory = memory_mark ();
            if (isset ( $memory ['start'] ) && isset ( $memory ['end'] )) {
                $mem [] = floatval ( memory_mark ( 'start', 'end' ) );
            }
            mark ( '[clear]' );
            memory_mark ( '[clear]' );
        }
        $data = [
            [
                'name' => 'Exec Time',
                'data' => $time
            ]
        ];
        $mem && $data [] = [
            'name' => 'Exec Memory',
            'data' => $mem
        ];
        $xAxis = [
            'categories' => $xAxis
        ];
        return chart ( $xAxis, $data, 'Function [' . htmlentities ( $func_name ) . '] Execute Time Statistics', 'At ' . date ( 'Y-m-d H:i:s' ), 'Number' );
    }
}