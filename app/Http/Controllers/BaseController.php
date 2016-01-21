<?php

namespace App\Http\Controllers;

use App\Services\ServiceClient;

class BaseController extends Controller
{

    public function __construct()
    {
        self::setHeader();
    }

    public static function setHeader()
    {

        $header ['Access-Control-Allow-Origin'] = '*';
        $header ['Access-Control-Allow-Methods'] = 'GET, PUT, POST, DELETE, HEAD, OPTIONS';
        $header ['Access-Control-Allow-Headers'] = 'X-Requested-With, Origin, X-Csrftoken, Content-Type, Accept';

        if ($header) {
            foreach ($header as $head => $value) {
                header("{$head}: {$value}");
            }
        }
    }

    public function __json($status = 200, $message = 'ok', array $data = [])
    {
        if ($data) {
            $data = ServiceClient::format($data);
        }
        $resp = [
            'status' => (int)$status,
            'message' => $message,
            'data' => $data
        ];
        return \Response::json($resp);
    }

    /**
     * 打印原生态sql
     * @access protected
     * @param boolean $needLog 是否要记录日志
     * @return array
     */
    protected function printRawSql($needLog = false){
    	$queries = \DB::getQueryLog();
    
    	$formattedQueries = [];
    	foreach( $queries as $query ) {
    		$prep = $query['query'];
    		foreach( $query['bindings'] as $binding ){
    			$prep = preg_replace("#\?#", $binding, $prep, 1);
    		}
    		$formattedQueries[] = $prep;
    	}
    
    	if($needLog) \Log::info($formattedQueries);
    
    	return $formattedQueries;
    }

}
