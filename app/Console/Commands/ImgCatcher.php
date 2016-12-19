<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Common\Smell;
use App\Models\Common\SmellPc;
use App\Models\Common\SmellThumb;
use GuzzleHttp\Client;

class ImgCatcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ict {action=stripAnn} {min?} {max?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function anAction(){
        
//         dump($this->baiduAvatar('大蒜'));
//         dump($this->baiduImage('大蒜'));
        dump($this->searchImgFromBaidu('日本碳烧'));
        exit;
        
        $res = \DB::select('SELECT * FROM __temp1');
        
        $hosts = [];
        $secondHosts = [];
        foreach ($res as $v){
            $parse_url = parse_url($v->thumb);
            if(isset($hosts[$parse_url['host']])){
                $hosts[$parse_url['host']] ++;
            }else{
                $hosts[$parse_url['host']] = 1;
            }
            
            $s = substr($parse_url['host'], strpos($parse_url['host'], '.') + 1);
            
            if(isset($secondHosts[$s])){
                $secondHosts[$s] ++;
            }else{
                $secondHosts[$s] = 1;
            }
        }
        
        uasort($secondHosts, function($a,$b){
            return $a > $b;
        });
        
        uasort($hosts, function($a,$b){
            return $a > $b;
        });
        $secondHosts = array_filter($secondHosts,function ($a){
            return $a > 100;
        });
        $hosts = array_filter($hosts,function ($a){
            return $a >= 40;
        });
        
        dump($hosts);
        dump($secondHosts);
        
        $pcc = [];
        foreach ($secondHosts as $k =>  $v){
            $rrr = \DB::select('select count(*) AS c from sm_smell_thumb where thumb like ?',['%'.$k.'%']);
            $pcc[$k] = $rrr[0]->c;
        }
        dump($pcc);
        array_walk($secondHosts, function($v,$k) use ($pcc){
            $p = $pcc[$k] / $v;
//             echo "$k => $p".PHP_EOL;
            $this->clog("$k => $p");
        });
    }
    
    
    public function fnpicAction(){
        
        $res = \DB::select("
SELECT
	s.id,
s.cn_name
FROM
	sm_smell s
LEFT JOIN sm_smell_thumb t ON t.id = s.id
WHERE
	t.id IS NULL");
        
        $names = [];
        foreach ($res as $v){
            $smell_id = $v->id;
            $cn_name = $v->cn_name;
            $imgs = $this->searchImgFromBaidu($cn_name,20,10);
            if($imgs){
                SmellPc::groupAddSmellImg($smell_id, $imgs,'');
            }else{
                SmellPc::addPlaceholder($smell_id);
            }
        }
    }
    
    
    public function searchImgFromBaidu($keyword,$n = 10,$offset = 0){
        $this->clog('keyword = '.$keyword);
        $imgs = $this->baiduAvatar($keyword,$n,$offset);
        $funcs = [
            'baiduAvatar',
            'baiduImage1',
            'baiduImage',
        ];
        $ret = [];
        do{
            $func = array_shift($funcs);
            $ret = call_user_func_array([$this,$func], [
                $keyword,$n,$offset
            ]);
        }while(!empty($funcs) && !$ret);
        
        return $ret;
    }
    
    public $unfit = [
        'lv0' => [
            'imgtn.bdimg.com' => 1,
            'imgsrc.baidu.com' => 1
        ],
        'lv-1' => [
            'imgtn.bdimg.com' => 1,
            '360doc.com' => 1,
            'nipic.com' => 1,
            'duitang.com' => 1,
            'hiphotos.baidu.com' => 1,
            'baidu.com' => 1,
            'sinaimg.cn' => 1
        ]
    ];
    
    public function determineUnfit($url){
        $parsed = parse_url($url);
        $unfit = $this->unfit;
        
        if(isset($unfit['lv0'][$parsed['host']])){
            return false;
        }
        $s = substr($parsed['host'], strpos($parsed['host'], '.') + 1);
        if(isset($unfit['lv-1'][$s])){
            return false;
        }
        return true;
    }
    
    public function baiduAvatar($word,$num = 10,$offset = 0){
        $url = 'http://image.baidu.com/search/avatarjson';
        $data = [
            'tn' => 'resultjsonavatarnew',
            'ie' => 'utf-8',
            'word' => $word,
            'cg' => 'wallpaper',
            'pn' => $offset,//
            'rn' => $num * 3,//
            'itg' => '0',
            'z' => '0',
            'fr' => '',
            'width' => '',
            'height' => '',
            'lm' => '-1',
            'ic' => '0',
            's' => '0',
            'st' => '-1',
            'gsm' => '1e'
        ];
        $res = curl_get($url, $data);
        $ret = [];
        if (isset($res['imgs'])) {
            foreach ($res['imgs'] as $key => $v) {
                $url = $v['objURL'];
                if($this->determineUnfit($url)){
                    $ret[] = array_only($v, [
                        'objURL',
                        'height',
                        'width'
                    ]);
                    if(count($ret) >= $num){
                        return $ret;
                    }
                }
            }
        }
        return $ret;
    }
    
    
    
    public function askBaidu($word){
        $url = 'http://image.baidu.com/search/index';
        $data = [
            'tn' => 'baiduimage',
            'ipn' => 'r',
            'ct' => '201326592',
            'cl' => '2',
            'lm' => '-1',
            'st' => '-1',
            'fm' => 'result',
            'fr' => '',
            'sf' => '1',
            'fmq' => '1481795967944_R',
            'pv' => '',
            'ic' => '0',
            'nc' => '1',
            'z' => '',
            'se' => '1',
            'showtab' => '0',
            'fb' => '0',
            'width' => '',
            'height' => '',
            'face' => '0',
            'istype' => '2',
            'ie' => 'utf-8',
            'hs' => '3',
            'ctd' => '1481795967945^00_1583X366',
            'word' => $word,
        ];
        $res = curl_get($url, $data,false);
        $rrr = preg_match('/\,query\:\"([.\w\W\s\S]*)\"\}/ui', $res,$matches);
        if($rrr){
            return $matches[1];
        }
        return false;
    }
    
    public function baiduImage1($word,$limit = 10,$offset = 0){
        $data = [
            'tn' => 'resultjson_com',
            'ipn' => 'rj',
            'ct' => '201326592',
            'is' => '',
            'fp' => 'result',
            'queryWord' => $word,
            'cl' => '2',
            'lm' => '-1',
            'ie' => 'utf-8',
            'oe' => 'utf-8',
            'adpicid' => '',
            'st' => '-1',
            'z' => '',
            'ic' => '0',
            'word' => $word,//'大蒜',
            's' => '',
            'se' => '',
            'tab' => '',
            'width' => '',
            'height' => '',
            'face' => '0',
            'istype' => '2',
            'qc' => '',
            'nc' => '1',
            'fr' => '',
            'pn' => $offset,
            'rn' => $limit * 3,
            'gsm' => '1e',
            //             '1481852172077' => '',
        ];
        $url = 'http://image.baidu.com/search/acjson';
        $res = curl_get($url, $data);
        $ret = [];
        if (isset($res['data'])) {
            foreach ($res['data'] as $k1 => $v1) {
                if(isset($v1['replaceUrl'])){
                    foreach ($v1['replaceUrl'] as $key => $v) {
                        if(isset($v['ObjURL'])){
                            $url = $v['ObjURL'];
                            if($this->determineUnfit($url)){
                                $ret[] = [
                                    'objURL' => $url,
                                    'height' => 0,
                                    'width' => 0,
                                ];
                                if(count($ret) >= $limit){
                                    return $ret;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $ret;
    }

    public function baiduImage($word,$limit = 10,$offset = 0){
        $data = [
            'tn' => 'resultjson_com',
            'ipn' => 'rj',
            'ct' => '201326592',
            'is' => '',
            'fp' => 'result',
            'queryWord' => $word,
            'cl' => '2',
            'lm' => '-1',
            'ie' => 'utf-8',
            'oe' => 'utf-8',
            'adpicid' => '',
            'st' => '-1',
            'z' => '',
            'ic' => '0',
            'word' => $word,
            's' => '',
            'se' => '',
            'tab' => '',
            'width' => '',
            'height' => '',
            'face' => '0',
            'istype' => '2',
            'qc' => '',
            'nc' => '1',
            'fr' => '',
            'pn' => $offset,
            'rn' => $limit * 3,
            'gsm' => 'd2',
            '1481855693934' => '',
        ];
    
        $url = 'http://image.baidu.com/search/acjson';
        $res = curl_get($url, $data);
        $ret = [];
        if (isset($res['data'])) {
            foreach ($res['data'] as $k1 => $v1) {
                if(isset($v1['replaceUrl'])){
                    foreach ($v1['replaceUrl'] as $key => $v) {
                        if(isset($v['ObjURL'])){
                            $url = $v['ObjURL'];
                            if($this->determineUnfit($url)){
                                $ret[] = [
                                    'objURL' => $url,
                                    'height' => 0,
                                    'width' => 0,
                                ];
                                if(count($ret) >= $limit){
                                    return $ret;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $ret;
    }
    
    
    public function paramAction(){
        $str = '
        tn:resultjson_com
ipn:rj
ct:201326592
is:
fp:result
queryWord:蓝叶玉簪
cl:2
lm:-1
ie:utf-8
oe:utf-8
adpicid:
st:-1
z:
ic:0
word:蓝叶玉簪
s:
se:
tab:
width:
height:
face:0
istype:2
qc:
nc:1
fr:
pn:210
rn:30
gsm:d2
1481855693934:';
        
        foreach (explode("\n", $str) as $v){
            $vv = trim($v);
            if($vv){
                $seg = explode(":", $vv);
                echo "'{$seg[0]}' => '{$seg[1]}', ".PHP_EOL;
            }
        }
        exit;
    }
    
    
    public function clog($msg){
        $this->comment('['.now().'] '.$msg);
    }
    
    public function fillAction(){
        // 为每个气味，搜索一个图片
        $smell_list = [];
        $maxAttampt = 200;
        $pageSize = 10;
        do {
            $this->clog('maxAttampt = '.$maxAttampt);
            $smell_list = Smell::getUnCatchedList($pageSize);
            foreach ($smell_list as $smell_id => $smell_name){
                $this->clog('smell_name = '.$smell_name);
                //               $smell_name = '什锦水果'  ;
                $imgs = $this->baiduAvatar($smell_name);
                $probably_name = '';
                if(!$imgs){
                    $this->clog('No Img Found ! Ask Baidu');
                    $probably_name = $this->askBaidu($smell_name);
                    if($probably_name){
                        $this->clog('Got probably name '.$probably_name);
                        // add $probably_name
                        $imgs = $this->baiduAvatar($probably_name);
                        if(!$imgs){
                            $this->clog('No Img Found For $probably_name imgs addPlaceholder');
                            SmellPc::addPlaceholder($smell_id);
                            continue;
                        }
                    }else{
                        $this->clog('Can\'t find probably name , addPlaceholder');
                        SmellPc::addPlaceholder($smell_id);
                        continue;
                    }
                }
                SmellPc::groupAddSmellImg($smell_id, $imgs,$probably_name);
            }
            $maxAttampt -- ;
        }while($maxAttampt && $smell_list );
    }
    
    
    public function fixAction(){
        
        // 为每个气味，搜索一个图片
        $smell_list = [];
        $maxAttampt = 200;
        $pageSize = 10;
        do {
            $this->clog('maxAttampt = '.$maxAttampt);
            $smell_list = SmellPc::getCatchFailedList($pageSize);
            
            foreach ($smell_list as $smell_id => $smell_name){
                $this->clog('smell_name = '.$smell_name);
                //               $smell_name = '什锦水果'  ;
                $imgs = $this->baiduImage($smell_name);
                $probably_name = '';
                if(!$imgs){
                    $this->clog('No Img Found ! Ask Baidu');
                    $probably_name = $this->askBaidu($smell_name);
                    if($probably_name){
                        $this->clog('Got probably name '.$probably_name);
                        // add $probably_name
                        $imgs = $this->baiduImage($probably_name);
                        if(!$imgs){
                            $this->clog('No Img Found For $probably_name imgs');
//                             SmellPc::addPlaceholder($smell_id);
                            continue;
                        }
                    }else{
                        $this->clog('Can\'t find probably name');
//                         SmellPc::addPlaceholder($smell_id);
                        continue;
                    }
                }
                SmellPc::removePlaceholder($smell_id);
                SmellPc::groupAddSmellImg($smell_id, $imgs,$probably_name);
            }
            $maxAttampt -- ;
        }while($maxAttampt && $smell_list && count($smell_list) == $pageSize);
        
        
    }
    
    public function download($url){
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url, [
            'stream' => true,
            'timeout' => 10
        ]);
        // Read bytes off of the stream until the end of the stream is reached
        $body = $response->getBody();
        
        $tempName = '__temp'.random_int(1, 10000);
        $temp = public_path('download/'.$tempName);
        $fp = fopen($temp,'w+');
        while (!$body->eof()) {
            fwrite($fp, $body->read(1024));
//             echo $body->read(1024);
        }
        fclose($fp);
        $sha1 = sha1_file($temp);
        $rename = str_replace($tempName, $sha1, $temp);
        if(!file_exists($rename)){
            rename($temp, str_replace($tempName, $sha1, $temp));
        }
        return $sha1;
    }
    
    
    public function tryDownload($url,$n = 3){
        do{
            try {
                $sha1 = $this->download($url);
                return $sha1;
            }catch (\Exception $e){
                $this->clog('Error Occur:'.$e->getMessage());
            }
            sleep(0.2);
        }while($n --);
        return false;
    }
    
    
    
    
    public function thumbImg($file,$dest,$targetWidth = 230){
        list($width, $height, $type, $attr) = getimagesize($file);
        $newHeight = $height * $targetWidth / $width ;
        $newHeight = intval($newHeight);
        
        
        $this->clog("($width, $height) => ($targetWidth, $newHeight)");
        
        // open an image file
        $img = \Image::make($file);
        // now you are able to resize the instance
        $img->resize($targetWidth, $newHeight);
//         and insert a watermark for example
//         $img->insert('public/watermark.png');
        
        // finally we save the image as a new file
        $img->save($dest);
    }
    
    public function dropImg($filename,$filesha1){
        unlink($filename);
        SmellThumb::where('localpath',$filesha1)->delete();
    }
    
    
    public function extAction(){

        ini_set('memory_limit', '1024M');
        
        $scandir = scandir(public_path('thumb'));
        
        $maxAttrmpt = 100000;
        
        $mines = [];
        
        while ( ( $file = array_pop($scandir) ) && $maxAttrmpt -- ){
            $filename = public_path('thumb/'.$file);
            if(is_file($filename)){
                $aa = getimagesize($filename);
        
                $dest = str_replace('thumb', 'thumbext', $filename);
        
                $extMap = [
                    "image/jpeg" => 'jpg',
                    "image/png" => 'png',
                    "image/gif" => 'gif',
                ];
                
                $dest .= '.'.$extMap[$aa['mime']];
                if(!file_exists($dest)){
                    \DB::update("UPDATE sm_smell_pc SET thumb = ? where thumb = ? ",[
                        $file.'.'.$extMap[$aa['mime']],
                        $file
                    ]);
                    copy($filename, $dest);
                }
            }
        }
    }
    
    
    public function thumbAction(){
        
        ini_set('memory_limit', '1024M');
        
        $scandir = scandir(public_path('download'));
        
        $maxAttrmpt = 100000;
        
        $mines = [];
        
        while ( ( $file = array_pop($scandir) ) && $maxAttrmpt -- ){
            $filename = public_path('download/'.$file);
            if(is_file($filename)){
                $aa = getimagesize($filename);
                
                $dest = str_replace('download', 'thumb', $filename);
                
                if($aa['mime'] == 'image/x-ms-bmp'){
                    $this->clog('Delete `'.$file.'` AS It is BMP ');
                    $this->dropImg($filename,$file);
                    continue;
                }
                if(!file_exists($dest)){
                    try {
                        
                        mt_mark('start');
                        $this->thumbImg($filename,$dest);
                        dump(mt_mark('start','end'));
                        
                    }catch (\Intervention\Image\Exception\NotReadableException $e){
                        $this->clog('Drop `'.$file.'` AS NotReadableException ');
                        $this->dropImg($filename,$file);
                    }
                }
                
//                 exit;
                if(isset($mines[$aa['mime']])){
                    $mines[$aa['mime']] ++;
                }else{
                    $mines[$aa['mime']] = 1;
                }
            }
        }
        
        dump($mines);

//         \LRedis::HGET();
        
    }
    
    
    public function downAction(){
        // 2519
        $min = $this->argument('min');
        $max = $this->argument('max');
        $min = $min ? $min : 0;
        
        $data = SmellThumb::getUnprocessedList(500,$min,$max);
        $i = 0;
        foreach ($data as $v){
            $i ++;
            $this->clog($i.' downloading '.$v['thumb']);
            
            $sha1 = $this->tryDownload($v['thumb']);
            if(false === $sha1){
                continue;
            }
            $this->clog(' over ');
            SmellThumb::updateDown($v['id'], [
                'localpath' => $sha1,
                'down_status' => 1
            ]);
        }
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $action = $this->argument('action');
        $funcName = strtolower($action) . 'Action';
        if (method_exists($this, $funcName)) {
            call_user_func([
                $this,
                $funcName
            ]);
        } else {
            $this->error(PHP_EOL . 'No Action Found');
        }
        $this->comment(PHP_EOL . '> DONE !');
    }
}
