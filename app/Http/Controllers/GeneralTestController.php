<?php
namespace App\Http\Controllers;

class GeneralTestController extends BaseController
{
    public function __xmlToArray($xmlStr){
        
        $xml = simplexml_load_string($xmlStr);
        if ($xml === false) {
            throw new \App\Exceptions\ServiceException('Error Occured When Loading Xml From String');
        }
        $resultArray = [];
        $child = (array) $xml->children();
        $child = $child['content'];
        $i = 0;
        foreach ($xml->children() as $k => $v) {
//             contentuid
            $contentuid = '';
            foreach ($v->attributes() as $k1=>$v2){
                $contentuid = $v2.'';
            }
            $resultArray[$contentuid] = $child[$i++];
        }
        return $resultArray;
    }
    
    
    public function test()
    {
        
        edump(md5('jinyanlin123456'));
        //Input::get('pass') == 'jUT4GlZtq3tHoPUE')
        exit;
        $phones = file_get_contents('phones.txt');
        $arr = preg_split('/\s+/', $phones);
        $array = array_chunk ($arr,intval(count($arr) / 3)+ 1);
        $rse = [];
        foreach ($array as $k => $v){
            $key = '7WQRJOnYAQUYGVSzke';
            $phone = $v;
            $message = '仁仁提醒：近期同学反馈有不法分子在百度留下私人电话冒充仁仁客服，对方会谎称同学还款失败需要重新还款，并要求将款项打入私人账户！请同学们提高警惕，如有任何问题先拨打官方唯一客服电话4007800087或风控部电话13454129972咨询！';
            $params = array(
                'mobile' => implode(',', $phone),
                'message' => $message
            );
            $params ['pass'] = 'jUT4GlZtq3tHoPUE';
            $url = "https://secure.renrenfenqi.com/interface/crm/sms";
            $rse []= curl_post($url, $params);
            sleep(2);
        }
        
        $url = "https://gw.alicdn.com/tps/i3/TB1QeiDGFXXXXb8XVXXszjdGpXX-140-140.png?imgtag=avatar";
        
        
        $content =  file_get_contents($url);
        file_put_contents("TB1QeiDGFXXXXb8XVXXszjdGpXX-140-140.png", $content);
        exit;
        $content = file_get_contents('20160218');
        
        edump(detect_encoding($content));
        
        
        dump(__FUNCTION__);
        
        exit;
        $unit = new  \App\Gather\RPG\UnitBase('Unit001');
        
        $unit->showDiedMessage();
        
        exit;
        $str = 'showInjuredMessage';
        
        dump(preg_replace('/[A-Z]/', '#\\0', $str));
        
        edump(preg_split('/[A-Z]/', $str));
         
        
        
        exit;
        
        if (version_compare(PHP_VERSION, '5.3.6', '>=')) {
            list($current, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
        } else {
            list($current, $caller) = debug_backtrace(false,2);
        }
//         edump($backtrace);
//         list($current, $caller) = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        dump($current);
        edump($caller);
        
        \App\Gather\AESTool::main();
        
        exit;
        
        
        
        
        
        
        
        
        $url = 'http://www.xunshu.la/22_22665/7813282.html';
        
        
        
        
        
        
        
        $word = \Input::get('w');
//         if(!$word){
//             echo 'Word Is Required!';
//             exit();
//         }
        $sourceData =  \App\Models\Transfer::all()->toArray();
        $transferData = [];
        $file = 'yun.xml';
        $content = file_get_contents($file);
        $resEN =  $this->__xmlToArray($content);
        $n = 0;
        \DB::beginTransaction();
        foreach ($sourceData as $v){
         
            if($v['status'] ==  0 
                && $v['eng'] != $resEN[$v['uid']] 
                ){
                $str = html_entity_decode( $resEN[$v['uid']]);
                $r =  preg_replace('/<[^>]*>/','', $str);
                preg_match_all('/[a-zA-Z]/i', $r ,$metchs);
                if(count($metchs[0]) >= 1){
                    continue;
                }
                echo $n.'[En]'.$v['eng'].'<br/>';
                echo $n.'[Ch]'.$resEN[$v['uid']].'<br/>';
                $n ++;
                \App\Models\Transfer::where('id',$v['id'])->update([
                    'status' => 2,
                    'chi' => htmlspecialchars($resEN[$v['uid']]) ,
                ]);
            }
        }
        \DB::commit();
        exit;
        foreach ($sourceData as $v){
            $transferData[$v['contentuid']] = $v;
            $reg = '/'.$word.'/i';
            if($word && preg_match($reg, $v['eng'])){
                $str = preg_replace($reg, '<font color="red">\\0</font>', $v['eng']);
                echo '[EN]:'.$str .'<br/>';
                echo '[CH]:'. ($v['status'] == 1 ? $v['chi'] : '<textarea></textarea>' ) .'<br/>';
            }
        }

//         dump($transferData);

        exit;
        
        exit;
        $fbsdk = \App\Services\Merchants\FBSdkService::getInstance();
        
        $file = 'english.xml';
        $content = file_get_contents($file);
        $resEN =  $this->__xmlToArray($content);
        $file = 'hanhua.xml';
        $content = file_get_contents($file);
        $resCH =  $this->__xmlToArray($content);
        
        
//         $fp = fopen('result.xml', 'w');
//         fputs($fp, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?\>
// <contentList>'.PHP_EOL);
        
//         $fp1 = fopen('eng.xml', 'w');
//         fputs($fp1, '<?xml version="1.0" encoding="UTF-8" standalone="yes"?\>
// <contentList>'.PHP_EOL);
        $word = \Input::get('w');
        
        if(!$word){
            echo 'Word Is Required!';
            exit();
        }
        
        set_time_limit(0);
        $fromEn = 0;
        $fromCh = 0;
        $length = 0;
        
        
        $insertData = [];
        
        foreach ($resEN as $k => $v){
            if(strlen($v) > $length ) $length = strlen($v);
            
//             $reg = '/'.$word.'/i';
//             if(preg_match($reg, $resEN[$k])){
//                 $resEN[$k] = preg_replace($reg, '<font color="red">\\0</font>', $resEN[$k]);
//                 echo ''.$resEN[$k] .'--'. ( isset($resCH[$k]) ? $resCH[$k] : '<textarea></textarea>' ) .'<br/>';
//             }
            
            
            if(isset($resCH[$k])){
//                 $resEN[$k] = $resCH[$k];
                $fromCh ++ ;
                $record = [
                    'uid' => $k,
                    'eng' => $resEN[$k],
                    'chi' => $resCH[$k],
                    'status' => 1,
                ];
            }else{
                $resEN[$k] = htmlspecialchars($resEN[$k]);
                $fromEn ++ ;
                $str = "\t<content contentuid=\"{$k}\">{$resEN[$k]}</content>".PHP_EOL;
//                 if(strlen($resEN[$k]) < 20)
//                 echo htmlspecialchars($resEN[$k]).'<br/>';;
                
//                 fputs($fp1, $str);
                $record = [
                    'uid' => $k,
                    'eng' => $resEN[$k],
                    'chi' => '',
                    'status' => 0,
                ];
            }
            $resEN[$k] = htmlspecialchars($resEN[$k]);
            $str = "\t<content contentuid=\"{$k}\">{$resEN[$k]}</content>".PHP_EOL;
//             fputs($fp, $str);
            $insertData [] = $record;
            
            if(count($insertData) > 100 ){
                \App\Models\Transfer::insert($insertData);
                $insertData = [];
            }
            
        }
        
        $insertData && \App\Models\Transfer::insert($insertData);
        
//         fputs($fp, '</contentList>');
//         fputs($fp1, '</contentList>');
//         fclose($fp);
//         fclose($fp1);
        dump('From English:'.$fromEn);
        dump('From Chinese:'.$fromCh);
        dump($length);
        exit;
        edump($resEN);
        
        
//         http://fanyi.baidu.com/v2transapi
        
//         from:en
//         to:zh
//         query:Invariant
//         transtype:realtime
//         simple_means_flag:3
        
        
        $res = curl_post('http://fanyi.baidu.com/v2transapi', [
            'from' => 'en',
            'to' => 'zh',
            'query' => 'realtime',
            'transtype' => 'realtime',
            'simple_means_flag' => '3',
        ]);
        edump(json_decode($res,1));
        
    }


    public function printTableView($str, $kv = true)
    {
        $result = $this->tableViewArray($str, $kv);
        preArrayKV($result);
        exit();
    }

    public function tableViewToArrayAnn($str)
    {
        $array = $this->tableViewArray($str, 1, 0);
        
        echo '<pre>';
        array_walk($array, function ($v, $k) {
            echo sprintf("'%s' => '' ,// %s\n", $k, $v);
        });
        echo '</pre>';
    }

    /**
     *
     * @param unknown $str            
     * @param string $kv
     *            $k => $v
     * @param string $ks
     *            ksort
     * @return boolean|multitype:string
     */
    public function tableViewArray($str, $kv = true, $ks = true)
    {
        $res = explode("\r", $str);
        // edump($res);
        $res = array_filter($res, function ($v) {
            return trim($v) != '';
        });
        $result = [];
        foreach ($res as $k => $v) {
            $v = trim($v);
            $vv = preg_split("/\s/", $v, 2);
            // edump($vv);
            $kv && $result[trim($vv[0])] = trim($vv[1]);
            $kv === false && $result[trim($vv[1])] = trim($vv[0]);
        }
        $ks && ksort($result);
        return $result;
    }

    public function resultExampleAnn($resultExample, $ann)
    {
        echo '<pre>';
        foreach ($resultExample as $k => $v) {
            echo " '$k' => '$v',//" . (isset($ann[$k]) ? $ann[$k] : 'UNKNOWN') . " " . PHP_EOL;
        }
        echo '</pre>';
    }

    public function printTableViewAnn($str, $kv = true)
    {
        $result = $this->tableViewArray($str, $kv);
        preArrayKV($result);
        exit();
    }
    
    
    public function test1(){

        
        \App\Models\McpayDetail::importFromLog();
        exit;
        
        \App\Models\FbsdkLog::create([]);
        dump(\App\Models\FbsdkLog::create([],true));
        exit;
        $fbsdk = \App\Services\Merchants\FBSdkService::getInstance();
        
        //DCPAYMNT
        //NTIBCOPR
        
        $logs =  \App\Models\FbsdkLog::where('func_name','DCPAYMNT')
        ->orWhere('func_name','NTIBCOPR')->get()->toArray();
        
        
        $dataKey = [
            'DCPAYMNT' => ['DCOPDPAYX','NTQPAYRQZ'],
            'NTIBCOPR' => ['NTIBCOPRX','NTOPRRTNZ'],
        ];
        
        
        foreach ($logs as $k => $v){
            if($v['send_status']){
                $log_id = $v['id'];
                $funcname = $v['func_name'];
                $sendData = $fbsdk->__xmlToArray(iconv( 'UTF-8', 'GBK',$v['send_xml']));
                $receiveData = $fbsdk->__xmlToArray(iconv( 'UTF-8', 'GBK',$v['received_xml']));
                //DCOPDPAYX NTQPAYRQZ
                //NTIBCOPRX NTOPRRTNZ 
                
                $send = isset($sendData[$dataKey[$funcname][0]]) ? $sendData[$dataKey[$funcname][0]] :[];
                $receive = isset($receiveData[$dataKey[$funcname][1]]) ? $receiveData[$dataKey[$funcname][1]] :[];
                
                \App\Models\McpayDetail::record(
                    $log_id, $funcname, 
                    $send, 
                    $receive
                );
            }
        }
        
        
        esqlLastSql();
        
        exit;
        
        
        
        
        edump($_SERVER);
        
        $v = iconv('utf8', 'iso-8859-1', "sdsd代发");
        header("Content-Type: text/xml;encoding=utf-8");
        echo utf8_decode(wddx_serialize_value($v));
        exit;
  
        edump(get_class());
        echo false;
        
        $a = 'asd';
        
        exit;
        get_class();
        
        $str =<<<qw
asdasd
qw;
        
        $str = <<<'EOT'
My name is "$name". I am printing some $foo->foo.
Now, I am printing some {$foo->bar[1]}.
This should not print a capital 'A': \x41
EOT;
        
        edump($str);
        dump(decbin(12));
        dump(floor((0.1+0.7)*10) );
         dump( ((0.1+0.7) *10) );
        var_dump(010120);
        
        exit;
    }
}



