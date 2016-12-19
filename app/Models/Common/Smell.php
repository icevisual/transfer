<?php
namespace App\Models\Common;

class Smell extends \Eloquent
{

    protected $table = 'smell';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    public static function getUnCatchedList($n = 1){
        $handle = self::select(['smell.id','smell.cn_name'])->leftJoin('smell_pc', 'smell.id','=','smell_pc.smell_id')
        ->whereNull('smell_pc.id')
        ->orderBy('smell.id')
        ->limit($n)
        ->get();
        $ret = [];
        if($handle){
            $list = $handle->toArray();   
            foreach ($list as $v){
                $ret[$v['id']] = $v['cn_name'];
            }
        }
        return $ret;
    }
    
    
    public static function imgSelectQueue($page = 1,$pageSize = 100){
        $sp = '#T#';
        $handler = self::select([
            'smell.id',
            'smell_pc.id AS pc_id',
            'smell.cn_name',
            'smell_pc.thumb',
            'smell_pc.height',
            'smell_pc.width',
            'smell_pc.probably_name',
//             \DB::raw("GROUP_CONCAT(sm_smell_pc.thumb, '{$sp}')")
        ])->Join('smell_pc', 'smell.id','=','smell_pc.smell_id')
        ->groupBy('smell.id')
        ->orderBy('smell.id');
        
        $paginate = $handler->paginate($pageSize, [
            '*'
        ], 'page', $page);
        $list = $paginate->toArray();
        $data = [
            'total' => $list['total'],
            'current_page' => $list['current_page'],
            'last_page' => $list['last_page'],
            'per_page' => $list['per_page'],
            'list' => $list['data'],
            'render' => $paginate->render()
        ];
        
        return $data;
    }
    
    
    public static function smellSearch($keyword,$page = 1,$pageSize = 100){
        $handler = self::select([
            'smell.id',
            'smell.en_name',
            'smell.cn_name',
            \DB::raw('CONCAT("/thumb/",sm_smell_thumb.localpath) AS thumb'),
//             'smell_thumb.localpath AS thumb',
        ])->Join('smell_thumb', 'smell.id','=','smell_thumb.id')
        ->where('smell.cn_name','like','%'.$keyword.'%');
    
        $paginate = $handler->paginate($pageSize, [
            '*'
        ], 'p', $page);
        $list = $paginate->toArray();
        $data = [
            'total' => $list['total'],
            'current_page' => $list['current_page'],
            'last_page' => $list['last_page'],
            'per_page' => $list['per_page'],
            'list' => $list['data'],
        ];
        return $data;
    }
    
    
    public static function smellSearch1($keyword,$page = 1,$pageSize = 100){
        $handler = self::select([
            'smell.id',
            'smell.en_name',
            'smell.cn_name',
            \DB::raw('CONCAT("/thumbext/",sm_smell_pc.thumb) AS thumb'),
            //             'smell_thumb.localpath AS thumb',
        ])->Join('smell_pc', 'smell.id','=','smell_pc.id')
        ->where('smell.cn_name','like','%'.$keyword.'%');
    
        $paginate = $handler->paginate($pageSize, [
            '*'
        ], 'p', $page);
        $list = $paginate->toArray();
        $data = [
            'total' => $list['total'],
            'current_page' => $list['current_page'],
            'last_page' => $list['last_page'],
            'per_page' => $list['per_page'],
            'list' => $list['data'],
        ];
        return $data;
    }
    
    
    
}