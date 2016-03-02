<?php
if (! function_exists('curl_get')) {

    
    
    function call_curl_reconnect($url, array $data = [], $json = true){
        
        $t_count = 10;
        $count = $t_count;
        while ($count){
            $result = curl_get($url,$data,$json);
            if($result ){
                return $result;
            }
            $slpSecd = intval(sqrt($t_count - $count) * 1000000);
            dump($slpSecd);
            usleep($slpSecd);
//             sleep($seconds);
            $count --;
        }
    }
    
    
    
    function curl_get($url, array $data = [], $json = true)
    {
        // $api = 'http://v.showji.com/Locating/showji.com20150416273007.aspx?output=json&m='.$phone;
        $ch = curl_init();
        if (! empty($data)) {
            $url = $url . '?' . http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 设置超时
                                              // curl_setopt($ch, CURLOPT_USERAGENT, $User_Agen); //用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 跟踪301
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 返回结果
        $info = curl_exec($ch);
        // echo curl_errno($ch);
        // echo curl_error($ch);
        curl_close($ch);
        return $json ? json_decode($info, 1) : $info;
    }

    function curl_post($url, array $data, $json = true)
    {
//         $cookie_file = 'cookie.txt';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // url
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
        curl_setopt($ch, CURLOPT_USERAGENT, $User_Agen);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
//         curl_setopt($ch, CURLOPT_COOKIEJAR,  $cookie_file); //存储cookies
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // 数据
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch);
        curl_close($ch);
        return $json ? json_decode($info, 1) : $info;
    }

    function curl_multi_request($query_arr, $data, $method = 'POST')
    {
        $ch = curl_multi_init();
        $count = count($query_arr);
        $ch_arr = array();
        for ($i = 0; $i < $count; $i ++) {
            $query_string = $query_arr[$i];
            $ch_arr[$i] = curl_init($query_string);
            curl_setopt($ch_arr[$i], CURLOPT_RETURNTRANSFER, true);
            
            curl_setopt($ch_arr[$i], CURLOPT_POST, 1);
            curl_setopt($ch_arr[$i], CURLOPT_POSTFIELDS, $data); // post 提交方式
            
            curl_multi_add_handle($ch, $ch_arr[$i]);
        }
        $running = null;
        do {
            curl_multi_exec($ch, $running);
        } while ($running > 0);
        for ($i = 0; $i < $count; $i ++) {
            $results[$i] = curl_multi_getcontent($ch_arr[$i]);
            curl_multi_remove_handle($ch, $ch_arr[$i]);
        }
        curl_multi_close($ch);
        return $results;
    }
}

if (! function_exists('__fsocket')) {

    /**
     * Request without response using curl
     *
     * @param unknown $url            
     * @param array $data            
     * @param string $host            
     * @param string $method            
     * @return boolean
     */
    function __async_curl($url, array $data = [], $host = '', $method = 'GET')
    {
        $host = $host ? $host : $_SERVER['HTTP_HOST'];
        $ch = curl_init();
        $url = $data ? $host . $url . '?' . http_build_query($data) : $host . $url;
        $curl_opt = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 1
        );
        curl_setopt_array($ch, $curl_opt);
        $res = curl_exec($ch);
        curl_close($ch);
        return true;
    }

    function __fsocket_get($url, array $param = [], $host = '')
    {
        return __fsocket($url, $param, $host, 'GET');
    }

    function __fsocket_post($url, array $param = [], $host = '')
    {
        return __fsocket($url, $param, $host, 'POST');
    }

    /**
     * Request without response using socket
     *
     * @param unknown $url            
     * @param array $param            
     * @param string $host            
     * @param string $method            
     */
    function __fsocket($url, array $param = [], $host = '', $method = 'POST')
    {
        $host = $host ? $host : $_SERVER['HTTP_HOST'];
        $fp = fsockopen($host, '80', $errno, $errstr, 30);
        $data = http_build_query($param);
        if ($method == 'POST') {
            $out = "POST ${url} HTTP/1.1\r\n";
            $out .= "Host:${host}\r\n";
            $out .= "Content-type:application/x-www-form-urlencoded\r\n";
            $out .= "Content-length:" . strlen($data) . "\r\n";
            $out .= "Connection:close\r\n\r\n";
            $out .= "${data}";
        } else {
            $url = $data ? $url . '?' . $data : $url;
            $out = "GET $url HTTP/1.1\r\n";
            $out .= "Host: $host\r\n";
            $out .= "Connection: Close\r\n\r\n";
        }
        stream_set_blocking($fp, 0); // 开启了手册上说的非阻塞模式
                                     // stream_set_timeout($fsp,1);//设置超时
        fwrite($fp, $out);
        // $row = fread($fp, 4096);
        
        // while (!feof($fp)) {
        // echo fgets($fp, 128);
        // }
        
        usleep(1000);
        fclose($fp);
    }

}

if (! function_exists('qishu')) {

    function qishu($curl = '')
    {
        $url = "http://www.xuanshu.com";
        $curl = $url . $curl;
        
        $html = call_curl_reconnect($curl, [], false); //
        
//         $html = http_fsocket($curl,[],'GET'); //
        
        
                                            // $html = file_get_contents($curl);
                                            // $html = iconv("gb2312", "utf-8//IGNORE",$html);
        $html = preg_replace('/target="_blank"/i', '', $html);
        $html = preg_replace('/<script.*<\/script>/i', '', $html);
        // <a href="/32848.html" style="color:default" class="name">《传奇控卫》全集</a>
        $html = preg_replace('/(<a href=")(\/\d*.html)("[^>]*>《)(.*)(》全集<\/a>)/i', '\\1http://dzs.qisuu.com/txt/\\4.txt\\3\\4\\5', $html);
        $html = preg_replace('/(<a href=")(\/\d*.html)("><img src="[:\-\d\w\/\.]*">《)(.*)(》全集<\/a>)/i', '\\1http://dzs.qisuu.com/txt/\\4.txt\\3\\4\\5', $html);
        // <a class="downButton" href="http://dzs.qisuu.com/txt/都市全能系统.txt" title="《都市全能系统》全集txt下载">Txt格式下载</a>
        $html = preg_replace('/(<link href=")([\w\.\/]*)(".*\/>)/i', '\\1' . $url . '/' . '\\2\\3', $html);
        // edump($str);
        $html = preg_replace('/(<img src=")([\w\.\/]*)/i', '\\1' . $url . '/' . '\\2', $html);
        // <img src="/skin/blue/logo.png"
        
        return $html;
    }
}










    
    
