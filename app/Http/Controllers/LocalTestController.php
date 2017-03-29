<?php
namespace App\Http\Controllers;

use Route;
use View;
use function GuzzleHttp\json_decode;

class LocalTestController extends Controller
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

    /**
     * 获取action指向方法的所需参数
     * 
     * @param unknown $action            
     * @return multitype:boolean
     */
    public function getInputParams($action)
    {
        $codes = getFunctionDeclaration($action);
        return $this->filterParams($codes);
    }

    /**
     * 判别所需参数，现以Input::get()判定
     * 
     * @param unknown $codes            
     * @return multitype:boolean
     */
    public function filterParams($codes)
    {
        $params = array();
        if (! is_array($codes))
            return false;
        array_walk($codes, function ($v, $k) use(&$params) {
            // Input::get ( 'uid' );Input::get ( 'service' )
            $r = preg_match('/Input::get\s*\(\s*[\'\"]([\w\d_]*)[\'\"]\s*(:?,\s*[\'\"]*[ \d\w]*[\'\"]*)*\)/', $v, $matchs);
            if ($r) {
                $params[$matchs[1]] = true;
            }
            $r = preg_match('/Input::get\s*\(\s*[\'\"]([\w\d_]*)[\'\"]\s*(:?,[:.\s\S\w\W]*)*\)/', $v, $matchs);
            if ($r) {
                $params[$matchs[1]] = true;
            }
            $r = preg_match('/\$_(?:POST|GET)\s*\[[\'\"]([\w\d_]*)[\'\"]\]/', $v, $matchs);
            if ($r) {
                $params[$matchs[1]] = true;
            }
            $r = preg_match('/\$request->input\s*\(\s*[\'\"]([\w\d_]*)[\'\"]\s*(:?,\s*[\'\"]*[ \d\w]*[\'\"]*)*\)/', $v, $matchs);
            if ($r) {
                $params[$matchs[1]] = true;
            }
        });
        return $params;
    }

    /**
     * 获取filter内用到的参数
     * 
     * @param unknown $filter            
     * @return multitype:boolean
     */
    public function getFilterParams($filter)
    {
        $codes = $this->getFilterCode($filter);
        return $this->filterParams($codes);
    }

    /**
     * 获取filter内用到的参数
     * 
     * @param unknown $filter            
     * @return multitype:boolean
     */
    public function getMiddlewareParams($Middleware)
    {
        $MiddlewareMap = \Route::getMiddleware();
        $MiddlewareClass = $MiddlewareMap[$Middleware];
        
        $codes = getFunctionDeclaration([
            $MiddlewareClass,
            'handle'
        ]);
        return $this->filterParams($codes);
    }

    /**
     * 获取filter的代码
     * 
     * @param unknown $filter            
     * @return Ambigous <boolean, multitype:Ambigous >
     */
    public function getFilterCode($filter)
    {
        // $app = app();
        // $filterClosure = $app['events']->getListeners('r.filter: '.$filter);
        // $code = getFunctionDeclaration($filterClosure[0]);
        $filters = array(
            'redpacket_switch' => 'Redpacket\RedpacketController@get_redpacket_status',
            'uid_token' => 'Redpacket\RedpacketController@verifyUserToken',
            'crm_auth' => 'Lend\LendController@crm_auth'
        );
        if (! isset($filters[$filter]))
            return false;
        $action = $this->compileAction($filters[$filter]);
        $codes = getFunctionDeclaration($action);
        return $codes;
    }

    public function phoneAttribution($phone)
    {
        $api = 'http://v.showji.com/Locating/showji.com20150416273007.aspx?output=json&m=' . $phone;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
        curl_setopt($ch, CURLOPT_USERAGENT, $User_Agen);
        $result = curl_exec($ch);
        
        $result = json_decode($result, true);
        
        if (json_last_error() == JSON_ERROR_NONE && isset($result['QueryResult']) && $result['QueryResult'] == 'True') {
            $data = array(
                'province' => $result['Province'],
                'city' => $result['City'],
                'areacode' => $result['AreaCode'],
                'zip' => $result['PostCode'],
                'company' => $result['TO'],
                'card' => $result['Card']
            );
            return $data;
        }
    }

    protected function tableViewArray($str, $kv = true, $ks = true)
    {
        $res = explode("\n", $str);
        // edump($res);
        $res = array_filter($res, function ($v) {
            return trim($v) != '';
        });
        $result = [];
        foreach ($res as $k => $v) {
            $v = trim($v);
            $vv = preg_split("/\s/", $v, 2);
            // edump($vv);
            $kv && $result[trim($vv[0])] = trim($vv[1]);
            $kv === false && $result[trim($vv[1])] = trim($vv[0]);
        }
        $ks && ksort($result);
        return $result;
    }

    protected function tableViewToArrayAnn($str)
    {
        $array = $this->tableViewArray($str, 1, 0);
        
        array_walk($array, function ($v, $k) {
            echo sprintf("'%s' => '' ,// %s\n", $k, $v);
        });
    }

    public function stripslashes()
    {
        $content = \Input::get('content');
        if ($content) {
            try {
                $res = stripslashes($content);
                echo $res;
            } catch (\Exception $e) {}
        }
    }

    public function strUnserialize()
    {
        $str = \Input::get('content');
        $str = str_replace('\'', '"', $str);
        $arr = unserialize($str);
        echoArrayKV($arr);
    }

    public function json2Array()
    {
        $str = \Input::get('content');
        $str = str_replace('\'', '"', $str);
        $arr = json_decode($str, 1);
        echoArrayKV($arr);
    }

    public function table2Array()
    {
        $str = \Input::get('content');
        preg_match_all('/`([\w]+)`[^\n]+COMMENT \'([^\n]+)\'/i', $str, $matchs);
        foreach ($matchs[1] as $k => $v) {
            echo "'$v' => '',//{$matchs[2][$k]}\n";
        }
    }

    public function table2ArrayData()
    {
        $str = \Input::get('content');
        preg_match_all('/`([\w]+)`[^\n]+COMMENT \'([^\n]+)\'/i', $str, $matchs);
        foreach ($matchs[1] as $k => $v) {
            echo "'$v' => \$data['$v'],//{$matchs[2][$k]}\n";
        }
    }

    public function format()
    {
        $str = \Input::get('content');
        $type = \Input::get('type');
        if (method_exists($this, $type)) {
            return $this->$type();
        }
        $array = $this->tableViewToArrayAnn($str);
    }

    public function generate_api_doc()
    {
        
        // mt_mark('start');
        // $res = getApiInstance('/v1.3.1/redpacket/register');
        // dmt_mark('start','end');
        // edump($res);
        
        // return View::make('localtest.doc');
        // $res = getReturnInLogFile('logs','ReqLogs',9,'');
        // $res = getReturnInLogFile('logs','ReqLogs',10);
        // $Route = new \Illuminate\Routing\Route();
        // $Route->getPrefix()
        $routes = Route::getRoutes();
        // $returns = getReturnInLogFile('logs','Return');
        $showData = [];
        $routes_select = array();
        $all_params = array();
        foreach ($routes as $v) {
            $data = array();
            $method = array();
            $methods = $v->getMethods(); // array(1) {[0] => string(4) "POST"}
            $uri = $v->getPath(); // string(15) "get_create_code"
            $action = $v->getActionName(); // string(29) "GeneralTestController@getCode"
                                            
            // 获取filters
            $filter = $v->beforeFilters();
            // 分割action
            $action = $this->compileAction($action);
            
            if (! method_exists($action[0], $action[1])) {
                continue;
            }
            
            in_array('GET', $methods) and $method[] = 'GET';
            in_array('POST', $methods) and $method[] = 'POST';
            // 生成method和uri
            $uri = '/' . ltrim($uri, '/');
            ! empty($method) and $data = array(
                'method' => '[' . implode('/', $method) . ']',
                'uri' => '/' . ltrim($uri, '/')
            );
            // 获取action指向的方法内的参数
            $data and $action and $data['params'] = $this->getInputParams($action);
            // 获取filter内部所需参数
            if ($data && $filter) {
                $params = array();
                foreach ($filter as $key => $value) {
                    $p = $this->getFilterParams($key);
                    $p && $params += $p;
                }
                $params && $data['params'] && (is_array($params) && $data['params'] += $params);
            }
            
            if (isset($data['params']) && ! is_array($data['params'])) {
                edump($data['params']);
            }
            
            isset($data['params']) && is_array($data['params']) && $all_params += $data['params'];
            
            $rrr = getAnnotation($action);
            
            if (isset($rrr['function'])) {
                $function = $rrr['function'][0];
                $dt = getApiInvokingLog($uri);
                $dt && $showData[$uri] = [
                    'title' => $function,
                    'data' => current($dt)
                ];
            }
            
            // if(isset($returns['/'.$uri]) && $rrr ){
            // dump($rrr);
            // dump($returns['/'.$uri]);
            // }
            
            $data && $routes_select[] = $data;
        }
        
        return View::make('localtest.doc')->with('list', $showData);
        
        edump($showData);
    }

    public function tableColumn()
    {
        $str = <<<EOL
  `order_id` bigint(26) NOT NULL COMMENT '订单流水号',
  `service_num` int(10) DEFAULT '0' COMMENT '服务人数',
  `service_fee` decimal(10,2) DEFAULT '0.00' COMMENT '服务费收费标准（元 / 人 / 次）',
  `tax_invoice_proportion` decimal(5,2) DEFAULT '0.00' COMMENT '开票税金比例 ',
  `has_insurance` tinyint(1) DEFAULT '2' COMMENT '是否有商保，1有，2无',
  `insurance_num` int(10) DEFAULT '0' COMMENT '商保代缴人数',
  `insurance_fee` decimal(10,2) DEFAULT '0.00' COMMENT '商保收费标准（元 ／月）',
  `insurance_invoice_proportion` decimal(5,2) DEFAULT '0.00' COMMENT '商保开票税金比',
  `has_disabled_gold` tinyint(1) DEFAULT '2' COMMENT '是否代缴残疾金，1是，2否',
  `disabled_num` int(10) DEFAULT '0' COMMENT '残疾金代缴人数',
  `disabled_fee` decimal(10,2) DEFAULT '0.00' COMMENT '残疾金收费标准（元 ／月）',
  `disabled_invoice_proportion` decimal(5,2) DEFAULT '0.00' COMMENT '残疾金开票税金比例 ',
  `total_fee` decimal(11,2) NOT NULL DEFAULT '0.00' COMMENT '结算总费用',
  `receive_unit` varchar(100) DEFAULT NULL COMMENT '收款方单位名',
  `receive_card_no` varchar(30) DEFAULT NULL COMMENT '收款方银行卡号',
  `receive_bankname` varchar(50) DEFAULT NULL COMMENT '收款方开户行',
  `receive_contact` varchar(20) DEFAULT NULL COMMENT '收款方联系方式',
EOL;
        preg_match_all('/`([\w]+)`/', $str, $matchs);
        foreach ($matchs[1] as $k => $v) {
            echo "'$v' => '',<br/>";
        }
        exit();
    }

    public function base64decode()
    {
        $content = \Input::get('content');
        if ($content) {
            try {
                $res = base64_decode($content);
                echo $res;
            } catch (\Exception $e) {}
        }
    }

    public function gzdecode()
    {
        $content = \Input::get('content');
        $res = '';
        if ($content) {
            try {
                $res = gzinflate(base64_decode($content));
            } catch (\Exception $e) {}
        }
        return View::make('localtest.gzdecode')->with([
            'result' => $res
        ]);
    }

    public function index()
    {
        // 获取接口调用频度
        $dir = 'logs';
        $fileName = 'ReqLogs';
        $filePath = storage_path() . "/{$dir}/" . $fileName;
        $fileRealPath = $filePath . date('Y-m-d');
        file_exists($fileRealPath) && readMonoLogFile($fileRealPath);
        $todayReq = readMonoLogFile('');
        $todayReq = array_map(function ($v) {
            return $v['Times'];
        }, $todayReq);
        asort($todayReq);
        
        $routes = Route::getRoutes();
        
        $baseUrls = array(
            'Localhost' => 'http://' . $_SERVER['HTTP_HOST'],
            'Test Api' => 'http://api.xb.guozhongbao.com'
        )
        // 'Api' =>'http://api.guozhongbao.com',
        // 'Stage Api' =>'http://stage.api.guozhongbao.com',
        ;
        $todayReqF = [];
        $routes_select = array();
        $all_params = array();
        foreach ($routes as $v) {
            $data = array();
            $method = array();
            $methods = $v->getMethods();
            $uri = $v->getPath();
            $action = $v->getActionName();
            
            $actionData = $v->getAction();
            
            $filter = [];
            if (isset($actionData['middleware']) && $actionData['middleware']) {
                $filter = (array) $actionData['middleware'];
            }
            
            // 获取filters
            // $filter = $v->beforeFilters();
            // 分割action
            $action = $this->compileAction($action);
            
            if (! method_exists($action[0], $action[1])) {
                continue;
            }
            
            in_array('GET', $methods) and $method[] = 'GET';
            in_array('POST', $methods) and $method[] = 'POST';
            // 生成method和uri
            ! empty($method) and $data = array(
                'method' => '[' . implode('/', $method) . ']',
                'doMethod' => $method[0],
                'uri' => '/' . ltrim($uri, '/')
            );
            // 获取action指向的方法内的参数
            $data and $action and $data['params'] = $this->getInputParams($action);
            // 获取filter内部所需参数
            if ($data && $filter) {
                $params = array();
                foreach ($filter as $key => $value) {
                    $p = $this->getMiddlewareParams($value);
                    $p && $params += $p;
                }
                $params && $data['params'] && (is_array($params) && $data['params'] += $params);
            }
            isset($data['params']) && is_array($data['params']) && $all_params += $data['params'];
            $data && ($routes_select[] = $data) && isset($todayReq[$data['uri']]) && $todayReqF[$data['uri']] = count($routes_select) - 1;
        }
        
        // 高频度置前
        $res = [];
        foreach ($todayReqF as $k => $v) {
            $add = $routes_select[$v];
            array_unshift($res, $add);
            unset($routes_select[$v]);
        }
        foreach ($routes_select as $k => $v) {
            $res[] = $v;
        }
        unset($routes_select);
        return View::make('localtest.index')->with('route', $res)
            ->with('baseUrls', $baseUrls)
            ->with('all_params', $all_params);
    }
    
    
    
    
}















