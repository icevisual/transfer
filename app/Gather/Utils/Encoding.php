<?php
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



