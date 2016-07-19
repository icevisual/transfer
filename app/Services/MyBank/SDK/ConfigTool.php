<?php
namespace App\Services\MyBank\SDK;

class ConfigTool
{

    private static $configTool = null;

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
        // 在此处设置配置文件的路径
        $configFilePath = "conf/TopESA.SignAndVerify.onlyCACert.conf.json";
        $configFilePath = __DIR__ . '/../' . $configFilePath;
        $json = file_get_contents($configFilePath);
        TCA::config(json);
        self::$initialized = true;
    }
}
