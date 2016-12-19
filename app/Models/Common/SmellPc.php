<?php
namespace App\Models\Common;

class SmellPc extends \Eloquent
{

    protected $table = 'smell_pc';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;
    
    
    public static function getCatchFailedList($n = 10){
        $handle = self::select([
            'smell_pc.smell_id',
            'smell.cn_name',
        ])->join('smell','smell.id','=','smell_pc.smell_id')
            ->where('thumb','')
            ->orderBy('smell.id')
            ->limit($n)
            ->get();
        $ret = [];
        if($handle){
            $list = $handle->toArray();
            foreach ($list as $v){
                $ret[$v['smell_id']] = $v['cn_name'];
            }
        }
        return $ret;
    }

    
    public static function removePlaceholder($smell_id){
        return self::where('thumb','')
        ->where('smell_id',$smell_id)
        ->delete();
    }
    
    
    public static function addPlaceholder($smell_id){
        $now = time();
        $data = [
            'smell_id' => $smell_id,
            'thumb' => '',
            'height' => 0,
            'width' => 0,
            'category_id' => 0,
            'user_id' => 0,
            'created_at' => $now,
            'updated_at' => $now
        ];
        return self::create($data);
    }
    
    public static function groupAddSmellImg($smell_id,$data,$probably_name = ''){
        \DB::beginTransaction();
        foreach ($data as $v){
            self::addSmellImg($smell_id, $v['objURL'],$v['height'],$v['width'],$probably_name);
        }
        \DB::commit();
    }
    
    public static function addSmellImg($smell_id,$thumb,$height = 0 ,$width = 0 ,$probably_name = ''){
        $now = time();
        $data = [
            'smell_id' => $smell_id,
            'thumb' => $thumb,
            'height' => $height, 
            'width' => $width,
            'category_id' => 0,
            'user_id' => 0,
            'created_at' => $now,
            'updated_at' => $now
        ];
        $probably_name && $data['probably_name'] = $probably_name;
        return self::create($data);
    }
    
}