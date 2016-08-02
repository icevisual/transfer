<?php
namespace AntFinancial\Services;

use AntFinancial\Api\BatchPay;
use AntFinancial\Api\SmsCodeSend;
use AntFinancial\Sdk\HttpsMain;
use AntFinancial\Sdk\Util;
use AntFinancial\Sdk\Sign;
use AntFinancial\Sdk\SFTPUtil;
use AntFinancial\Sdk\DataGenerator;
use AntFinancial\Api\BatchPayQuery;

class BatchPayServices
{

    /**
     * /download/H2H/batchPayResult/${IpRoleID}/${YYYYMMDD}/h2h_batchPayResult_${batchTransNo}_${FILE_MD5}.xls
     *
     * ${IpRoleID}为网商银行给商户分配的商户角色编号
     * ${YYYYMMDD}为年月日
     * ${batchTransNo}为网商银行交易批次号
     *
     * @param unknown $batchTransNo            
     * @return multitype:string
     */
    public function getBatchPayResultFilename($batchTransNo,$fileMD5)
    {
        $IpRoleID = HttpsMain::getIpRoleID();
        $Date = $this->getDataFromBatchTransNo($batchTransNo,'Ymd');
        $path = "/download/H2H/batchPayResult/{$IpRoleID}/{$Date}/";
        $file = "h2h_batchPayResult_{$batchTransNo}_{$fileMD5}.xls";
        $Ret = [
            'full' => $path . $file,
            'path' => $path,
            'file' => $file
        ];
        return $Ret;
    }

    /**
     * /upload/H2H/batchPay/${IpRoleID}/${YYYYMMDD}/h2h_batchPay_${IpRoleID}_${bizNo}.xls
     * ${bizNo}为商户流水号，需定长32位数字字符，不足可在前面补零，上传文件为使用天威证书签名加密后文件。
     *
     * @param unknown $bizNo            
     * @return string
     */
    public function getBatchPayFileName($bizNo, $type = 'xls')
    {
        $IpRoleID = HttpsMain::getIpRoleID();
        $Date = date('Ymd');
        $path = "/upload/H2H/batchPay/{$IpRoleID}/{$Date}/";
        $file = "h2h_batchPay_{$IpRoleID}_{$bizNo}.{$type}";
        $Ret = [
            'full' => $path . $file,
            'path' => $path,
            'file' => $file
        ];
        return $Ret;
    }

    public function exportExcel($filename, $data)
    {
        $titleArray = [
            'card_no' => '收款人账号（必填）',
            'truename' => '收款人姓名（必填）',
            'bank_name' => '收款人开户行名称（必填）',
            'amount' => '付款金额（单位：元）',
            'bank_no' => '开户行联行号',
            'payee_name' => '收款人网点名称',
            'identity' => '身份证号',
            'phone' => '手机号',
            'alipay_no' => '支付宝账号',
            'note' => '备注',
            'pay_id' => '对账ID'
        ];
        [
            'card_no' => '6228380322133403',
            'truename' => '古天乐',
            'bank_name' => '营口银行',
            'amount' => 222,
            'bank_no' => '',
            'payee_name' => '',
            'identity' => '',
            'phone' => '',
            'alipay_no' => '',
            'note' => '' . randomChineseName(2),
            'pay_id' => '000001'
        ];
        $width = [
            'A' => '32',
            'B' => '32',
            'C' => '32',
            'D' => '32',
            'E' => '22',
            'F' => '22',
            'G' => '22',
            'H' => '22',
            'I' => '22',
            'J' => '22',
            'K' => '22'
        ];
//         array_unshift($data, $titleArray);
//         return Util::exportExcelByTemplate($filename . '.xls', $data);
        // exportExcelByTemplate
        return Util::exportExcelJava($filename, $titleArray, $data);
        return Util::exportExcelSimple($filename, $titleArray, $data);
        return Util::exportExcelProcessor($filename, $titleArray, $data, $width);
    }

    
    public function getDataFromBatchTransNo($batchTransNo,$format = 'Y-m-d'){
        $dateString = substr($batchTransNo, 0,8);
        $date = date($format,strtotime($dateString));
        return $date;
    } 
    
    public function generateBatchResultDownload($batchTransNo){
        return storage_path('exports/'.$batchTransNo.'-result.xls');
    }
    
    
    public function loadBatchPayResult($donwloadFile){
        return Util::PHPExcelReader($donwloadFile,function($col,$currentRow){
            $titleArray = [
                'card_no' => '收款人账号（必填）',
                'truename' => '收款人姓名（必填）',
                'bank_name' => '收款人开户行名称（必填）',
                'amount' => '付款金额（单位：元）',
                'bank_no' => '开户行联行号',
                'payee_name' => '收款人网点名称',
                'identity' => '身份证号',
                'phone' => '手机号',
                'alipay_no' => '支付宝账号',
                'note' => '备注',
                'pay_id' => '对账ID',
                'result_status' => '交易结果',
                'result_desc' => '交易描述',
            ];
            if($currentRow > 1){
                return array_combine(array_keys($titleArray), $col);
            }
            return false;
        });
    }
    
    
    public function BatchPayQuery($batchTransNo,$date = null){
        if(!$date){
            $date = $this->getDataFromBatchTransNo($batchTransNo);
        }
        $api = new BatchPayQuery();
        $params = [
            'queryStartDate' => $date,
            'queryEndDate' => $date,
            'pageSize' => '10',
            'pageIndex' => '1',
            'batchTransNo' => $batchTransNo,
            'batchState' => null,
        ];
        $result = $api->run($params);
        if(isset($result[0])){
            $retData = $result[0];
            if($api->judgeBatchFinished($retData)){
                $BatchPayResultFileinfo = $this->getBatchPayResultFilename($batchTransNo, $retData['resultMd5']);
                $donwloadFile = $this->generateBatchResultDownload($batchTransNo);
                if(!is_file($donwloadFile)){
                    $instance = SFTPUtil::getInstance(HttpsMain::getSFTPConfig());
                    $donwloadResult = $instance->download($BatchPayResultFileinfo['full'],$donwloadFile );
                }
                $excelData = $this->loadBatchPayResult($donwloadFile);
                dump($donwloadFile);
                dump($excelData);
            }
            edump($retData);
        }else{
            // ERROR
            edump($result);
        }
    }
    
    
    /**
     * 商户通过银企直连确认批量代发，发起前先上传代发名册文件至sftp服务器，
     * 再调用发送短信验证码接口，最后调用此接口确认代发。（上传文件后请稍后3分钟
     * 左右再确认代发，因为sftp上的文件是异步扫描通知银企直连服务的。）
     */
    public function BatchPay(array $data = [])
    {
//         dump(sha1_file('D://desktop/a.xls'));
//         dump(sha1_file('D://desktop/00000000000000000001469777043014-signed.xls'));
//         exit;
        mt_mark('start');
        
        $bizNo = Util::timestampBizNo();
        
        $action = \Input::get('a', 'upload'); //
        
        $bizNo = \Input::get('b', $bizNo); //
        
        $num = \Input::get('n', 2); //
        
        $data = DataGenerator::getInstance()->getCachedDs($bizNo,$num);
        
        $redisKey = 'bizList';
        
        \LRedis::HSET($redisKey,$bizNo,date('Y-m-d H:i:s-').$num);
        
        // 获取支付统计信息
        $totalCount = count($data);
        $totalAmount = 0;
        foreach ($data as $k => $v) {
            $totalAmount += $v['amount'];
        }
        // 生成流水号
                                          
        // 组装SFTP文件全路径
        $remoteFile = $this->getBatchPayFileName($bizNo);
        // 初始化STFP
        $instance = SFTPUtil::getInstance(HttpsMain::getSFTPConfig());
        dump($remoteFile['full']);
        // 检测文件是否存在
        if (! $instance->is_file($remoteFile['full'])) {
            mt_mark('before exportExcel');
            // 导出数据EXCEL
            $exported = $this->exportExcel($bizNo, $data);
            mt_mark('before signFile');
            // EXCEL 加签
            $fileinfo = Sign::signToExcel($exported['full'], $bizNo . '-signed');
            mt_mark('before upload');
            // 上传文件
            $instance->upload($fileinfo['full'], $remoteFile['full']);
        }
        mt_mark('before BatchPay');
        dump(mt_mark());
        dump(mt_mark('start', 'before BatchPay'));
        dump($bizNo);
        if ($action == 'upload') {
            exit();
        }
        \LRedis::HDEL($redisKey,$bizNo,$num);
        
        $api = new BatchPay();
        if (HttpsMain::$sendSms) {
            $SmsCodeSend = new SmsCodeSend();
            $bizNo = $SmsCodeSend->run([
                'bizNo' => $bizNo,
                'bizName' => 'P_BATCH_SALARY',
                'certNo' => HttpsMain::$certNo
            ]);
        }
        // P_FUND_BUY，余利宝申购；
        // P_FUND_RANSOM，余利宝赎回；
        // P_BATCH_SALARY，批量代发;
        // P_INNER_TRANSFER, 同行转账;
        // P_CROSS_TRANSFER, 跨行转账
        $params = [
            'bizNo' => $bizNo,
            'smsCode' => HttpsMain::$smsCode,
            'fileName' => $remoteFile['file'],
            'totalCount' => $totalCount,
            'totalAmount' => Util::formatMoney($totalAmount),
            'currencyCode' => HttpsMain::$currencyCode,
            'remark' => '代发工资',
            'companyCardNo' => HttpsMain::$cardNo
        ];
        
        $result = $api->run($params);
        
        $api->console($result);
    }

    public static function Main()
    {
        $BatchPayServices = new static();
        //2016080110610000002390
//         $BatchPayServices->BatchPay();
        $batchTransNo = '2016080110610000002390';
        $BatchPayServices->BatchPayQuery($batchTransNo);
    }
}