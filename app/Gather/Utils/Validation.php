<?php

if (! function_exists('setRequiredIfMissedEmpty')) {


    function setRequiredIfMissedEmpty($data,$rule){
        foreach ($rule as $k => $v){
            if(is_string($v)){
                $r = explode('|',$v);
            }else{
                $r = $v;
            }
            foreach ($r as $rr){
                if(strpos($rr, 'required_if') === 0){
                    $ruleProperties = preg_split('/[,:]/', $rr);
                    if($data[$ruleProperties[1]] != $ruleProperties[2]){

                        $data[$k] = null;
                    }
                }
            }
        }
        return $data;
    }
}

if (! function_exists('runCustomValidator')) {

    /**
     * Run Constom Validator
     * @param array $input
     *            <pre>
     *            [
     *            'data',      //数据
     *            'rules',     //条件
     *            'messages',  //错误信息
     *            'attributes',//属性名映射
     *            'valueNames',//属性值映射
     *            'config' =>
     *            <div style="margin:20px;">[
     *                  <span style="margin:10px;">'ReturnOrException' => 0, // Return (0) Or Exception(1)</span>
     *                  <span style="margin:10px;">'FirstOrAll' => 0 // First(0) Or All(1)</span>
     *              ],</div>
     *            ]
     *            </pre>
     * @throws \App\Exceptions\ServiceException
     * @return multitype:|boolean
     */
    function runCustomValidator(array $input)
    {
        $input = array_only($input, [
            'data',
            'rules',
            'messages',
            'attributes',
            'valueNames',
            'config'
        ]);

        $config = isset($input['config']) ? $input['config'] : [];
        // Return (0) Or Exception(1)
        // First(0) Or All(1)
        $defaultConfig = [
            'ReturnOrException' => 1,
            'FirstOrAll' => 0
        ];

        $config += $defaultConfig;
        $input = $input + [
            'messages' => [],
            'attributes' => [],
            'valueNames' => [],
            'config' => [],
        ] ;
        $validate = \Validator::make($input['data'], $input['rules'], $input['messages'], $input['attributes']);

        if (isset($input['valueNames']) && ! empty($input['valueNames'])) {
            $validate->setValueNames($input['valueNames']);
        }

        if ($validate->fails()) {
            $message = $validate->getMessageBag();
            $message->setFormat([
                'key' => ':key',
                'message' => ':message'
            ]);
            $errorMsg = $config['FirstOrAll'] ? $message->all() : $message->first();

            if ($config['ReturnOrException']) {
                if ($config['FirstOrAll']) {
                    $errors = [];
                    foreach ($errorMsg as $v) {
                        $errors[$v['key']] = $v['message'];
                    }
                    throw new \App\Exceptions\ServiceException('error', 600, $errors);
                } else {
                    throw new \App\Exceptions\ServiceException($errorMsg['message'], 202);
                }
            } else {
                return $errorMsg;
            }
        }
        return true;
    }
}



if (! function_exists('runValidator')) {

    /**
     * 执行
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @throws \App\Exceptions\ServiceException
     */
    function runValidator(array $data, array $rules, array $messages = [])
    {
        $validate = \Validator::make($data, $rules, $messages);
        if ($validate->fails()) {
            $message = $validate->getMessageBag()->first();
            throw new \App\Exceptions\ServiceException($message, 202);
        }
        return true;
    }
}

if (! function_exists('mobileCheck')) {

    /**
     * 检查手机号是否符合规则
     *
     * @param
     *            $mobile
     * @return bool
     */
    function mobileCheck($mobile)
    {
        // 手机号码的正则验证
        // return preg_match("/^13[0-9]{1}[0-9]{8}$|15[0189]{1}[0-9]{8}$|189[0-9]{8}$/",$phone);
        return (! preg_match("/^(((13[0-9]{1})|(14[0-9]{1})|(15[0-9]{1})|(18[0-9]{1})|(17[0-9]{1}))+\d{8})$/", $mobile)) ? false : true;
    }
}



if (! function_exists('identityCardCheck')) {

    /**
     * 验证身份证号
     *
     * @param
     *            $vStr
     * @return bool
     */
    function identityCardCheck($vStr)
    {
        $vCity = array(
            '11',
            '12',
            '13',
            '14',
            '15',
            '21',
            '22',
            '23',
            '31',
            '32',
            '33',
            '34',
            '35',
            '36',
            '37',
            '41',
            '42',
            '43',
            '44',
            '45',
            '46',
            '50',
            '51',
            '52',
            '53',
            '54',
            '61',
            '62',
            '63',
            '64',
            '65',
            '71',
            '81',
            '82',
            '91'
        );

        if (! preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr))
            return false;

        if (! in_array(substr($vStr, 0, 2), $vCity))
            return false;

        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);

        if ($vLength == 18) {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }

        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday)
            return false;
        if ($vLength == 18) {
            $vSum = 0;

            for ($i = 17; $i >= 0; $i --) {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
            }

            if ($vSum % 11 != 1)
                return false;
        }

        return true;
    }
}


if (! function_exists('form_value')) {

    function form_value($data, $key,$default = '')
    {
        $ks = explode('.', $key);
        $old = old(end($ks));
        return $old ? $old :( array_get($data,$key) ? array_get($data,$key): $default) ;
    }

    
    /**
     *
     * @param array $selectData
     *  select 内容
     * @param mix $name
     *  string : name属性
     * @param string $selected
     *  选中值
     * @param string $append
     *  select 标签追加属性
     * @return string
     */
    function form_select(array $selectData,$name,$selectValue = '',$append = '')
    {
         
        $str = '<select ';
        if(is_array($name)){
            foreach ($name as $k => $v){
                $str .= "$k = '$v' ";
            }
        }
        $str .= " {$append} >";
        foreach ($selectData as $k => $v){
            $selected = '';
            if($k == $selectValue){
                $selected = 'selected';
            }
            $str .= "<option {$selected} value='{$k}'>{$v}</option>";
        }
        $str .= '</select>';
        return $str;
    }
    
    function option_selected($data, $key,$value,$default = '')
    {
        $v = form_value($data, $key,$default);
        return $value == $v ? 'selected':'' ;
    }

    function checkbox_checked($data, $key,$value,$default = '')
    {
        $v = form_value($data, $key,$default);
        return $value == $v ? 'checked':'' ;
    }

}

if (! function_exists ( 'old' )) {

    /**
     * Get Previous Form Field Data
     *
     * @param string $key
     * @param string $default
     */
    function old($key = null, $default = null) {
        return app ( 'request' )->old ( $key, $default );
    }
}

