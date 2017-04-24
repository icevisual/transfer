<?php

namespace App\Models\Common;

use App\Exceptions\NeedRecordException;
use App\Models\BaseModel;

class RequestLog extends BaseModel
{
    protected $table = 'request_log';
    
    public $timestamps = false;
    
    public $guarded = [];

    
    public static function upgradeSha1(){
        \DB::beginTransaction();
        $data = self::all([
            'id','uri','params','return'
        ]);
        foreach ($data as $v){
            $sha1 = self::calculateSha1($v['uri'],json_decode($v['params'],1),json_decode($v['return'],1));
            
            self::where('id',$v['id'])->update([
                'sha1' => $sha1
            ]);
        }
        \DB::commit();
    }
    
    public static function calculateSha1(){
        $argvs = func_get_args();
        $data = [];
        foreach ($argvs as $v){
            if(!is_array($v)){
                $v = (array)$v;
            }
            ksort($v);
            $data[] = $v;
        }
        return sha1(json_encode($data));
    }
    
    
    public static function addRecord($inputData){
        $hashKeys = [
            'uri','params','return'  
        ];
        return self::create($inputData);
    } 
    

}