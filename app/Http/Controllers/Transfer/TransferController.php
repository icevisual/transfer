<?php
namespace App\Http\Controllers\Transfer;

use App\Http\Controllers\BaseController;
use App\Models\Common\Transfer;

class TransferController extends BaseController{
    
    
    public function put(){
        
        return $this->__json(200,'put ok');
        
    }
    
    
    /**
     * 专用名词查询
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(){
        $eng = \Input::get('key');
        $page = \Input::get('page');
        $pageSize = \Input::get('pagesize');
        $highlight = \Input::get('highlight');
        $status = \Input::get('status');
        
        
        $page || $page = 1;
        $pageSize || $pageSize = 20;
        $highlight && $highlight = 1;
        $inputData = get_defined_vars();
        runValidator($inputData, [
            'eng' => 'required',
            'page' => 'required|numeric',
            'pageSize' => 'required|numeric',
            'status' => 'sometimes|numeric',
        ]);
        $result = Transfer::getList($inputData,$page,$pageSize);
        $result['_status'] = [
            '0 ' => '未翻译',
            '1 ' => '已翻译',
            '2 ' => '人工已翻译',
        ];
        return $this->__json(200,'ok',$result,JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES);
    }
    
    /**
     * 百度翻译
     */
    public function translate(){
        $query = \Input::get('w');
        if(!$query){
            echo 'Word Is Required'.PHP_EOL;
            exit;
        }
        echo translate($query);;//iconv('UTF-8', 'GB2312', $resStr);
        exit;
    }
    
    
    
    public function index(){
        
        return \View::make('Transfer.index');
    }
    
}



