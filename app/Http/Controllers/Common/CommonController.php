<?php
namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\Bill;


use App\Models\Common\Smell;
use App\Models\Common\SmellPc;


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
    
    public function removeUnwanted(){
        $id = \Input::get('pc_id');
        if($id){
            
            $info = SmellPc::select([
                'smell_id',
            ])->where('id',$id)->first();
            
            SmellPc::where('id',$id)->delete();
            
            $new = SmellPc::select([
                'smell_id',
                'id AS pc_id',
                'thumb'
            ])->where('smell_id',$info['smell_id'])->first();
            
            return $this->__json(200,'ok',[
                'pc_id' => $new['pc_id'],
                'thumb' => $new['thumb'],
            ]);
        }
        return $this->__json(400,'ID is required');
    }
    
    public function listUndecidedSmell(){
        $p = \Input::get('page',1);
        $res = Smell::imgSelectQueue($p,50);
        return view('smell.welcome',[
            'info' => $res
        ]);
    }
    
    public function smellSearch(){
        $p = \Input::get('p',1);
        $n = \Input::get('n',10);
        $k = \Input::get('keyword');
        $res = Smell::smellSearch1($k,$p,$n);
        return \Response::json([
            'code' => 1,
            'data' => $res['list'],
            'msg'  => $res['total'],
        ]);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}



