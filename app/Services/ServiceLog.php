<?php
namespace App\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogHandler;

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
        
        if (\App::environment('local','testing')) {
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
    
    
    public static function hitPatterns($patterns,$path)
    {
        foreach ($patterns as $pattern) {
            if ($pattern !== '/') {
                $pattern = trim($pattern, '/');
            }
            if (\Illuminate\Support\Str::is($pattern, $path)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Log a request.
     *
     * @param \Illuminate\Http\Request $request            
     * @param array $return            
     * @return mixed
     */
    public static function requestLog(\Illuminate\Http\Request $request, $return,$httpStatus = 200)
    {
        
        $defaultConfig = [
            'logRequestDB' => [
                'time_usage' => 0,
                'memory_usage' => 0,
            ],
            'logRequestRange' => [
                'only' => [
                    'api/*'
                ],
                //         'except' => []
            ]
        ];
        
        
        $onlyArray = \Config::get('app.logRequestRange.only',array_get($defaultConfig, 'logRequestRange.only'));
        $uri = $request->route()->getUri();
        // Process Only Settings
        if(!self::hitPatterns($onlyArray,$uri)){
            return;
        }
        
        $ret = is_array($return) ? $return : json_decode($return, 1);
        
        $data = array(
            'mt' => mt_mark('request-mt-start', 'request-mt-end', 'MB', 4),
            'refer' => $request->header('http-refer'),
            'ips' => $request->ips(),
            'uri' => $uri,
            'method' => $request->method(),
            'original' => $request->all(),
            'return' => $ret
        );
        mt_mark('[clear]');
        $params = $data['original'];
        
        $params = array_map(function ($v) {
            return is_string($v) ? (strlen($v) > 100 ? '...' : $v) : $v;
        }, $params);
        
        
        $sha1 = \App\Models\Common\RequestLog::calculateSha1($data['uri'],$params,$data['return']);
        
        $requestLogData = [
            'ip' => $data['ips'][0],
            'uri' => $data['uri'],
            'params' => json_encode($params),
            'http_status' => $httpStatus,
            'return' => json_encode($data['return']),
            'sha1' => $sha1,
            'time_usage' => isset($data['mt']['t']) ? $data['mt']['t'] : 0,
            'memory_usage' => isset($data['mt']['m']) ? $data['mt']['m'] : 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $logCondition = \Config::get('app.logRequestDB',array_get($defaultConfig, 'logRequestDB'));
        if ($requestLogData['time_usage'] >= $logCondition['time_usage']
             || $requestLogData['memory_usage'] >= $logCondition['memory_usage']) {
            \App\Models\Common\RequestLog::addRecord($requestLogData);
        }
        self::record('request', 'RQS', $data, 'request');
    }

    /**
     * 
     * @param string $name       The logging channel
     * @param unknown $message
     * @param array $context
     * @param string $dir
     */
    public static function record($name, $message, array $context = [], $dir = "request")
    {
        $filePath = storage_path() . "/{$dir}/" . $name;
        
        $log = new Logger($name);
        
        $logFileName = $filePath . '.' . date('Y-m-d');
        
        $log->pushHandler(new StreamHandler($logFileName, Logger::INFO));
        
        $log->addInfo($message, $context);

        @chown($logFileName, 'www:www');
        @chmod($logFileName, 0777);
	}
    
}