<?php
$router->any('/', function () {
    return 'Welcome!';
});

\Route::group([], function () {
    \Route::get('transfer', 'Transfer\TransferController@index');
});

\Route::group([
    'prefix' => '/api/'
], function () {
    \Route::get('translate', 'Transfer\TransferController@translate');
    \Route::get('search', 'Transfer\TransferController@search');
    
    
});
    