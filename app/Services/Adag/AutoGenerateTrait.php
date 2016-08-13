<?php
namespace App\Services\Adag;


trait AutoGenerateTrait 
{

    /**
     * 获取方法注释
     * 
     * @param unknown $methodName            
     * @return multitype:
     */
    public static function getMethodAnnotation($methodName)
    {
        $export = \ReflectionMethod::export(__CLASS__, $methodName, 1);
        $export = explode('Method [ ', $export);
        if (!$export[0]){
            $export[0] = "\n";
        }else{
            $export[0] = "\n\t".$export[0];
        }
        return "\t" . $export[0];
    }

    /**
     * 自动生成const变量接口
     */
    public static function autoGeneration($prefix = 'parse')
    {
        $ReflectionClass = new \ReflectionClass(__CLASS__);
        // $ConstantsArray = $ReflectionClass->getConstants();
        $InterfaceNames = $ReflectionClass->getInterfaceNames();
        
        $InterfaceFileName = explode("\\", $InterfaceNames[0]);
        $InterfaceFileName = end($InterfaceFileName);
        $ReflectionInter = new \ReflectionClass($InterfaceNames[0]);
        $interConstsArray = $ReflectionInter->getConstants();
        $methodsArray = $ReflectionClass->getMethods();
        $processorConstsArray = [];
        foreach ($methodsArray as $k => $v) {
            $methodName = $v->getName();
            
            if (strpos($methodName, $prefix) === 0 && $prefix !== $methodName) {
                $name = substr($methodName, strlen($prefix));
                $processorConstsArray[strtoupper(\Illuminate\Support\Str::snake($name))] = ucfirst($name);
            }
        }
        $namespace = $ReflectionClass->getNamespaceName();
        $diffArray = array_diff($processorConstsArray, $interConstsArray);
        if ($diffArray) {
            $constString = '';
            $interFileName = __DIR__ . DS . $InterfaceFileName . '.php';
            $eol = PHP_EOL;
            // $eol = "\r\n";
            foreach ($processorConstsArray as $k => $v) {
                $ann = self::getMethodAnnotation($prefix . $v);
                $constString .= "$ann    const $k = '$v';$eol";
            }
            // 有区别,重新生成
            $newContent = <<<EOF
<?php
    
/**
 * Auto
 */
namespace $namespace;
    
interface $InterfaceFileName 
{
$constString
}


EOF;
            
            @file_put_contents($interFileName, $newContent);
        }
    }

}















