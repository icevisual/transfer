<?php
namespace App\Services\Adag;

use Route;
use View;
use App\Http\Controllers\Controller;

class ApidocAnnGener
{

    /**
     * 分割action
     *
     * @param unknown $action_name            
     * @return multitype:|boolean
     */
    public function compileAction($action_name)
    {
        if ('Closure' != $action_name) {
            if (strpos($action_name, '@')) {
                $action = explode('@', $action_name);
                return $action;
            }
        }
        return false;
    }

    public function run($env = 'local',$apidocAnnstorageFile = '')
    {
        $ignoreRoutes = [
            '/',
            'web/route2',
            'web/test',
            'web/route',
            'web/routeApiData'
        ];
        
        $apidocAnnstorageFile || $apidocAnnstorageFile = base_path('doc/apidocAnns.php');
        
        file_put_contents($apidocAnnstorageFile, '');
        
        $testArray = [
            'web/register'
        ];
        
        $routes = \Route::getRoutes();
        
        $baseUrls = array(
            'local' => \Config::get('app.url'),
            'test' => 'http://test.open.qiweiwangguo.com'
        );
        $routes_select = array();
        foreach ($routes as $v) {
            $data = array();
            $method = array();
            $methods = $v->getMethods();
            $uri = $v->getPath();
            $action = $v->getActionName();
            
            if (in_array($uri, $ignoreRoutes)) {
                continue;
            }
            
            // if (!in_array($uri, $testArray)) {
            // continue;
            // }
            
            $actionData = $v->getAction();
            // 分割action
            $action = $this->compileAction($action);
            if (! $action || ! method_exists($action[0], $action[1])) {
                continue;
            }
            in_array('POST', $methods) and $method[] = 'POST';
            in_array('GET', $methods) and $method[] = 'GET';
            empty($method) && $method[] = $methods[0];
            // 生成method和uri
            $data = [
                'method' => '[' . implode('/', $method) . ']',
                'doMethod' => $method[0],
                'uri' => ltrim($uri, '/')
            ];
            // 获取action指向的方法内的参数
            $data['params'] = $this->getInputParamsAndAnns($action);
            
            $funcAnn = getAnnotation($action);
            
            $data['apiName'] = array_get($funcAnn, 'function.note', $action[1]);
            
            if (isset($funcAnn['@apiSuccess'])) {
                $data['apiSuccess'] = $funcAnn['@apiSuccess'];
            }
            if (isset($funcAnn['@apiError'])) {
                $data['apiError'] = $funcAnn['@apiError'];
            }
            if (isset($funcAnn['@apiVersion'])) {
                $data['apiVersion'] = $funcAnn['@apiVersion'][0]['type'];
            }
            if (isset($funcAnn['@apiGroup'])) {
                $data['apiGroup'] = $funcAnn['@apiGroup'][0]['type'];
            }
            
            $data['example'] = $this->getInvokeExample($data['uri']);
            
            $routes_select[] = $data;
            
            $this->apidocAnnTemplate($data, $baseUrls[$env], $apidocAnnstorageFile);
        }
        return ($routes_select);
    }

    protected function getInvokeExample($uri)
    {
        $list = \App\Models\Common\RequestLog::select([
            'params',
            'return'
        ])->where('uri', $uri)
            ->where('return', '!=', 'null')
            ->whereNotNull('return')
            ->get()
            ->toArray();
        $ret = [];
        $maxDiffRate = 65;
        if ($list) {
            foreach ($list as $k => $v) {
                if (empty($ret)) {
                    $ret[] = $v['return'];
                } else {
                    $similarNum = 0;
                    foreach ($ret as $vv) {
                        similar_text($vv, $v['return'], $per);
                        if ($per > $maxDiffRate) {
                            $similarNum ++;
                        }
                    }
                    if ($similarNum == 0) {
                        $ret[] = $v['return'];
                    }
                }
            }
        }
        $return = [];
        foreach ($ret as $v) {
            $jsonArray = json_decode($v, 1);
            
            if (\JsonReturn::STATUS_OK == array_get($jsonArray, 'code')) {
                $return['success'][] = $jsonArray;
            } else {
                $return['error'][] = $jsonArray;
            }
        }
        return $return;
    }

    protected function getFuncName($action)
    {
        $ann = getAnnotation($action);
        return array_get($ann, 'function.note', $action[1]);
    }

    /**
     * 获取action指向方法的所需参数和参数备注
     *
     * @param unknown $action            
     * @return multitype:boolean
     */
    public function getInputParamsAndAnns($action)
    {
        $codes = getFunctionDeclaration($action);
        return $this->filterParamsAndAnns($codes);
    }

    /**
     * 判别所需参数，现以Input::get()判定
     *
     * @param unknown $codes            
     * @return multitype:boolean
     */
    public function filterParamsAndAnns($codes)
    {
        $params = array();
        if (! is_array($codes))
            return false;
        array_walk($codes, function ($v, $k) use(&$params, $codes) {
            $regs = [
                '/(?:Input::get|\$request->input)\s*\(\s*[\'\"]([\w\d_]*)[\'\"]\s*(?:\s*,\s*[\'\"]?([\s\w_\-]*)[\'\"]?\s*)?\)/',
                '/\$_(?:POST|GET)\s*\[[\'\"]([\w\d_]*)[\'\"]\]/'
            ];
            $hit = false;
            foreach ($regs as $regex) {
                $r = preg_match($regex, $v, $matchs);
                if ($r) {
                    $hit = true;
                    $params[$matchs[1]] = [];
                    if (isset($matchs[2])) {
                        // 设置默认值
                        $params[$matchs[1]] = [
                            'default' => $matchs[2]
                        ];
                    }
                    break;
                }
            }
            if ($hit) {
                if (isset($codes[$k - 1])) {
                    // 获取 参数名称、类别
                    $ann = trim($codes[$k - 1]);
                    if (strpos($ann, '//') === 0) {
                        list ($params[$matchs[1]]['type'], $params[$matchs[1]]['name']) = $this->getPossibleTypeAndName(trim($ann, "/ \r\n"));
                    } else {
                        $ann = explode("//", $v, 2);
                        if (isset($ann[1])) {
                            $ann = trim($ann[1], "/ \r\n");
                            list ($params[$matchs[1]]['type'], $params[$matchs[1]]['name']) = $this->getPossibleTypeAndName(trim($ann, "/ \r\n"));
                        } else {
                            $params[$matchs[1]]['name'] = $this->getDefaultParamName($matchs[1]);
                            $params[$matchs[1]]['type'] = 'String';
                        }
                    }
                }
            }
        });
        return $params;
    }

    /**
     * 分析备注。获取参数类别和名称
     *
     * @param unknown $ann            
     * @return multitype:string unknown
     */
    protected function getPossibleTypeAndName($ann)
    {
        // TODO 根据参数名称 自学习类别
        if (strpos($ann, ' ') === false) {
            return [
                'String',
                $ann
            ];
        }
        $segments = explode(" ", $ann, 2);
        $standardTypesArray = [
            'string',
            'array',
            'object',
            'int',
            'integer',
            'float',
            'double',
            'bool',
            'boolean',
            'file',
            'long',
            'char',
            'short',
            'varchar',
            'date',
            'time',
            'datatime'
        ];
        $standardTypesArray = array_flip($standardTypesArray);
        if (isset($standardTypesArray[strtolower($segments[0])])) {
            return [
                ucfirst(strtolower($segments[0])),
                $segments[1]
            ];
        }
        if (isset($standardTypesArray[strtolower($segments[1])])) {
            return [
                ucfirst(strtolower($segments[1])),
                $segments[0]
            ];
        }
        return [
            'String',
            $ann
        ];
    }

    protected function getDefaultParamName($property)
    {
        // fill Default Map , from exists params of other api , 自学习算法
        $map = [
            'sad' => '阿萨德'
        ];
        return isset($map[$property]) ? $map[$property] : 'unknow';
    }

    
    protected function apidocAnnTemplate($data, $baseUrl, $storageFile)
    {
        $parser             = new ApidocAnnParser();
        $apiVersion         = $parser->parse('apiVersion',array_get($data, 'apiVersion'),'1.0.0');
        $apiGroup           = $parser->parse('apiGroup',array_get($data, 'apiGroup'),'Open_Web');
        $apiName            = $parser->parse('apiName',array_get($data, 'uri'));
        $apiParam           = $parser->parse('apiParam',array_get($data, 'params'));
        $apiSuccessExample  = $parser->parse('apiSuccessExample',array_get($data, 'example.success'));
        $apiErrorExample    = $parser->parse('apiErrorExample',array_get($data, 'example.error'));
        $apiSuccess         = $parser->parse('apiSuccess',array_get($data, 'apiSuccess'));
        $apiError           = $parser->parse('apiError',array_get($data, 'apiError'));
        // @apiSuccess
        $str = <<<EOF
    /**
     * @apiVersion {$apiVersion}
     *
     * @api {{$data['doMethod']}} {$data['uri']} {$data['apiName']}
     * @apiName {$apiName}
     * @apiGroup {$apiGroup}
     *
{$apiParam}     *
{$apiSuccess}     *
{$apiSuccessExample}     *
{$apiError}     *
{$apiErrorExample}     *
     * @apiSampleRequest {$baseUrl}/{$data['uri']}
     */
EOF;
        file_put_contents($storageFile,PHP_EOL.PHP_EOL. $str, FILE_APPEND);
    }
}















