<?php
namespace App\Http\Controllers\Common;

use App\Http\Controllers\BaseController;
use App\Models\Common\Bill;

class CommonController extends BaseController{
    
        
    /**
     * 合租计费
     */
    public function bill(){
        
        Bill::run();
    }
    
}



