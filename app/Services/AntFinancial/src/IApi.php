<?php
namespace AntFinancial;

interface IApi
{
    
    
    
    /**
     * 获取APPID
     * @return string
     */
    public function getAppid();
    
    
    /**
     * 设置MsgId
     */
    public function setReqMsgId($msgId);
    
    
    /**
     * 获取请求URL
     */
    public function getReqUrl();
    
    /**
     * 先base64decode，再jsondecode
     * @param array $data
     */
    public function base64JsonDecode($data);
    
    /**
     * 格式化返回数据
     * 
     * @param unknown $return            
     */
    public function resultFormat($return);

    /**
     * 获取成员机构编号
     */
    public function getPartner();

    /**
     * 从XML的body中获取返回内容
     *
     * @param unknown $responseArray            
     */
    public function getResultContent($responseArray);

    /**
     * 检测请求返回结果
     *
     * @param unknown $resultInfo            
     * @return boolen true on success while false otherwise
     */
    public function judgeResult($resultInfo);

    /**
     * 获取返回报文中的resultInfo
     *
     * @param array $responseArray            
     * @return array $resultInfo
     *         <pre>
     *         'ResultStatus' => '' ,// 处理状态 string 2 M U 本次业务处理的状态，默认以下3个状态：S：成功，F：失败，U：未知如不满足要求，可根据业务扩展
     *         'ResultCode' => '' ,// 返回码 string 6 M 9000 当resultStatus为S时，该字段必定为0000当resultStatus为F或U时，该字段可以为全局返回码，也可以为业务返回码。如果为业务返回码，参见业务接口部分
     *         'ResultMsg' => '' ,// 返回码信息 string 255 O 暂时系统异常 当result为S时，该字段可为空当result为F或U时，需要描述该错误的原因
     *         </pre>
     */
    public function getResultInfo($responseArray);

    /**
     * 获取请求时间
     * 
     * @return date
     */
    public function getReqTime();

    /**
     * 获取请求ID
     * 
     * @return UUID
     */
    public function getReqMsgId();

    /**
     * 检测字符串是否为XML
     *
     * @param unknown $string            
     * @return boolean
     */
    public function isXml($string);

    /**
     * 组织业务层签名字符串
     * 业务层签名规则：对除partner、sign本身之外的业务接口字段进行签名，
     * 如已无其他业务接口字段则对空字符串签名；如有则按以下文档中业务字段顺序签名。
     * 如有字段a，值A，字段b，值B，则签名原串为”a=A||b=B”,||为分隔符。
     * 若a字段值为空则签名原串为”a=||b=B”。
     *
     * @param array $data            
     * @return string
     */
    public function assemblySignOriginString(array $data);

    /**
     * 验证参数
     *
     * @param array $data            
     * @return boolean
     */
    public function validateParameters(array $data);

    /**
     * 业务层签名
     *
     * @param string $originString            
     * @return string
     */
    public function signParameters($originString);

    /**
     * 将业务层签名结果组装成XML
     *
     * @param array $data            
     * @return string
     */
    public function formatToXml(array $data);

    /**
     * 对XML做XML Signature签名
     *
     * @param string $xmlStr            
     * @return string
     */
    public function signXml($xmlStr);

    /**
     * XML Signature签名的验签
     *
     * @param string $xmlStr            
     * @return boolean
     */
    public function verifyXml($xmlStr);

    /**
     * XML字符串转为Array
     *
     * @param unknown $xmlStr            
     * @return array
     */
    public function xmlToArray($xmlStr);

    /**
     * 发送报文
     *
     * @param string $xmlStr            
     */
    public function send($xmlStr);

    /**
     * 执行接口
     *
     * @param array $params            
     * @return array $result
     */
    public function run($params);

    /*
     * 输出日志
     */
    public function console($string);

    /**
     * 结束执行
     */
    public function stop();

    /*
     * 输出日志
     * @param array $evnData
     */
    public function recordRequest($evnData);
}