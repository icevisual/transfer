<?php
namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\Bill;

class CommonController extends Controller{
    
        
    /**
     * 鍚堢璁¤垂
     */
    public function bill(){
        
        Bill::run();
    }
    
    
    public function websocket(){
        
        \Artisan::call('ws');
    }
    
    
    
    public function superuser(){
//         {
//             "username": "0CRngr3ddpVzUBoeF",
//             "clientid": "0CRngr3ddpVzUBoeF"
//         }
        return \Response::json([],401);
    }
    
    public function auth(){
//         {
//             "clientid": "0CRngr3ddpVzUBoeF",
//             "username": "0CRngr3ddpVzUBoeF",
//             "password": "XqCEMSzhsdWHfwhm"
//         }
        
        
        return \Response::json();
    }
    
    public function acl(){
        
//         {
//             "access": "1",
//             "username": "0CRngr3ddpVzUBoeF",
//             "clientid": "0CRngr3ddpVzUBoeF",
//             "ipaddr": "192.168.5.60",
//             "topic": "/0CRngr3ddpVzUBoeF"
//         }
        
        
        return \Response::json();
    }
}



