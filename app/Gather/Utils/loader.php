<?php

call_user_func(function(){
    
    $basePath =  __DIR__;
    
    $files = scandir($basePath);
    
    $fileBaseName = pathinfo(__FILE__)['basename'];
    foreach ($files as $k => $file){
        if(
            $fileBaseName != $file &&
            is_file($basePath.'/'.$file)

            ){
            include $basePath.'/'.$file;
        }
    }
});


