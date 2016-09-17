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
    
}



