<?php
/**
 * Created by PhpStorm.
 * User: dryyun,唐佳琦
 * Email: dryyun@gmail.com
 * Date: 2015/4/10
 * Time: 12:57
 */
namespace App\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ServiceLog
{
	
	/**
	 * Log an error request.
	 * @param  \Illuminate\Http\Request $request
	 * @param  array $return
	 * @return mixed
	 */
	public static function errorLog(\Illuminate\Http\Request $request , $return){
	    $data = array(
	        'ips' => $request->ips(),
	        'url' => $request->fullUrl(),
	        'method' => $request->method(),
	        'return' => $return,
	        'original' => $request->all(),
	    );
	    self::record('exception', $data, 'request');
	}
	
	
	/**
	 * Log an request.
	 * @param  \Illuminate\Http\Request $request
	 * @param  array $return
	 * @return mixed
	 */
	public static function requestLog(\Illuminate\Http\Request $request , $return){
		$data = array(
		    'ips' => $request->ips(),
		    'url' => $request->fullUrl(),
		    'method' => $request->method(),
		    'return' => $return,
		    'original' => $request->all(),
		);
		self::record('request', $data, 'request');
	}
	
	
	
	
    public static function record($fileName, $fileData, $dir = "request")
    {
        $filePath = storage_path() . "/{$dir}/" . $fileName;

        $log = new Logger($fileName);

        $log->pushHandler(new StreamHandler($filePath . '.' . date('Y-m-d'), Logger::INFO));

        $log->addInfo($fileName, $fileData);

        @chmod($filePath . date('Y-m-d'), 0777);

    }
    
}