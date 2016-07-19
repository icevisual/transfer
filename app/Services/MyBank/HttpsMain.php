<?php
namespace App\Services\MyBank;

class HttpsMain
{

    /**
     * 是否开启验签
     */
    public static  $isSign = true;

    /**
     * 身份证
     */
    public static  $certNo = "430830198907201280";

    /**
     * 银行卡号
     */
    public static  $cardNo = "8888888453427229";

    /**
     * 渠道
     */
    public static  $channel = "MYBANK";

    /**
     * 短信验证码
     */
    public static  $smsCode = "888888";

    /**
     * 币种
     */
    public static  $currencyCode = "156";

    /**
     * 联调环境url
     */
    // public static  String reqUrl = "https://fcsupergw.dev.dl.alipaydev.com/open/api/common/request.htm";
    // sit环境url
    public static  $reqUrl = "https://fcsupergw.dl.alipaydev.com/open/api/common/request.htm";

    /**
     */
    public static function main()
    {}

    public static function httpsReq($reqUrl, $param)
    {}
}
