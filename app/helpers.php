<?php



if(!function_exists('qishu')){

    function qishu($curl = ''){
        $url = "http://www.xuanshu.com";
        $curl = $url. $curl;
        
        
        $html = curl_get($curl);//
//         $html = file_get_contents($curl);
//         $html = iconv("gb2312", "utf-8//IGNORE",$html);
        $html = preg_replace('/target="_blank"/i', '', $html);
        $html = preg_replace('/<script.*<\/script>/i', '', $html);
        //<a href="/32848.html" style="color:default" class="name">《传奇控卫》全集</a>
        $html = preg_replace('/(<a href=")(\/\d*.html)("[^>]*>《)(.*)(》全集<\/a>)/i', '\\1http://dzs.qisuu.com/txt/\\4.txt\\3\\4\\5', $html);
        $html = preg_replace('/(<a href=")(\/\d*.html)("><img src="[:\-\d\w\/\.]*">《)(.*)(》全集<\/a>)/i', '\\1http://dzs.qisuu.com/txt/\\4.txt\\3\\4\\5', $html);
        //<a class="downButton" href="http://dzs.qisuu.com/txt/都市全能系统.txt" title="《都市全能系统》全集txt下载">Txt格式下载</a>
        $html = preg_replace('/(<link href=")([\w\.\/]*)(".*\/>)/i', '\\1'.$url.'/'.'\\2\\3', $html);
        //edump($str);
        $html = preg_replace('/(<img src=")([\w\.\/]*)/i', '\\1'.$url.'/'.'\\2', $html);
        //<img src="/skin/blue/logo.png"
        return $html ;
    }
    
}







/**
 * User: dryyun
 * Time: 2015/11/16 16:25
 * File: helper.php
 */
if (! function_exists('runValidator')) {

    /**
     * 执行
     *
     * @param array $data            
     * @param array $rules            
     * @param array $messages            
     * @throws \App\Exceptions\ServiceException
     */
    function runValidator(array $data, array $rules, array $messages = [])
    {
        $validate = \Validator::make($data, $rules, $messages);
        if ($validate->fails()) {
            $message = $validate->getMessageBag()->first();
            throw new \App\Exceptions\ServiceException($message, 202);
        }
        return true;
    }
}

if (! function_exists('mobileCheck')) {

    /**
     * 检查手机号是否符合规则
     *
     * @param
     *            $mobile
     * @return bool
     */
    function mobileCheck($mobile)
    {
        // 手机号码的正则验证
        // return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$phone);
        return (! preg_match("/^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/", $mobile)) ? false : true;
    }
}

if (! function_exists('createSerialNum')) {

    /*
     * 创建流水号
     */
    function createSerialNum($num = 18)
    {
        list ($usec, $sec) = explode(" ", microtime());
        
        $usec = (int) ($usec * 1000000);
        
        $str = $sec . $usec . mt_rand(100000, 999999);
        
        $str = substr($str, 0, $num);
        
        if (strlen($str) < $num) {
            $str = str_pad($str, $num, mt_rand(100000, 999999));
        }
        
        return $str;
    }
}

if (! function_exists('identityCardCheck')) {

    /**
     * 验证身份证号
     *
     * @param
     *            $vStr
     * @return bool
     */
    function identityCardCheck($vStr)
    {
        $vCity = array(
            '11',
            '12',
            '13',
            '14',
            '15',
            '21',
            '22',
            '23',
            '31',
            '32',
            '33',
            '34',
            '35',
            '36',
            '37',
            '41',
            '42',
            '43',
            '44',
            '45',
            '46',
            '50',
            '51',
            '52',
            '53',
            '54',
            '61',
            '62',
            '63',
            '64',
            '65',
            '71',
            '81',
            '82',
            '91'
        );
        
        if (! preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr))
            return false;
        
        if (! in_array(substr($vStr, 0, 2), $vCity))
            return false;
        
        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);
        
        if ($vLength == 18) {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }
        
        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday)
            return false;
        if ($vLength == 18) {
            $vSum = 0;
            
            for ($i = 17; $i >= 0; $i --) {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
            }
            
            if ($vSum % 11 != 1)
                return false;
        }
        
        return true;
    }
}

if (! function_exists('randStr')) {

    /**
     * 生成随机字符串
     *
     * @param unknown_type $len
     *            长度
     * @param unknown_type $format
     *            内容类别，ALL,CHAR,NUMBER
     */
    function randStr($len = 6, $format = 'NUMBER')
    {
        switch ($format) {
            case 'ALL':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@~';
                break;
            case 'CHAR':
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@~';
                break;
            case 'NUMBER':
                $chars = '0123456789';
                break;
            default:
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        // mt_srand ( ( double ) microtime () * 1000000 * getmypid () );
        $password = "";
        while (strlen($password) < $len)
            $password .= substr($chars, (mt_rand() % strlen($chars)), 1);
        return $password;
    }
}

if (! function_exists('toFix')) {

    /**
     * 生成随机字符串
     *
     * @param unknown_type $len
     *            长度
     * @param unknown_type $format
     *            内容类别，ALL,CHAR,NUMBER
     */
    function toFix($val, $precision = 2)
    {
        return sprintf('%.' . $precision . 'f', $val); // round($val,$precision);
    }
}

if (! function_exists('groupInsert')) {

    /**
     * Insert A Group Of Values
     *
     * @param unknown $k            
     * @param unknown $v            
     */
    function fireInsert($k, $v)
    {
        $fields = implode('`,`', array_keys($v[0]));
        $sql = 'insert into `' . $k . '`(`' . $fields . '`)values';
        foreach ($v as $obj) {
            $values[] = '(\'' . implode('\',\'', array_values($obj)) . '\')';
        }
        $sql .= implode(',', $values);
        \DB::insert($sql);
    }

    /**
     * Group Insert
     *
     * @param string $tbname            
     * @param unknown $inputDate            
     */
    function groupInsert($tbname = '', $inputDate = [])
    {
        static $_queue = [];
        if ($tbname == '[fire]') {
            fireInsert($k, $v);
            $_queue = [];
        } else 
            if (substr_replace($tbname, '', 1, strlen($tbname) - 2) == '[]') {
                $tbname = substr($tbname, 1, strlen($tbname) - 2);
                fireInsert($tbname, $_queue[$tbname]);
                unset($_queue[$tbname]);
            } else {
                $_queue[$tbname][] = $inputDate;
                if (count($_queue[$tbname]) > 8000) {
                    groupInsert('[' . $tbname . ']');
                }
            }
    }
}

if (! function_exists('mt_mark')) {

    /**
     * Calculates the Memory & Time difference between two marked points.
     *
     * @param unknown $point1            
     * @param string $point2            
     * @param number $decimals            
     * @return string|multitype:NULL
     */
    function mt_mark($point1 = '', $point2 = '', $unit = 'KB', $decimals = 4)
    {
        static $marker = [];
        
        $units = [
            'B' => 1,
            'KB' => 1024,
            'MB' => 1048576,
            'GB' => 1073741824
        ];
        $unit = isset($units[$unit]) ? $unit : 'KB';
        if ($point2 && $point1) {
            // 取件间隔
            if (! isset($marker[$point1]))
                return false;
            if (! isset($marker[$point2])) {
                $marker[$point2] = [
                    'm' => memory_get_usage(),
                    't' => microtime()
                ];
            }
            
            list ($sm, $ss) = explode(' ', $marker[$point1]['t']);
            list ($em, $es) = explode(' ', $marker[$point2]['t']);
            
            return [
                't' => number_format(($em + $es) - ($sm + $ss), $decimals),
                'm' => number_format(($marker[$point2]['m'] - $marker[$point1]['m']) / $units[$unit], $decimals)
            ];
        } else 
            if ($point1) {
                // 设记录点
                if ($point1 == '[clear]') {
                    $marker = [];
                } else {
                    $marker[$point1] = [
                        'm' => memory_get_usage(),
                        't' => microtime()
                    ];
                }
            } else {
                // 返回所有
                return $marker;
            }
    }
}

if (! function_exists('funcCache')) {

    /**
     * Cache function result and reflush every 5 second
     * Send a commend and unset the selected key
     *
     * @param unknown $func            
     * @param unknown $params            
     * @return mixed
     */
    function funcCache($func, $params = [], $expire = 5)
    {
        // TODO : Release expired keys
        static $_cached = [];
        $key = sha1(json_encode([
            $func,
            $params
        ]));
        if ($expire == '[clear]') {
            unset($_cached[$key]);
            return;
        }
        $time = time();
        if (isset($_cached[$key]) && $_cached[$key]['expire'] > $time) {
            $result = $_cached[$key]['result'];
        } else {
            $result = call_user_func_array($func, $params);
            $_cached[$key] = [
                'result' => $result,
                'expire' => $time + $expire
            ];
        }
        return $result;
    }
}

if (! function_exists('__fsocket')) {

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

