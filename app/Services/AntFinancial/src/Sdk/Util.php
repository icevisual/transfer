<?php
namespace AntFinancial\Sdk;

class Util
{
    
    
    public static function exportExcelByTemplate($filename,array $data,$temp = 'empty'){
        
        $template = storage_path('exports/'.$temp.'.xls');
        
        $PHPReader = new \PHPExcel_Reader_Excel5();
        $PHPExcel = $PHPReader->load($template);
        $PHPExcel->setActiveSheetIndex(0);
        $currentSheet = $PHPExcel->getActiveSheet();
        $count = count($data);
        $i = 0 ;
        foreach ($data as $v){
            $j = 0;
            if(is_array($v)){
                foreach ($v as $v1){
                    $currentSheet->getCell(chr($j + 65).($i + 1))->setValueExplicit($v1,\PHPExcel_Cell_DataType::TYPE_STRING2);
//                     $currentSheet->setCellValue(chr($j + 65).($i + 1),$v1);
                    $j ++ ;
                }
            }else{
//                 $currentSheet->setCellValue(chr($j + 65).($i + 1),$v);
                $currentSheet->getCell(chr($j + 65).($i + 1))->setValueExplicit($v1,\PHPExcel_Cell_DataType::TYPE_STRING2);
            }
            $i ++;
        }
        $writer = \PHPExcel_IOFactory::createWriter($PHPExcel,'Excel5');
        $writer->save(storage_path('exports/'.$filename));
        return [
            'file' => $filename,
            'full' => storage_path('exports/'.$filename),
            'path' => storage_path('exports'),
        ];
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
        $PHPExcel->setActiveSheetIndex(0);
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
                if(preg_match('/^\d+\.\d+$/',$string) ){
                    $string = sprintf('%.2f', $string) ;
                }
                if(is_string($string) && $string && $string{0} == '=') {
                    $string = $CellValue->getFormattedValue() ;
                }
                //$currentSheet->getCellByColumnAndRow($i,$currentRow)->getValue();
                //                 $string && $string.=$currentSheet->getCellByColumnAndRow($i,$currentRow)->getCalculatedValue();
                $string == null && $colNullCount ++;
                $col[$i + 1] = $string;
            }
            if($colNullCount == $allColumn){
                break;
            }
            if($callback != null){
                $callResult = call_user_func_array($callback, [$col,$currentRow]);
                if($callResult){
                    $all[] = $callResult;
                }
            }else{
                $all[] = $col;
            }
        }
        return $all;
    }
    
    
    public static function exportExcelJava($title, $titleArray, $excelData)
    {
        $filename = $title.'.xls';
        $outPutFileName = storage_path('exports/'.$filename);
        $file = new \Java('java.io.File',$outPutFileName);
        $os = new \Java('java.io.FileOutputStream',$file);
        $workbookClass = new \JavaClass('jxl.Workbook');
        $workbook =  $workbookClass->createWorkbook($os); 
        $sheet = $workbook->createSheet('Sheet1',0);
        array_unshift($excelData, $titleArray);
        $i = 0 ;
        foreach ($excelData as $k => $v){
            $j = 0;
            foreach ($v as $k1 => $v1){
                $Label = new \Java('jxl.write.Label',$j,$i,$v1);
                $sheet->addCell($Label);
                $j ++;
            }
            $i ++;
        }
        
        $workbook->write();
        $workbook->close();
        $os->close();
        return [
            'file' => $filename,
            'full' => $outPutFileName,
            'path' => storage_path('exports'),
        ];
    }
    
    
    
    public static function exportExcelSimple($title, $titleArray, $excelData)
    {
        return \Excel::create($title, function ($excel) use($titleArray, $excelData) {
            $excel->sheet('sheet1', function (\Maatwebsite\Excel\Classes\LaravelExcelWorksheet $sheet) 
                use($titleArray, $excelData) {
                $sheet->row(1, $titleArray);
                $sheet->fromArray($excelData, null, 'A2', false, false);
            });
            $excel->setActiveSheetIndex(0);
        })->store('xls', false, true);
    }
    
    

    /**
     * 下载Excel
     *
     * @param unknown $title
     *            文件名
     * @param unknown $titleArray
     *            第一行标题
     * @param unknown $excelData
     *            内容
     * @param unknown $width
     *            列宽
     * @param \Closure $processor
     *            其他样式设置
     */
    public static function exportExcelProcessor($title, $titleArray, $excelData, $width = [], \Closure $processor = null)
    {
        return \Excel::create($title, function ($excel) use($titleArray, $excelData, $width, $processor) {
            $excel->sheet('sheet1', function (\Maatwebsite\Excel\Classes\LaravelExcelWorksheet $sheet) use($titleArray, $excelData, $width, $processor, $excel) {
                // 设置表格整体样式
                $maxRow = count($excelData) + 99;
                
                $processor && call_user_func($processor, $sheet, $excel);
                
                $lastCol = chr(64 + count($titleArray));
                
                $styleArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => \PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array(
                                'argb' => '00000000'
                            )
                        )
                    ),
                    'font' => [
                        'name' => 'Calibri',
                        'size' => 11,
                        'bold' => false
                    ],
                    'alignment' => array(
                        'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    )
                );
                
                $sheet->getStyle('A1:' . $lastCol . $maxRow)
                    ->applyFromArray($styleArray);
                $sheet->getStyle('A1:' . $lastCol . '1')
                    ->getFill()
                    ->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('A000C5FF');
                for ($i = count($excelData); $i <= count($excelData) + 100; $i ++) {
                    $sheet->getRowDimension($i)
                        ->setVisible(true);
                }
                // Set width for multiple cells
                $width && $sheet->setWidth($width);
                $sheet->row(1, $titleArray);
                $sheet->fromArray($excelData, null, 'A2', false, false);
            });
            $excel->setActiveSheetIndex(0);
        })->store('xls', false, true);
        //->store('xls', false, true);
    }

    public static function getBytes($string)
    {
        $bytes = array();
        for ($i = 0; $i < strlen($string); $i ++) {
            $bytes[] = ord($string[$i]);
        }
        return $bytes;
    }

    public static function timestampBizNo()
    {
        return self::formatBizNo(time() . self::formatBizNo((int) rand(1, 999), 3));
    }

    /**
     * ${bizNo}为商户流水号，需定长32位数字字符，不足可在前面补零，上传文件为使用天威证书签名加密后文件。
     * 
     * @param unknown $bizNo            
     * @return string
     */
    public static function formatBizNo($bizNo, $num = 32)
    {
        return str_pad($bizNo, $num, '0', STR_PAD_LEFT);
    }

    public static function formatMoney($amount)
    {
        return str_replace('.', '', sprintf('%016.2f', $amount));
    }

    public static function uuid()
    {
        if (function_exists('com_create_guid')) {
            return com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000); // optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12);
            return $uuid;
        }
    }

    public static function TimestampCurrentTimeMillis()
    {
        $System = new \JavaClass('java.lang.System');
        $Timestamp = new \Java('java.sql.Timestamp', $System->currentTimeMillis());
        return $Timestamp . '';
    }

    public static function base64Encoding($data)
    {
        $base64 = new \JavaClass("org.apache.commons.codec.binary.Base64");
        $result = $base64->encodeBase64String($data);
        return $result . '';
    }

    public static function randomUUID()
    {
        $uuid = new \JavaClass("java.util.UUID");
        return $uuid->randomUUID()->toString() . '';
    }
}
