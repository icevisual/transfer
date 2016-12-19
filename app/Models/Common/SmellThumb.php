<?php
namespace App\Models\Common;

class SmellThumb extends \Eloquent
{

    protected $table = 'smell_thumb';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;
    
    
    /**
     * 
     * @param unknown $id
     * @param unknown $data
     * [
            'down_status' => '',
            'localpath' => '',
            'height' => '',
            'width' => '',
        ];
     */
    public static function updateDown($id,$data){
//         $data = [
//             'down_status' => '',
//             'localpath' => '',
//             'height' => '',
//             'width' => '',
//         ];
        self::where('id',$id)->update($data);
    }
    
    public static function getUnprocessedList($n = 100,$min = 0,$max = 0){
        $handle = self::where('down_status',0)
        ->limit($n);
        if($min){
            $handle->where('id','>=',$min);
        }
        if($max){
            $handle->where('id','<',$max);
        }
        $handle = $handle->get();
        $ret = [];
        if($handle){
            return $list = $handle->toArray();
        }
        return $ret;
    }
    
    
}