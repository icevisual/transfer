<?php



if (! function_exists('ant_path')) {

    function ant_path($path = '')
    {
        _ANT_ROOT_ . $path;
        if ($path) {
            return _ANT_ROOT_ . ltrim($path, '/');
        }
        return rtrim(_ANT_ROOT_, '/');
    }
}

if (! function_exists('ant_config_path')) {

    function ant_config_path($path = '')
    {
        return ant_path('conf').DS.$path;
    }
}

if (! function_exists('ant_test_path')) {

    function ant_test_path($path = '')
    {
        return ant_path('test').DS.$path;
    }
}

