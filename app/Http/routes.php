<?php


$router->any('/',function(){
    return 'Welcome!';
});




    \Route::get('transfer','Transfer\TransferController@index');
