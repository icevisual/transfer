<?php

if (! function_exists ( 'S' )) {
    function S($data) {
        $data = ( array ) $data;
        $debug_data = debug_backtrace ();
        $sdata ['file'] = pathinfo ( $debug_data [0] ['file'] )['filename'];
        if (isset ( $debug_data [1] ) && isset ( $debug_data [1] ['function'] )) {
            $sdata ['file'] .= ' F:' . $debug_data [1] ['function'];
        }
        $sdata ['file'] .= ' L:' . $debug_data [0] ['line'];
        $sdata ['data'] = $data;
        \Ser\LogService::record ( 'P', $sdata, 'logs' );
    }
}





if (! function_exists ( 'getReturnInLogFile' )) {

    

    /**
     * 读取日志内的接口调用记录
     *
     * @param unknown $fileRealPath
     * @param string $url
     * @return multitype:Ambigous <> |boolean|multitype:|multitype:Ambigous <Ambigous> |Ambigous <number, multitype:, multitype:Ambigous , multitype:multitype:unknown string >
     */
    function readMonoLogFile($fileRealPath, $url = '') {
        static $returns = [ ];
        static $loaded = [ ];

        if ($url) {
            if (isset ( $returns [$url] )) {
                return [
                    $url => $returns [$url]
                ];
            }
            if (isset ( $loaded [$fileRealPath] )) {
                return false;
            }
        } else {
            if (isset ( $loaded [$fileRealPath] )) {
                return $returns;
            } else {
                $loaded [$fileRealPath] = true;
            }
        }

        $filelines = [ ];
        file_exists ( $fileRealPath ) && $filelines = file ( $fileRealPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );
        $HTTP_HOST = '';
        $keys = [
            'status',
            'message',
            'data'
        ];
        foreach ( $filelines as $key => $line ) {
            preg_match ( '/\{.*\}/', $line, $matchs );
            	
            if ($matchs) {
                $matchs = json_decode ( $matchs [0], true );

                if (json_last_error () == JSON_ERROR_NONE && isset ( $matchs ['Url'] ) && isset ( $matchs ['func_num_args'] ) && (! $url || endsWith ( $matchs ['Url'], $url ))) {
                    if (! $HTTP_HOST) { // Get Host Name
                        preg_match ( '/^http(:?s)?:\/\/[^\/]*/', $matchs ['Url'], $mh );
                        if ($mh) {
                            $HTTP_HOST = $mh [0];
                        }
                    }
                    	
                    $matchs ['Url'] = substr ( $matchs ['Url'], stripos ( $matchs ['Url'], $HTTP_HOST ) + strlen ( $HTTP_HOST ) );
                    	
                    $matchs ['Url'] = '/' . ltrim ( $matchs ['Url'], '/' );
                    	
                    $ret = $matchs ['func_num_args'];
                    count ( $ret ) == 2 && $ret [] = [ ];
                    $ret = array_combine ( $keys, $ret );
                    // /Filter When Return Data Contains [object]
                    // [object] (User\Account: {"uid":159007,"password":"","salt":"","account_status":0,"my_code":"031077"})
                    // array_map_recursive
                    $ret = json_decode_recursive ( $ret );
                    	
                    $ret = array_clear ( $ret );
                    	
                    // TODO :Remove Large Return
                    if (isset ( $returns [$matchs ['Url']] )) {
                        $returns [$matchs ['Url']] ['Times'] ++;
                        /**
                         * 补全返回信息
                         */
                        if (isset ( $ret ['status'] ) && ! isset ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] )) {
                            $returns [$matchs ['Url']] ['Return'] [$ret ['status']] = $ret;
                        } else if (isset ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] )) {
                            // TODO :Complete Return Info
                            // Complete Input Data
                            if (! isset ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] ['data'] )) {
                                // 								edump ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] );
                                continue;
                            }
                            	
                            if (is_array ( $ret ['data'] )) {

                                $returns [$matchs ['Url']] ['Return'] [$ret ['status']] ['data'] = array_filter ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] ['data'], function ($v) {
                                } ) + $ret ['data'];
                                ksort ( $returns [$matchs ['Url']] ['Return'] [$ret ['status']] ['data'] );
                            }
                            	
                            // isset($returns[$matchs['Url']]['Return'][$ret['status']]['data'] ) &&
                        }
                        $returns [$matchs ['Url']] ['Params'] = array_filter ( $returns [$matchs ['Url']] ['Params'] ) + $matchs ['Input'];
                    } else {
                        if (isset ( $matchs ['Input'] )) {
                            // Add Input And Return
                            $returns [$matchs ['Url']] = [
                                'Times' => 1,
                                'Url' => $matchs ['Url'],
                                'Params' => $matchs ['Input'],
                                'Method' => $matchs ['Method']
                            ];
                            if (isset ( $ret ['status'] )) {
                                $returns [$matchs ['Url']] ['Return'] [$ret ['status']] = $ret;
                            }
                        } else {
                            // TODO :Error Handler
                            // echo 'Input Field Not Found<br/>';
                            // return false;
                        }
                    }
                } else {
                    // echo 'Line '.$key.' Can\'t Be Json Or Can\'t Find Url<br/>';
                    // return false;
                }
            }
        }

        if ($url) {
            if (isset ( $returns [$url] )) {
                return [
                    $url => $returns [$url]
                ];
            } else
                return false;
        }
        return $returns;
    }

    /**
     * Analysis Log File In laravel (MonoLog)
     */
    function getApiInvokingLog($api) {
        $dir = 'logs';
        $fileName = 'ReqLogs';

        $filePath = storage_path () . "/{$dir}/" . $fileName;
        $t = 0;

        $result = false;
        for($i = 0; $i < 10; $i ++) {
            $fileRealPath = $filePath . date ( 'Y-m-d', strtotime ( "-{$i} days" ) );
            // if(mt_rand(0,10) > 7 &&
            file_exists ( $fileRealPath ) && readMonoLogFile ( $fileRealPath );
            if (file_exists ( $fileRealPath )) {
                $res = readMonoLogFile ( $fileRealPath, $api );
                if ($res !== false) {
                    $t ++;
                    $result = $res;
                }
                if ($t >= 2) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Analysis Log File In laravel (MonoLog)
     */
    function getReturnInLogFile($dir, $fileName, $last = 0, $url = '') {
        $filePath = storage_path () . "/{$dir}/" . $fileName;
        $i = $last;
        while ( ! file_exists ( $fileRealPath = $filePath . date ( 'Y-m-d', strtotime ( "-{$i} days" ) ) ) && ++ $i && $i < 5 )
            ;
            // echo $fileRealPath;
            if (file_exists ( $fileRealPath )) {
                return readMonoLogFile ( $fileRealPath, $url );
            }
    }
    // 	function endsWith($haystack, $needles) {
    // 		foreach ( ( array ) $needles as $needle ) {
    // 			if (( string ) $needle === substr ( $haystack, - strlen ( $needle ) ))
        // 				return true;
        // 		}

        // 		return false;
        // 	}
}
