<?php
if (! function_exists('tableViewToArray')) {

    /**
     * 输出 接口字段 => 字段说明
     * 
     * @param unknown $str            
     * @param string $kv            
     */
    function printTableView($str, $kv = true)
    {
        $result = tableViewArray($str, $kv);
        preArrayKV($result);
    }

    /**
     * 将招行-接口的字段说明作为注释和key输出
     * 
     * @param unknown $str            
     */
    function tableViewToArrayAnn($str)
    {
        $array = tableViewArray($str, 1, 0);
        
        echo '<pre>';
        array_walk($array, function ($v, $k) {
            echo sprintf("'%s' => '' ,// %s\n", $k, $v);
        });
        echo '</pre>';
    }

    /**
     * 将招行-接口文档中的字段说明表格，转成数组，key为字段名，value为字段说明
     * 
     * @param unknown $str            
     * @param string $kv
     *            $k => $v or $v => $k
     * @param string $ks
     *            ksort
     * @return boolean|multitype:string
     */
    function tableViewArray($str, $kv = true, $ks = true)
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

    /**
     * 通过接口输出和接口字段说明，生成接口返回样例和 字段说明作为注释
     * 
     * @param unknown $resultExample            
     * @param unknown $ann            
     */
    function resultExampleAnn($resultExample, $ann)
    {
        echo '<pre>';
        foreach ($resultExample as $k => $v) {
            echo " '$k' => '$v',//" . (isset($ann[$k]) ? $ann[$k] : 'UNKNOWN') . " " . PHP_EOL;
        }
        echo '</pre>';
    }
}

if (! function_exists('echoArray')) {

    function echoArray(array $arr)
    {
        echo '[';
        foreach ($arr as $k => $v) {
            if (is_array($v)) {
                echo ',';
                echoArray($v);
            } else {
                if ($k > 0) {
                    echo ',';
                }
                echo $v;
            }
        }
        echo ']';
    }

    /**
     *
     * @param array $arr            
     */
    function echoArrayKV(array $arr, $lv = 1, $paddingLeft = "\t")
    {
        echo '[' . PHP_EOL;
        $padding = str_pad('', $lv, $paddingLeft);
        $padding1 = str_pad('', $lv - 1, $paddingLeft);
        foreach ($arr as $k => $v) {
            echo "$padding'$k' => ";
            if (is_array($v)) {
                echoArrayKV($v, $lv + 1);
            } else {
                echo "'$v'," . PHP_EOL;
            }
        }
        if ($lv == 1) {
            echo $padding1 . '];' . PHP_EOL;
        } else {
            echo $padding1 . '],' . PHP_EOL;
        }
    }

    /**
     *
     * @param array $arr            
     */
    function preArrayKV(array $arr, $lv = 1, $paddingLeft = "\t")
    {
        echo '<pre>';
        echoArrayKV($arr, $lv, $paddingLeft);
        echo '</pre>';
    }
}

if (! function_exists('dump')) {

    /**
     * 浏览器友好的变量输出
     *
     * @param mixed $var
     *            变量
     * @param boolean $echo
     *            是否输出 默认为True 如果为false 则返回输出字符串
     * @param string $label
     *            标签 默认为空
     * @param boolean $strict
     *            是否严谨 默认为true
     * @return void|string
     */
    function dump($var, $echo = true, $label = null, $strict = true)
    {
        $label = ($label === null) ? '' : rtrim($label) . ' ';
        if (! $strict) {
            if (ini_get('html_errors')) {
                $output = print_r($var, true);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            } else {
                $output = $label . print_r($var, true);
            }
        } else {
            ob_start();
            var_dump($var);
            $output = ob_get_clean();
            if (! extension_loaded('xdebug')) {
                $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);
                $output = '<pre>' . $label . htmlspecialchars($output, ENT_QUOTES) . '</pre>';
            }
        }
        if ($echo) {
            echo ($output);
            return null;
        } else
            return $output;
    }
}
if (! function_exists('export')) {

    function export($var)
    {
        echo '<pre>';
        var_export($var);
        echo '</pre>';
    }

    function eexport($var)
    {
        export($var);
        exit();
    }
}
if (! function_exists('redline')) {

    function redline($var)
    {
        echo '<p style="color:red;">' . $var . '</p>';
    }
}

if (! function_exists('line')) {

    function line($var, $eof = PHP_EOL)
    {
        echo $var . $eof;
    }

    function lp($var)
    {
        echo '<p>' . $var . '</p>';
    }
}
if (! function_exists('edump')) {

    /**
     * Dump And Exit
     *
     * @param mix $var            
     * @param string $echo            
     * @param string $label            
     * @param string $strict            
     */
    function edump($var)
    {
        // echo '<pre>';
        dump($var);
        // echo '</pre>';
        // dump($var);
        // call_user_func_array('dump', func_get_args());
        exit();
    }

    function edumpLastSql()
    {
        edump(lastSql());
    }

    function dumpLastSql()
    {
        dump(lastSql());
    }
}

if (! function_exists('object_name')) {

    /**
     * 获取对象的类名
     *
     * @param unknown $name            
     */
    function object_name($name)
    {
        return (new \ReflectionObject($name))->getFileName();
    }

    /**
     * Dump The Class Name Of An Given Object
     *
     * @param String $obj
     *            The Given Object
     */
    function dump_object_name($obj)
    {
        dump(object_name($obj));
    }

    function edump_object_name($obj)
    {
        edump(object_name($obj));
    }
}

