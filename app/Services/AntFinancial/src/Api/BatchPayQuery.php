<?php
namespace AntFinancial\Api;

use AntFinancial\BaseApi;

class BatchPayQuery extends BaseApi
{

    /**
     * 批处理状态 INIT 申请成功
     * 
     * @var unknown
     */
    const BATCHSTATE_INIT = 'INIT';

    /**
     * 批处理状态 PROCESSING 处理中
     * 
     * @var unknown
     */
    const BATCHSTATE_PROCESSING = 'PROCESSING';

    /**
     * 批处理状态 FINISH 处理完成
     * 
     * @var unknown
     */
    const BATCHSTATE_FINISH = 'FINISH';

    /**
     * 业务状态 ACCEPTED
     * 
     * @var unknown
     */
    const BIZSTATE_ACCEPTED = 'ACCEPTED';

    /**
     * 业务状态  SUCCESS
     * 
     * @var unknown
     */
    const BIZSTATE_SUCCESS = 'SUCCESS';

    /**
     * 业务状态 PART_SUCCESS
     * 
     * @var unknown
     */
    const BIZSTATE_PART_SUCCESS = 'PART_SUCCESS';

    /**
     * 业务状态 FAIL
     * 
     * @var unknown
     */
    const BIZSTATE_FAIL = 'FAIL';

    /**
     * 业务状态  UNKNOWN
     * 
     * @var unknown
     */
    const BIZSTATE_UNKNOWN = 'UNKNOWN';

    protected $function = 'ant.ebank.transfer.batchpay.query';

    /**
     * 参数验证规则
     *
     * @var unknown
     */
    protected $parameterValidate = [
        'queryStartDate' => '',
        'queryEndDate' => '',
        'pageSize' => '',
        'pageIndex' => '',
        'batchTransNo' => '',
        'batchState' => ''
    ];

    public function resultFormat($return)
    {
        return $this->base64JsonDecode($return['batchResultList']);
    }

    
    public function judgeBatchFinished($retData){
        return array_get($retData,'batchState') == self::BATCHSTATE_FINISH;
    }
    
    /**
     * 结果描述
     * batchApplyNbr 内部批次号 String 40 M Json格式base64编码
     * bizState 业务状态 String 10 M ACCEPTED, SUCCESS, PART_SUCCESS, FAIL, UNKNOWN
     * batchState 批处理状态 String 10 M INIT 申请成功,PROCESSING 处理中,FINISH 处理完成
     * totalCnt 总笔数 int M 100 代发笔数
     * totalAmt 总金额 number(15，0) 15 M 000000000001500 消费币种的最小货币单位（分），参考《附件-金额》，金额不足15位前补0，如金额为15.00，则000000000001500
     * failCnt 失败总笔数 int M 100 代发笔数
     * failAmt 失败总金额 number(15，0) 15 M 000000000001500 消费币种的最小货币单位（分），参考《附件-金额》，金额不足15位前补0，如金额为15.00，则000000000001500
     * successCnt 成功总笔数 int M 100 代发笔数
     * successAmt 成功总金额 number(15，0) 15 M 000000000001500 消费币种的最小货币单位（分），参考《附件-金额》，金额不足15位前补0，如金额为15.00，则000000000001500
     * resultMd5 处理结果文件md5 String 32 C 202cb962ac59075b964b07152d234b70
     * remark 备注 String 100 M 代发工资
     * errorDesc 错误原因描述 String 100 C
     * extContext 附加信息 String 100 C
     * gmtCreate 创建时间 datetime 19 M 2015-01-07 16:23:12 yyyy-MM-dd HH:mm:ss
     */
    public function resultDesc()
    {}

    public static function Main()
    {
        $api = new static();
        
        $params = [
            'queryStartDate' => '2016-08-01',
            'queryEndDate' => '2016-08-01',
            'pageSize' => '10',
            'pageIndex' => '1',
            'batchTransNo' => '2016080110610000002390',
            'batchState' => null
        ];
        $result = $api->run($params);
        
        $api->console($result);
    }
}