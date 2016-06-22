<?php
namespace App\Extensions\MultiUserAuth;

interface AuthMuiltableContract
{

    /**
     * 检查用户是否有权访问
     * @param unknown $action
     */
    public function userAuthorityCheck($action);
    
    /**
     * 获取用户权限信息
     */
    public function getUserAuthority();
    
    /**
     * 单人登录TOKEN
     */
    public function getSingleToken();
    
    /**
     * 设置单人登录TOKEN
     * @param unknown $value
     */
    public function setSingleToken($value);
    
    /**
     * 获取单次登录的令牌
     * @return string
     */
    public function getSingleTokenName();
    
    /**
     * 不同的登录用户Model对应企业的查询关系不同
     * @return string
     */
    public function getCompanyRetrieveCondition();
    
    /**
     * 获取登录的企业信息
     */
    public function getLoginCompany();
    
    /**
     * 此登录系统，需引入第三个实体-企业，企业账户和企业操作员账户都是企业的一个代表
     * @return boolean
     */
    public function companyLoginAuthorityCheck();
    
    /**
     * 检测Session缓存的singleToken和数据库的是否一样
     * @return boolean
     */
    public function singleTokenCheck();
    
    /**
     * 设置一个账号在同一时间只能一人登录
     */
    public function singleTokenSet();
    
    /**
     * 通过登录的输入信息获取用户实体
     * @param array $credentials
     * @return unknown
     */
    public function retrieveByCredentials(array $credentials);
}


/**
 * 
## 查询第一序列Model
[2016-05-17 17:24:42] local.INFO: select * from `xb_company_account` where `account` = ? limit 1 ["ice@hotmail.com"] 
## 查询第二序列Model
[2016-05-17 17:24:42] local.INFO: select * from `xb_operator` where `loginname` = ? limit 1 ["ice@hotmail.com"] 
## 查询company信息
[2016-05-17 17:24:42] local.INFO: select * from `xb_company` where `company_id` = ? limit 1 [155] 
## 验证结果[密码，企业合作状态]
[2016-05-17 17:24:42] local.INFO: validateCredentials [true,true] 
## 更新最后登录
[2016-05-17 17:24:42] local.INFO: update `xb_operator` set `lastlogin` = ?, `prevlogin` = ?, `single_token` = ?, `updated_at` = ? where `oid` = ? ["2016-05-17 17:24:42","2016-05-17 17:19:03","bcf504f2f06cc30dc73ab9a42d0a8dd44c76a7bc","2016-05-17 17:24:42",63] 

## Auth Middleware 检测是否为游客
[2016-05-17 17:24:43] local.INFO: select * from `xb_operator` where `xb_operator`.`oid` = ? limit 1 [63] 
## Auth Middleware 检测企业状态
[2016-05-17 17:24:43] local.INFO: select * from `xb_company` where `company_id` = ? limit 1 [155] 

 */