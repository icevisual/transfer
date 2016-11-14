<?php
if (! function_exists('getRows')) {

    /**
     * 获取文件指定行的内容
     *
     * @param string $filename
     *            文件名
     * @param integer $start
     *            开始行>=1
     * @param integer $offset
     *            偏移量
     * @return array 所请求行的数组
     */
    function getRows($filename, $start, $offset = 0)
    {
        $rows = file($filename);
        $rowsNum = count($rows);
        if ($offset == 0 || (($start + $offset) > $rowsNum)) {
            $offset = $rowsNum - $start;
        }
        $fileList = array();
        for ($i = $start; $max = $start + $offset, $i < $max; $i ++) {
            $fileList[] = substr($rows[$i], 0, - 2);
        }
        return $fileList;
    }
}

if (! function_exists('reverseScandir')) {

    function reverseScandir($basePath,$prefix = ''){
        $DS = DIRECTORY_SEPARATOR;
        $basePath = trim($basePath,'/\\');
        $scan = scandir($basePath);
        $ret = [];
        for ($i = 2 ; $i < count($scan) ; $i ++){
            $filename = $basePath.$DS.$scan[$i];
            if(is_dir($filename)){
                $ret = array_merge($ret,reverseScandir($filename,$prefix.$scan[$i].$DS));
            }else{
                $ret[] = $prefix.$scan[$i];
            }
        }
        return $ret;
    }
}