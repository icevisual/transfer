<?php




if(!function_exists('divide_equally')){
    function divide_equally($price,$period){
        $each = bcmul ( $price / $period, 1, 2 );
        $result = array_fill(0, $period, floatval($each));
        if($period > 1 ){
            $result[$period - 1 ] =  $price - $each * ($period - 1);
        }
        return  $result;
    }
}

