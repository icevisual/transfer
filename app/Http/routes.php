<?php
\Route::any('/', function () {
    return call_user_func('qishu');
});

\Route::group([
], function () {
    // Route::post('redirect' , 'GeneralTestController@redirect');
    \Route::get('localtest', 'LocalTestController@index');
    \Route::get('document', 'LocalTestController@generate_api_doc');
    \Route::any('test', 'GeneralTestController@test');
    // Route::get('generate' , 'GeneralTestController@generate');
    // \Route::post( 'get_create_code' , 'GeneralTestController@getCode' ); // 注册--获取验证码
});

\Route::group([], function () {
    \Route::get('transfer', 'Transfer\TransferController@index');
    
    Route::get('/soft/sort{id}/index_{page}.html', function ($id, $page) {
        return qishu("/soft/sort{$id}/index_{$page}.html");
    });
    
    Route::get('/soft/sort{id}/sort{id1}', function ($id, $id1) {
        return qishu("/soft/sort{$id}/sort{$id1}");
    });
    
    Route::get('/soft/sort{id}', function ($id) {
        return qishu('/soft/sort' . $id);
    });
    
    Route::get('/{id}.html', function ($id) {
        return qishu('/' . $id . '.html');
    });
});

\Route::group([
    'prefix' => '/api/'
], function () {
    \Route::get('translate', 'Transfer\TransferController@translate');
    \Route::get('search', 'Transfer\TransferController@search');
    
    // \Route::put('put', 'Transfer\TransferController@put');
});
    