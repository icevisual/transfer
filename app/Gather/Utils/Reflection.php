<?php
if (! function_exists('invokeMethod')) {

    function getInvokeMethodArray($class, $method)
    {
        $ReflectionMethod = new ReflectionMethod($class, $method);
        if ($ReflectionMethod->isStatic()) {
            return [
                $class,
                $method
            ];
        }
        return [
            new $class(),
            $method
        ];
    }

    function invokeMethod($class, $method, array $param_arr = [])
    {
        $callback = getInvokeMethodArray($class, $method);
        return call_user_func_array($callback, $param_arr);
    }
}

if (! function_exists('getAnnotation')) {

    /**
     * Get The Anntation Array Of Given Function
     *
     * @param unknown $function            
     * @return boolean|multitype:multitype:multitype:string $data = [
     *         '@return' => ['name' => '','type' => '','note' => ''],
     *         '@param' => ['name' => '','type' => '','note' => ''],
     *         'function' => ['note' => ''],
     *         ];
     */
    function getAnnotation($function)
    {
        $reflect = getFunctionReflection($function);
        if ($reflect === false)
            return false;
        $start = $reflect->getStartLine() - 1;
        $end = $reflect->getEndLine();
        $file = $reflect->getFileName();
        $offset = $end - $start;
        $rows = file($file);
        $rowsNum = count($rows);
        $annotation = [];
        $i = $start - 1;
        
        while (($ann = trim($rows[$i --])) && (strpos($ann, '//') === 0 || strpos($ann, '*') === 0 || strpos($ann, '/*') === 0)) {
            ($ann = trim($ann, "/* \t")) && $annotation[] = $ann;
        }
        
        $annData = [];
        $tmp = [];
        foreach ($annotation as $value) {
            if (stripos($value, '@') === 0) {
                // TODO::Process @Return
                $exp = explode(' ', $value);
                $count = count($exp);
                $attr = [];
                if ($count == 2) {
                    $attr = [
                        'type' => $exp[1]
                    ];
                } else 
                    if ($count >= 3) {
                        $attr = [
                            'type' => $exp[1],
                            'name' => $exp[2]
                        ];
                        for ($i = 3; $i < $count; $i ++) {
                            $tmp[] = $exp[$i];
                        }
                    } else {
                        continue;
                    }
                if ($tmp) {
                    $tmp = array_reverse($tmp);
                    $tmp = implode(' ', $tmp);
                    $attr[$exp[0]]['note'] = $tmp;
                }
                $annData[$exp[0]][] = $attr;
                $tmp = [];
            } else {
                $tmp[] = $value;
            }
        }
        if ($tmp) {
            $tmp = array_reverse($tmp);
            $tmp = implode(' ', $tmp);
            $annData['function'][] = [
                'note' => $tmp
            ];
        }
        return $annData;
    }
}

if (! function_exists('getFunctionParamaters')) {

    /**
     * Get The Paramaters Of Given Function
     *
     * @param unknown $function            
     * @return boolean|multitype:NULL
     */
    function getFunctionParamaters($function)
    {
        $reflect = getFunctionReflection($function);
        if ($reflect === false)
            return false;
        $parameters = $reflect->getParameters();
        $params = array();
        foreach ($parameters as $value) {
            $params[] = $value->getName();
        }
        return $params;
    }
}

if (! function_exists('getFunctionReflection')) {

    /**
     * 获取方法的反射
     *
     * @param string|array $function
     *            方法名
     * @return boolean|ReflectionFunction
     */
    function getFunctionReflection($name)
    {
        if (is_array($name)) {
            if (method_exists($name[0], $name[1])) {
                $reflect = new ReflectionMethod($name[0], $name[1]);
            } else {
                return false;
            }
        } else {
            try {
                $reflect = new ReflectionFunction($name);
            } catch (\Exception $e) {
                return false;
            }
        }
        return $reflect;
    }
}

if (! function_exists('getFunctionDeclaration')) {

    /**
     * 获取方法的代码
     *
     * @param unknown $name            
     * @return boolean|multitype:Ambigous
     */
    function getFunctionDeclaration($name, $show = false)
    {
        $reflect = getFunctionReflection($name);
        if ($reflect === false)
            return false;
        $start = $reflect->getStartLine();
        $end = $reflect->getEndLine();
        $file = $reflect->getFileName();
        if ($show) {
            dump($file . ":$start - $end");
        }
        $res = getRows($file, $start - 1, $end - $start + 1);
        return $res;
    }
}



