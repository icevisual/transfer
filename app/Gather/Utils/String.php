<?php 



if (! function_exists('getRangeWidthWords')) {

    function getStrWidth($component){
        return $component['c'] * 2 + $component['e'];
    }

    function getStrComponent($str){
        $desLen = strlen($str) ;
        $desNum = mb_strlen($str);
        $c_n = ($desLen - $desNum)/2;
        $e_n = $desNum - $c_n;
        $len = $c_n * 2 + $e_n;
        return [
            'c' => $c_n,
            'e' => $e_n,
        ];
    }

    /**
     * 获取 $min ~ $max 个英文宽度的字 （中文占3，英占1）
     * @param unknown $str
     * @param unknown $min
     * @param unknown $max
     */
    function getRangeWidthWords($str,$min,$max){
        $target = preg_replace('/[\r\n\t]/', '', $str);
        $length = $max;
        $prev = $length;
        $i = 0;
        do{
            if($prev == $length){
                $length--;
            }
            $target = mb_substr($target, 0,$length);
            $component = getStrComponent($target);
            $width = getStrWidth($component);
            $prev = $length;
            if($width <= 0 ){
                return $target;
            }
            $length = $length * $max / $width;
            $i ++ ;
            if($i > 100) break;
        }while($width > $max );
        return $target;
    }

}

