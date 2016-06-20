<?php

namespace App\Extensions\Verify;

class SessionStoreHandler implements VerifyStoreInterface {
    
    /**
     * {@inheritdoc}
     */
    public function set($key ,$value){
        return session([$key  => $value]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function get($key){
        return session($key);
    }
    
    /**
     * {@inheritdoc}
     */
    public function del($key){
        return session([$key  => null]);
    }
    
}