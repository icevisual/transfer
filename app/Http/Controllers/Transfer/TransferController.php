<?php
namespace App\Http\Controllers\Transfer;

use App\Http\Controllers\BaseController;

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
        $result = \App\Models\Transfer::getList($inputData,$page,$pageSize);
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
        }//www.xunshu.org/xunshu/22/22665/7844538.html
        $res = curl_post('http://fanyi.baidu.com/v2transapi', [
            'from' => 'en',
            'to' => 'zh',
            'query' => $query,
            'transtype' => 'realtime',
            'simple_means_flag' => '3',
        ]);
        $result = [];
        $data = json_decode($res,1);
        if(isset($data['dict_result']['simple_means']['symbols'][0])){
            $symbols = $data['dict_result']['simple_means']['symbols'][0];
            $result['[En]'] = '['.$symbols['ph_en'].' ]';
            $result['[Am]'] = '['.$symbols['ph_am'].' ]';
            //echo -e "\e[1;31m skyapp exist \e[0m"
            echo  PHP_EOL." [\e[1;31m{$query}\e[0m ]".PHP_EOL;
            if ($symbols['ph_en'])
                echo ' '."【英】[{$symbols['ph_en']} ],【美】[{$symbols['ph_am']} ]".PHP_EOL;
            foreach ($symbols['parts'] as $k => $v){
                $result['means'] [$k] = $v['part'];
                foreach ($v['means'] as $k1 => $v1){
                    $result['means'] [$k].= ($k1 ? ",":'').$v1;
                }
                echo ' '.$result['means'] [$k].PHP_EOL;
            }
            echo  PHP_EOL;
        }
        exit;
    }
    
    
    
    public function index(){
        
        
        
        
        return \View::make('Transfer.index');
    }
    
}



