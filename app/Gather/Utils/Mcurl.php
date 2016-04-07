<?php


namespace App\Services\Merchants;

class Mcurl {
    
    protected $mh;

    protected $urls;
    
    protected $data;
    
    public function __construct(array $urls = [],array $data = []){
        // TODO Url tags
        $this->urls = $urls;
        
        $this->data = $data;
    }
    
    public function addRequest($url,$data = null){
        
        $this->urls[] = $url;
        $this->data[] = $data;
    }
    
    
    public function full_curl_multi_exec($mh, &$still_running) {
        do {
            $rv = curl_multi_exec($mh, $still_running);
        } while ($rv == CURLM_CALL_MULTI_PERFORM);
        return $rv;
    }
    
    
    public function run(callable $callback = null){
        if($this->urls){
            $this->__init();
            
            return $this->__request($callback);
        }
        return false;
    }
    
    
    public function getReqData($index){
        return isset($this->data[$index]) ? $this->data[$index] : null;
    }
    
    public function __init(){
        
        $mh = curl_multi_init();
        $curl_array = array();
        $urls = $this->urls;
        $data = $this->data;
        foreach($urls as $i => $url)
        {
            $ch = curl_init($url);
            if ($reqData = $this->getReqData($i)){
                if(is_array($reqData)){
                    $reqData = http_build_query($reqData);
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $reqData); // 数据
            }
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            //             curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            //             curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            //             curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            //             curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $curl_array[$i] = $ch;
            curl_multi_add_handle($mh, $curl_array[$i]);
        }
        $this->mh = $mh;
    }
    
    
    public function __request(callable $callback = null){
        $mh = $this->mh;
        $res = [];
        $still_running = null;
        $this->full_curl_multi_exec($mh, $still_running); // start requests
        do { // "wait for completion"-loop
            curl_multi_select($mh); // non-busy (!) wait for state change
            $this->full_curl_multi_exec($mh, $still_running); // get new state
            while ($info = curl_multi_info_read($mh)) {
                $curl_info = curl_getinfo($info['handle']);
                $curl_content = curl_multi_getcontent($info['handle']);
                
//                 curl_error($info['handle']);
//                 curl_errno($info['handle']);
                
                if($callback){
                    $res [] = call_user_func_array($callback,[$curl_content,$curl_info]);
                }else{
                    $res [$curl_info['url']] = $curl_content;
                }
                curl_multi_remove_handle($mh,$info['handle']);
                // process completed request (e.g. curl_multi_getcontent($info['handle']))
            }
        } while ($still_running);
        curl_multi_close($mh);
        return  $res;
    }
    
    
    
}



