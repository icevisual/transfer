<?php
$router->any('/', function () {
    return qishu();
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
    
//     \Route::put('put', 'Transfer\TransferController@put');
    
    
});
    