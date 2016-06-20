<?php

namespace App\Extensions\Verify;

class RedisStoreHandler implements VerifyStoreInterface {
    
    /**
     * {@inheritdoc}
     */
    public function set($key ,$value){
        return \LRedis::SETEX($key,86400,$value);
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($key){
        return \LRedis::GET($key);
    }
    
    /**
     * {@inheritdoc}
     */
    public function del($key){
        return \LRedis::DEL($key);
    }
    
}