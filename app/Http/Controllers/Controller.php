<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    
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
    
    public function __json($status = 200, $message = 'ok', array $data = [],$options = 0)
    {
        header("Content-type: application/json");
        if (is_array($status)) {
            return \Response::json($status);
        }
        $resp = [
            'status' => (int)$status,
            'message' => $message,
            'data' => $data
        ];
        return \Response::json($resp,200, array(), $options);
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
