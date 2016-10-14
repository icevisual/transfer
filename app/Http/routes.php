<?php
\Route::any('/', function () {
    return call_user_func('qishu');
});

// \Route::any('/', 'MerchantsPay\MerchantsController@index');

\Route::group([
], function () {
    \Route::get('localtest', 'LocalTestController@index');
    \Route::get('document', 'LocalTestController@generate_api_doc');
    \Route::any('test', 'GeneralTestController@test');
    \Route::any('gz', 'LocalTestController@gzdecode');
    \Route::any('format', 'LocalTestController@format');
    
    
    
    \Route::controller('/testapi', 'MerchantsPayTestController');
});


Route::match(['get','post'],'/mqtt/superuser','Common\CommonController@superuser' );
Route::match(['get','post'],'/mqtt/auth','Common\CommonController@auth' );
Route::match(['get','post'],'/mqtt/acl','Common\CommonController@acl' );


Route::match(['get','post'],'kindEditor/upload_json.aspx', 'Common\KindEditor@upload_json');
Route::match(['get','post'],'kindEditor/file_manager_json','Common\KindEditor@file_manager_json' );





\Route::group([], function () {
    
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

/**
 * 接口路由
 */
\Route::group([
    'prefix' => '/api/'
], function () {
    \Route::get('translate', 'Transfer\TransferController@translate');
    \Route::get('search', 'Transfer\TransferController@search');
    
    // \Route::put('put', 'Transfer\TransferController@put');
});

/**
 * 页面输出路由
 */
\Route::group([
    'prefix' => '/output/'
], function () {
    \Route::get('bill', 'Common\CommonController@bill');
    \Route::get('websocket', 'Common\CommonController@websocket');
});






    