<?php

if (! function_exists('base64Decode')) {

    function base64Decode($encode)
    {
        $base = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
        $segmentsLength = strlen($encode) / 4;
        $map = array_flip(str_split($base, 1));
        $ret = "";
        for ($i = 0; $i < $segmentsLength; $i ++) {
            $str = substr($encode, $i * 4, 4);
            if ($i == $segmentsLength - 1) {
                $str = trim($str, '=');
                if (strlen($str) == 2) {
                    $a = chr($map[$str[0]] << 2 | $map[$str[1]] >> 4);
                } else
                    if (strlen($str) == 3) {
                        $a = "12";
                        $a{0} = chr($map[$str[0]] << 2 | $map[$str[1]] >> 4);
                        $a{1} = chr(($map[$str[1]] & 0x0f) << 4 | $map[$str[2]] >> 2);
                    } else {
                        $a = "123";
                        $a{0} = chr($map[$str[0]] << 2 | $map[$str[1]] >> 4);
                        $a{1} = chr(($map[$str[1]] & 0x0f) << 4 | $map[$str[2]] >> 2);
                        $a{2} = chr(($map[$str[2]] & 0x01) << 6 | $map[$str[3]]);
                    }
            } else {
                $a = "123";
                $a{0} = chr($map[$str[0]] << 2 | $map[$str[1]] >> 4);
                $a{1} = chr(($map[$str[1]] & 0x0f) << 4 | $map[$str[2]] >> 2);
                $a{2} = chr(($map[$str[2]] & 0x01) << 6 | $map[$str[3]]);
            }
            $ret .= $a;
        }
    
        return $ret;
    }
    
    function c_base64_encode($src)
    {
        static $base = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/";
    
        // //将原始的3个字节转换为4个字节
        $slen = strlen($src);
        $smod = ($slen % 3);
        $snum = floor($slen / 3);
    
        $desc = array();
    
        for ($i = 0; $i < $snum; $i ++) {
            // //读取3个字节
            $_arr = array_map('ord', str_split(substr($src, $i * 3, 3)));
    
            // /计算每一个base64值
            $_dec0 = $_arr[0] >> 2;
            $_dec1 = (($_arr[0] & 3) << 4) | ($_arr[1] >> 4);
            $_dec2 = (($_arr[1] & 0xF) << 2) | ($_arr[2] >> 6);
            $_dec3 = $_arr[2] & 63;
    
            $desc = array_merge($desc, array(
                $base[$_dec0],
                $base[$_dec1],
                $base[$_dec2],
                $base[$_dec3]
            ));
        }
    
        if ($smod == 0)
            return implode('', $desc);
    
        // /计算非3倍数字节
        $_arr = array_map('ord', str_split(substr($src, $snum * 3, 3)));
        $_dec0 = $_arr[0] >> 2;
        // /只有一个字节
        if (! isset($_arr[1])) {
            $_dec1 = (($_arr[0] & 3) << 4);
            $_dec2 = $_dec3 = "=";
        } else {
            // /2个字节
            $_dec1 = (($_arr[0] & 3) << 4) | ($_arr[1] >> 4);
            $_dec2 = $base[($_arr[1] & 7) << 2];
            $_dec3 = "=";
        }
        $desc = array_merge($desc, array(
            $base[$_dec0],
            $base[$_dec1],
            $_dec2,
            $_dec3
        ));
        return implode('', $desc);
    }
    
}

if (! function_exists('detect_encoding')) {

    /**
     * Detect string encoding
     * 
     * @param unknown $content            
     * @return string
     */
    function detect_encoding($content)
    {
        $encode = mb_detect_encoding($content, array(
            "ASCII",
            'UTF-8',
            "GB2312",
            "GBK",
            'BIG5'
        ));
        return $encode;
    }
}

if (! function_exists('unicode_encode')) {

    /**
     * $str 原始中文字符串
     * $encoding 原始字符串的编码，默认GBK
     * $prefix 编码后的前缀，默认"&#"
     * $postfix 编码后的后缀，默认";"
     */
    function unicode_encode($str, $encoding = 'GBK', $prefix = '&#', $postfix = ';')
    {
        $str = iconv($encoding, 'UCS-2', $str);
        $arrstr = str_split($str, 2);
        $unistr = '';
        for ($i = 0, $len = count($arrstr); $i < $len; $i ++) {
            $dec = hexdec(bin2hex($arrstr[$i]));
            $unistr .= $prefix . $dec . $postfix;
        }
        return $unistr;
    }
}

if (! function_exists('unicode_decode')) {

    /**
     * $str Unicode编码后的字符串
     * $decoding 原始字符串的编码，默认GBK
     * $prefix 编码字符串的前缀，默认"&#"
     * $postfix 编码字符串的后缀，默认";"
     */
    function unicode_decode($unistr, $encoding = 'GBK', $prefix = '&#', $postfix = ';')
    {
        $arruni = explode($prefix, $unistr);
        $unistr = '';
        for ($i = 1, $len = count($arruni); $i < $len; $i ++) {
            if (strlen($postfix) > 0) {
                $arruni[$i] = substr($arruni[$i], 0, strlen($arruni[$i]) - strlen($postfix));
            }
            $temp = intval($arruni[$i]);
            $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
        }
        return iconv('UCS-2', $encoding, $unistr);
    }
}





function generatePunctuationRegex(){
    $specialChars = '，。！？,.!?';
    $tsr = iconv('UTF-8', 'UCS-2', $specialChars);
    $arrstr = str_split($tsr,2);
    $res = [];
    foreach ($arrstr as $value){
        $res [] = bin2hex($value{0}).bin2hex($value{1});
    }
    return $regex = '/[\d\x{'.implode('}\x{', $res).'}]/u';
}

function nameContainNumberAndSpecialChar($str){
    //         1. GBK (GB2312/GB18030)
    //         x00-xff GBK双字节编码范围
    //         x20-x7f ASCII
    //         xa1-xff 中文 gb2312
    //         x80-xff 中文 gbk
    //         2. UTF-8 (Unicode)
    //         u4e00-u9fa5 (中文)
    //         x3130-x318F (韩文
    //             xAC00-xD7A3 (韩文)
    //             u0800-u4e00 (日文)
    $regex = '/[\d\x{ff0c}\x{3002}\x{ff01}\x{ff1f}\x{002c}\x{002e}\x{0021}\x{003f}]/u';
    if(preg_match_all($regex, $str,$matchs) ){
        return true;
    }
    return false;
}



