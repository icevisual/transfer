<?php

namespace App\Gather\Utils;

class Excel{
    
    /**
     * 下载Excel
     * @param unknown $title    文件名
     * @param unknown $titleArray 第一行标题
     * @param unknown $excelData 内容
     * @param unknown $width 列宽
     * @param \Closure $processor 其他样式设置
     */
    public static function downloadExcelProcessor($title,$titleArray,$excelData,$width = [],\Closure $processor = null){
        \Excel::create($title, function($excel) use ($titleArray,$excelData,$width,$processor){
            $excel->sheet('sheet11', function (\Maatwebsite\Excel\Classes\LaravelExcelWorksheet $sheet) use
                ($titleArray,$excelData,$width,$processor) {
                $rowCount = count($excelData);
                // 设置表格整体样式
                $sheet->cells('A1:Z'.($rowCount + 1 ), function(\Maatwebsite\Excel\Writers\CellWriter $cells) {
                    $cells->setBorder(array(
                        'borders' => array(
                            'top'   => array(
                                'style' => 'solid'
                            ),
                        )
                    ));
                    // Set alignment to center
                    $cells->setAlignment('center');
                    // Set vertical alignment to middle
                    $cells->setValignment('middle');
                });
                // Set font with ->setStyle()`
                $sheet->setStyle(array(
                    'font' => array(
                        'name'      =>  'Calibri',
                        'size'      =>  11,
                        'bold'      =>  false
                    )
                ));
                $processor && call_user_func($processor,$sheet);
    
                // Set width for multiple cells
                $width && $sheet->setWidth($width);
                $sheet->row(1, $titleArray);
                $sheet->fromArray($excelData, null, 'A2', false, false);
            });
        })->export('xls');
    }
    
    
    /**
     *
     * @param unknown $filePath
     * @param callable $callback
     * @param number $allColumn
     *  读取的列数
     * @param number $allRow
     *  读取的行数
     * @return boolean|multitype:multitype:mixed
     */
    public static function PHPExcelReader($filePath, callable $callback = null, $allColumn = 0, $allRow = 0)
    {
        $pathinfo = pathinfo($filePath);
        if ($pathinfo['extension'] == 'xlsx') {
            $PHPReader = new \PHPExcel_Reader_Excel2007();
        } else {
            $PHPReader = new \PHPExcel_Reader_Excel5();
        }
        if (! $PHPReader->canRead($filePath)) {
            return false;
        }
        $PHPExcel = $PHPReader->load($filePath);
        $currentSheet = $PHPExcel->getActiveSheet();
        $allColumn || $allColumn = ord($currentSheet->getHighestColumn()) - 64 ;
        $allRow || $allRow = $currentSheet->getHighestRow();
        $all = [];
        for ($currentRow = 1; $currentRow <= $allRow; $currentRow ++) {
            $col = [];
            $colNullCount = 0 ;
            for ($i = 0 ; $i < $allColumn; $i ++) {
                $CellValue = $currentSheet->getCellByColumnAndRow($i,$currentRow);
                $string = $CellValue->getValue();
                if($string instanceof  \PHPExcel_RichText){
                    $string = $string->getPlainText();
                }
                if($string{0} == '=')
                    $string = $CellValue->getFormattedValue() ;//$currentSheet->getCellByColumnAndRow($i,$currentRow)->getValue();
                //                 $string && $string.=$currentSheet->getCellByColumnAndRow($i,$currentRow)->getCalculatedValue();
                $string == null && $colNullCount ++;
                $col[$i + 1] = $string;
            }
            if($colNullCount == $allColumn){
                break;
            }
            if($callback != null){
                $all[] = call_user_func_array($callback, [$col,$currentRow]);
            }else{
                $all[] = $col;
            }
        }
        return $all;
    }
    
    public static function loadCheck($filePath)
    {
    
        // Check validtion and empty
        $pathinfo = pathinfo($filePath);
        if ($pathinfo['extension'] == 'xlsx') {
            $PHPReader = new \PHPExcel_Reader_Excel2007();
        } else {
            $PHPReader = new \PHPExcel_Reader_Excel5();
        }
        if (! $PHPReader->canRead($filePath)) {
            return false;
        }
        return true;
    }
    
    public static function load($filePath,$columnNum = 6)
    {
        $data = self::PHPExcelReader($filePath,null,$columnNum);
        $result['titleArr'] = array_shift($data);
        $result['list'] = array_values($data);
        $result['maxrow'] = count($result['list']);
        $result['maxcol'] = count($result['titleArr']);
        return $result;
    }
    
    
}