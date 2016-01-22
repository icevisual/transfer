<?php
namespace App\Models;

class Transfer extends \Eloquent
{

    protected $table = 'transfer';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public $timestamps = false;

    public static function getList($search = [], $page = 1, $pageSize = 20)
    {
        $handler = self::select([
            'uid',
            'eng',
            'chi',
            'status',
        ])->orderBy('id', 'asc');
        
        if (isset($search['eng']) && $search['eng']) {
            $handler->whereRaw('binary eng like "%'.$search['eng'].'%" ');
        }
        
        if (isset($search['status']) ) {
            $handler->where('status',$search['status']);
        }
        
//         if (isset($search['start_date']) && $search['start_date']) {
//             $handler->where('company_bill.created_at', '>=', $search['start_date']);
//         }
//         if (isset($search['end_date']) && $search['end_date']) {
//             $handler->where('company_bill.created_at', '<=', $search['end_date']);
//         }
        $paginate = $handler->paginate($pageSize, [
            '*'
        ], 'page', $page);
        $list = $paginate->toArray();
        
        
        if($search['highlight']){
            foreach ($list['data'] as $k => $v) {
                $list['data'][$k]['eng'] = preg_replace('/'.$search['eng'].'/', '<font color=\'red\'>\\0</font>', $v['eng']);;
            }
        }

        $data = array(
            'total' => $list['total'],
            'current_page' => $list['current_page'],
            'last_page' => $list['last_page'],
            'per_page' => $list['per_page'],
            'list' => $list['data']
        );
        return $data;
    }
    
    
}