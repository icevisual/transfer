<?php
namespace App\Console\Achieves\Adag;

use Route;
use View;
use App\Http\Controllers\Controller;
use App\Extensions\Common\ConstTrait;

class ApidocAnnParser implements AnnParserConstInter
{
    use ConstTrait;
    
    protected function parseApiParam($data)
    {
        $apiParam = '';
        foreach ($data as $key => $v) {
            if (isset($v['default'])) {
                $str = '     * @apiParam {' . $v['type'] . '} [' . $key . '=' . $v['default'] . '] ' . $v['name'];
            } else {
                $str = '     * @apiParam {' . $v['type'] . '} ' . $key . ' ' . $v['name'];
            }
            $apiParam .= $str . PHP_EOL;
        }
        return $apiParam;
    }

    protected function parseApiSuccessExample($data)
    {
        $ret = '';
        $temp_1 = '     * @apiSuccessExample Success-Response: HTTP/1.1 200 OK';
        foreach ($data as $v) {
            $str = $temp_1 . PHP_EOL . '     *' . json_encode($v);
            $ret .= $str . PHP_EOL;
        }
        return $ret;
    }
    // Adag
    protected function parseApiErrorExample($data)
    {
        $ret = '';
        $temp_1 = '     * @apiErrorExample Error-Responsee: ';
        foreach ($data as $v) {
            $str = $temp_1 . PHP_EOL . '     *' . json_encode($v);
            $ret .= $str . PHP_EOL;
        }
        return $ret;
    }

    protected function parseApiSuccess($data)
    {
        $apiSuccess = '';
        $temp_3 = '     * @apiSuccess ';
        foreach (array_reverse($data) as $v) {
            $str = $temp_3 . implode(' ', $v);
            $apiSuccess .= $str . PHP_EOL;
        }
        return $apiSuccess;
    }

    protected function parseApiError($data)
    {
        $apiError = '';
        $temp_4 = '     * @apiError ';
        foreach ($data as $v) {
            $str = $temp_4 . implode(' ', $v);
            $apiError .= $str . PHP_EOL;
        }
        return $apiError;
    }

    public function parseApiName($data)
    {
        return $appName = str_replace('/', '_', $data);
    }

    public function parseApiVersion($data)
    {
        return $data;
    }

    protected function parseApiGroup($data)
    {
        return $data;
    }

    public function parse($type, $data, $default = '')
    {
        $func = 'parse' . ucfirst($type);
        if (method_exists($this, $func)) {
            if ($data) {
                return $this->$func($data);
            }
            return $default;
        }
        return $default;
    }
}















