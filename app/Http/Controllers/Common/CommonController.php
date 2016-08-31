<?php
namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Common\Bill;

class CommonController extends Controller{
    
        
    /**
     * 合租计费
     */
    public function bill(){
        
        Bill::run();
    }
    
}



