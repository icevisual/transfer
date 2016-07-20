<?php
namespace App\Services\MyBank\Sdk;

use App\Services\MyBank\Main;

class ConfigTool
{

    private static $configTool = false;

    public static function getInstance()
    {
        if (! self::$configTool) {
            self::$configTool = new static();
        }
        return self::$configTool;
    }

    private static $initialized = false;

    public function init()
    {
        // 初始化不可重复
        if (self::$initialized)
            return;
        Main::init();
        // 在此处设置配置文件的路径
        $configFilePath = _MYBANK_ROOT_ . DS . 'conf/TopESA.SignAndVerify.onlyCACert.conf.json';
        $json = file_get_contents($configFilePath);
        $TCA = new \JavaClass("cn.topca.api.cert.TCA");
        $TCA->config($json);
        self::$initialized = true;
    }
}
