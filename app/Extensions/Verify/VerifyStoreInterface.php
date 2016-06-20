<?php

namespace App\Extensions\Verify;

interface VerifyStoreInterface {
    
    /**
     * 设置值
     * @param string $key
     * @param string $value
     */
    public function set($key ,$value);
    
    /**
     * 获取值
     * @param string $key
     */
    public function get($key);
    
    /**
     * 删除值
     * @param string $key
     */
    public function del($key);
    
}