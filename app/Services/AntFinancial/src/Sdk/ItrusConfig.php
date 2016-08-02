<?php
namespace AntFinancial\Sdk;

/**
 * 天威证书配置初始化
 * @author Administrator
 *
 */
class ItrusConfig
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
            // 在此处设置配置文件的路径
        $configFilePath = _ANT_CONFIG_FILE_;
        $json = file_get_contents($configFilePath);
        
        $jsonArray = json_decode($json, true);
        $jsonArray['keyStore'][0]['keyStorePath'] = ant_config_path($jsonArray['keyStore'][0]['keyStorePath']);
        $json = json_encode($jsonArray);
        $TCA = new \JavaClass("cn.topca.api.cert.TCA");
        $TCA->config($json);
        self::$initialized = true;
    }
}
