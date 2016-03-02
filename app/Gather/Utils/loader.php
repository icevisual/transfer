<?php
call_user_func(function () {
    $basePath = __DIR__;
    $files = scandir($basePath);
    $fileBaseName = pathinfo(__FILE__)['basename'];
    foreach ($files as $k => $file) {
        if ($fileBaseName != $file && is_file($basePath . DIRECTORY_SEPARATOR . $file)) {
            include $basePath . DIRECTORY_SEPARATOR . $file;
        }
    }
});


