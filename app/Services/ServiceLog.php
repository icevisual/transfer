<?php

namespace App\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ServiceLog
{

    /**
     * Log Update & Insert Sql Sentence
     * 
     * @param unknown $sql            
     * @param unknown $bindings            
     * @param unknown $time            
     * @return string
     */
    public static function sqlLog($sql, $bindings, $time)
    {
        $i = 0;
        $bindings && $sql = preg_replace_callback('/\?/', function ($matchs) use(&$i, $bindings) {
            return '\'' . $bindings[$i ++] . '\'';
        }, $sql);
        if (strpos($sql, 'update') === 0) {
            self::record('sql', $sql, [], 'sqlLog');
        }
        
        if(\App::environment('local')){
            if (strpos($sql, 'insert') === 0) {
                self::record('sql', $sql, [], 'sqlLog');
            }
            if (strpos($sql, 'select') === 0) {
                self::record('sql', $sql, [], 'sqlLog');
            }
        }
    }

    /**
     * Log an error request.
     * 
     * @param \Illuminate\Http\Request $request            
     * @param array $return            
     * @return mixed
     */
    public static function errorLog(\Illuminate\Http\Request $request, $return)
    {
        $data = array(
            'ips' => $request->ips(),
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'return' => $return,
            'original' => $request->all()
        );
        self::record('exception', 'RQS', $data, 'request');
    }

    /**
     * Log an request.
     * 
     * @param \Illuminate\Http\Request $request            
     * @param array $return            
     * @return mixed
     */
    public static function requestLog(\Illuminate\Http\Request $request, $return)
    {
        $data = array(
            'mt' => mt_mark('request-mt-start','request-mt-end','MB',4),
            'refer' => $request->header('http-refer'),
            'ips' => $request->ips(),
            'uri' => $request->route()->getUri(),
            'method' => $request->method(),
            'original' => $request->all(),
            'return' => $return,
        );
        $params = $data['original'];
        
        
        $params = array_map(function ($v){
            return is_string($v) ? (strlen($v) > 40 ? '...':$v) : $v;
        }, $params);
        
        
        $requestLogData = [
            'ip' => $data['ips'][0],
            'uri' => $data['uri'],
            'params' => json_encode($params),
            'time_usage' => isset($data['mt']['t']) ? $data['mt']['t'] : 0,
            'memory_usage' => isset($data['mt']['m']) ? $data['mt']['m'] : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        \DB::insert(createInsertSql(\DB::getTablePrefix().'request_log', $requestLogData));
        self::record('request', 'RQS', $data, 'request');
    }

    public static function record($fileName, $message, array $logData = [], $dir = "request")
    {
        $filePath = storage_path() . "/{$dir}/" . $fileName;
        
        $log = new Logger($fileName);
        
        $log->pushHandler(new StreamHandler($filePath . '.' . date('Y-m-d'), Logger::INFO));
        
        $log->addInfo($message, $logData);
        @chown($filePath . date('Y-m-d'), 'www:www');
        @chmod($filePath . date('Y-m-d'), 0777);
	
	}
    
}