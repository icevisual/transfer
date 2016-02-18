<?php
/**
 * Applies the callback to the elements of the given arrays
 *
 * @link http://www.php.net/manual/en/function.array-map.php
 * @param
 *        	callback callable <p>
 *        	Callback function to run for each element in each array.
 *        	</p>
 * @param
 *        	_ array[optional]
 * @return array an array containing all the elements of array1
 *         after applying the callback function to each one.
 */
function array_map_recursive($callback, array $array1)
{
    return array_map(function ($v) use($callback) {
        if (is_array($v)) {
            return array_map_recursive($callback, $v);
        } else {
            return call_user_func_array($callback, array(
                $v
            ));
        }
    }, $array1);
}

/**
 * 减除过长连续数组
 *
 * @param array $array1            
 * @return multitype:|multitype:Ambigous <> Ambigous <Ambigous <>>
 */
function array_clear(array $array1, $limit = 5)
{
    return array_map(function ($v) use($limit) {
        if (is_array($v)) {
            if (count($v) > $limit) {
                $keyys = array_keys($v);
                if (isset($keyys[$limit]) && $keyys[$limit] == $limit) {
                    $v = [
                        $v[0],
                        $v[1]
                    ];
                }
            }
            // $v = array_filter($v);
            return array_clear($v);
        } else {
            return $v; // call_user_func_array($callback, array($v));
        }
    }, $array1);
}

/**
 * Decode Json String recursively
 */
function json_decode_recursive($ret)
{
    return array_map_recursive(function ($rt) {
        if (strpos($rt, '[object]') === 0) {
            preg_match('/\{.*\}/', $rt, $mt);
            if ($mt) {
                $mtr = json_decode(($mt[0]), true);
                if (json_last_error() == JSON_ERROR_NONE) {
                    return json_decode_recursive($mtr);
                }
            }
        }
        $len = strlen($rt);
        if ($len && $rt{0} == '{' && $rt{$len - 1} == '}') {
            $mt = json_decode($rt, true);
            if (json_last_error() == JSON_ERROR_NONE) {
                return json_decode_recursive($mt);
            }
        }
        return $rt;
    }, $ret);
}

function getOnlineIp()
{
    $OnlineIp = \LRedis::GET('OnlineIp');
    if (! $OnlineIp) {
        $url = 'http://city.ip138.com/ip2city.asp';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 8);
        $send_result = curl_exec($ch);
        if ($send_result === false) {
            throw new \Exception("REQ[$url]" . curl_error($ch), curl_errno($ch) + 60000);
        }
        preg_match('/\[(.*)\]/', $send_result, $ip);
        $OnlineIp = $ip[1];
        \LRedis::SETEX('OnlineIp', 6000, $OnlineIp);
    }
    return $OnlineIp;
}



if(! function_exists('transfer')){

    function transfer(){
        $query = \Input::get('w');
        if(!$query){
            echo 'Word Is Required'.PHP_EOL;
            exit;
        }
        $res = curl_post('http://fanyi.baidu.com/v2transapi', [
            'from' => 'en',
            'to' => 'zh',
            'query' => $query,
            'transtype' => 'realtime',
            'simple_means_flag' => '3',
        ]);
        $result = [];
        $data = json_decode($res,1);
        if(isset($data['dict_result']['simple_means']['symbols'][0])){
            $symbols = $data['dict_result']['simple_means']['symbols'][0];
            $result['[En]'] = '['.$symbols['ph_en'].' ]';
            $result['[Am]'] = '['.$symbols['ph_am'].' ]';
            //echo -e "\e[1;31m skyapp exist \e[0m"
            echo  PHP_EOL."[\e[1;31m{$query}\e[0m ]".PHP_EOL;
            if ($symbols['ph_en'])
                echo "【英】[{$symbols['ph_en']} ],【美】[{$symbols['ph_am']} ]".PHP_EOL;
            foreach ($symbols['parts'] as $k => $v){
                $result['means'] [$k] = $v['part'];
                foreach ($v['means'] as $k1 => $v1){
                    $result['means'] [$k].= ($k1 ? ",":'').$v1;
                }
                echo $result['means'] [$k].PHP_EOL;
            }
            echo  PHP_EOL;
        }
        exit;
    }

}



function compute_distance($strA, $strB) {
    $len_a = mb_strlen($strA);
    $len_b = mb_strlen($strB);
    $temp = [];
    for($i = 1; $i <= $len_a; $i++) {
        $temp[$i][0] = $i;
    }

    for($j = 1; $j <= $len_b; $j++) {
        $temp[0][$j] = $j;
    }

    $temp[0][0] = 0;

    for($i = 1; $i <= $len_a; $i++) {
        for($j = 1; $j <= $len_b; $j++) {
            if($strA[$i -1] == $strB[$j - 1]) {
                $temp[$i][$j] = $temp[$i - 1][$j - 1];
            } else {
                $temp[$i][$j] = min($temp[$i - 1][$j], $temp[$i][$j - 1], $temp[$i - 1][$j - 1]) + 1;
            }
        }
    }
    return $temp[$len_a][$len_b];
}


if (! function_exists ( 'to_array' )) {

    /**
     * Convert Object Array To Array Recursively
     *
     * @param unknown $arr
     */
    function to_array(&$arr) {
        $arr = ( array ) $arr;
        $arr && array_walk ( $arr, function (&$v, $k) {
            $v = ( array ) $v;
        } );
    }
}


if (! function_exists ( 'counter' )) {

    /**
     * A Counter Achieve By Static Function Var
     *
     * @return number
     */
    function counter() {
        static $c = 0;

        return $c ++;
    }
}




if (! function_exists ( 'randStr' )) {
    function randStr($len = 6, $format = 'NUMBER') {
        switch ($format) {
            case 'ALL' :
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-@#~';
                break;
            case 'CHAR' :
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz-@#~';
                break;
            case 'NUMBER' :
                $chars = '0123456789';
                break;
            default :
                $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                break;
        }
        // mt_srand ( ( double ) microtime () * 1000000 * getmypid () );
        $password = "";
        while ( strlen ( $password ) < $len )
            $password .= substr ( $chars, (mt_rand () % strlen ( $chars )), 1 );
        return $password;
    }
}


if (! function_exists('funcCache')) {

    /**
     * Cache function result and reflush every 5 second
     * Send a commend and unset the selected key
     *
     * @param unknown $func
     * @param unknown $params
     * @return mixed
     */
    function funcCache($func, $params = [], $expire = 5)
    {
        // TODO : Release expired keys
        static $_cached = [];
        $key = sha1(json_encode([
            $func,
            $params
        ]));
        if ($expire == '[clear]') {
            unset($_cached[$key]);
            return;
        }
        $time = time();
        if (isset($_cached[$key]) && $_cached[$key]['expire'] > $time) {
            $result = $_cached[$key]['result'];
        } else {
            $result = call_user_func_array($func, $params);
            $_cached[$key] = [
                'result' => $result,
                'expire' => $time + $expire
            ];
        }
        return $result;
    }
}

if (! function_exists('toFix')) {

    /**
     * 生成随机字符串
     *
     * @param unknown_type $len
     *            长度
     * @param unknown_type $format
     *            内容类别，ALL,CHAR,NUMBER
     */
    function toFix($val, $precision = 2)
    {
        return sprintf('%.' . $precision . 'f', $val); // round($val,$precision);
    }
}


if (! function_exists('createSerialNum')) {

    /*
     * 创建流水号
     */
    function createSerialNum($num = 18)
    {
        list ($usec, $sec) = explode(" ", microtime());

        $usec = (int) ($usec * 1000000);

        $str = $sec . $usec . mt_rand(100000, 999999);

        $str = substr($str, 0, $num);

        if (strlen($str) < $num) {
            $str = str_pad($str, $num, mt_rand(100000, 999999));
        }

        return $str;
    }
}



if(! function_exists('last_30_days')){


    function get_month_day($year,$month){
        if($month <= 7){
            if($month == 2){
                return ($year % 400 == 0 || ($year % 100 != 0 && $year % 4 == 0 )) ? 29 :28;
            }else{
                return $month % 2 ? 31 : 30;
            }
        }else{
            return $month % 2 ? 30 : 31;
        }
    }


    function last_n_days($n = 30,$now = ''){
        $now || $now = date('Y-m-d');
        $t = explode('-', $now);
        $stack[] = [
            intval($t[0]),
            intval($t[1]),
            intval($t[2]),
        ];
        $i = 0;
        while ($n > $stack[$i][2]){
            $n -= $stack[$i][2];
            $prev_m = ($stack[$i][1] - 1) ? ( $stack[$i][1] -1) : 12;
            $prev_y = $prev_m == 12 ? ($stack[$i][0] - 1): $stack[$i][0] ;
            $stack[$i + 1] = [
                $prev_y,
                $prev_m,
                get_month_day($prev_y, $prev_m)
            ];
            $i ++;
        }
        $result = [];
        for ($j = $i; $j >= 0 ; $j -- ){
            $k = $j == $i ? ( $stack[$i][2] - $n + 1) : 1;
            for(;$k <= $stack[$j][2]; $k ++){
                $result[] = sprintf('%d-%02d-%02d',$stack[$j][0],$stack[$j][1],$k);
            }
        }
        return $result;
    }
}



if(! function_exists('chineseWord')){

    function chineseWord(){
        $str = '苗疆素来以蛊毒瘴气闻名多鬼狐精怪之事而其核心地十万大山更是神秘无比人迹罕至处古树高耸老藤如龙岳巍峨河流壮阔一派蛮荒的风貌深座脚下此时阵异歌声飘荡出：“王叫我巡喽完南北吆仿佛踏行道上现了个獐头鼠脑干瘦少年欢快唱着那谣眼珠子滴溜旋转给种极度明感觉令骇然他并非徒步胯有纯黑色皮毛豹副蔫样垂丧驮赶路许这扰兴致止住恶狠俯瞰身你懒散货前方便最后要查寨刻钟看到门否则会禀报想烧烤只幽灵墨趣呢两字似乎某魔力原本进浑忍不颤抖眸顿显惶恐态形纵已化作残影消失在留连串咒骂回虚空畜生慢点家铁柱爷掉足遍布盆内四平八稳端坐整理皱巴衣衫才倨傲喝葛些滚早等候伴随嘎吱打开中鱼贯群为首乃袍肥胖者毫犹豫率领众跪伏讨好九天青羽主拜见使祝敌岁咧嘴笑莫废话月们诸帮提供贡品可曾准备充？切妥当请放小意您务必纳恭敬拿物双手奉睛亮脸露满伸取将入怀安得伙果亏待定面说几句言诚模很激动真太客数直混乱寇飞贼都啊若统实施仁慈政策何能走劳酬没间马屁腿夹再速踪确所尽闭塞始仅活也盘踞强盗却因发改变自幼被养育屠戮带妹侥幸逃凭借与狡诈辣性格女孩加日过刀肉般虽堪称胎食牛穷久展计做成立雄略断扩张杀股匪死五囊知晓奇术妖管什么邪竟奈震撼情鼎凶响彻近千碧祖二暴虐代表瑶善良她劝皆祥和需隔腹片澄净清澈湖泊畔壁株松苍翠条虬绿草茵尊香巨型铜三耳符文膝富正屈指弹缕火焰尖涌汇聚于悸热浪严丝合缝掩盖依旧扑鼻让孔舒通体服远站滔息望威猛霸别赫沉吟摆谱音刚落视跑谄媚潮汹念经传颂功德采对崇江水绵绝穿挪硕骚包抹额滋润分颜悦交办吩咐敢阿谀揖答案错起观烹煮美味吧七炉药金狮期嘿希突破瓶颈饕餮吞噬段引轩波存横法惊沦愿偿口又欲仙爽坏哥应今陪儿去玉菇怎崖就脆寒恨淡紫织锦腰束盈握肢乌秀编俏辫插枚桃花簪雪巧虹鞋皓白腕挂银圈尴尬乖顺像羊缩脖弱根据猜测关重底哼琼饶郁闷差哭哀怨怕宠溺噗嗤轻吐兰啦次算例听痛涕暖鸟由轰隆雷蒸腾霞光瑞彩澎湃紧跟氤氲赤雾冲同黄铸慑鬓耀爆炸冰冷宛祇降怒吼哈象丹袖掀璀璨燃挥抓摄呈终炼制浓汁淌简骨酥麻浩瀚粹量升脱换蜕越诞伦元相己枷锁谁隐晦闪烁陷暗锅倒扣振聋聩霆蜿蜒际电籍记载劫还罚呼瘫软茫渺沧海粟玩';
        $chineseWord = [];
        for($i = 0 ; $i < mb_strlen($str) ; $i ++){
            $sub = mb_substr($str, $i,1);
            $chineseWord[] = $sub;
        }
        return $chineseWord;
    }

    function randomChineseName(){
        $word = chineseWord();
        $count = count($word);
        $pos1 = random_int(0, $count -1 );
        $pos2 = random_int(0, $count -1 );
        $pos3 = random_int(0, $count -1 );
        return $word[$pos1].$word[$pos2].$word[$pos3];
    }

    function rdPhone(){

        // /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}

        $data = [
            '13',
            '15',
            '17',
            '18',
            '14',
        ];

        $h = $data [random_int(0, 4)];
        $t = random_int(100000000, 999999999);
        return $h.$t;
    }

    function chineseWordGenerate($str){
        $punctuation = ['，','”','.','！','。'];
        $chineseWord = [];
        for($i = 0 ; $i < mb_strlen($str) ; $i ++){
            $sub = mb_substr($str, $i,1);
            if(preg_match("/[\x7f-\xff]/", $sub) && !in_array($sub, $punctuation)){
                $chineseWord[] = $sub;
            }
        }
        $chineseWord = array_unique($chineseWord);

        foreach ($chineseWord as $v){
            echo $v;
        }
        exit;
        preArrayKV($chineseWord);
        exit;
    }

}


// /**
//  * 设置msg回调
//  * @param string $callback
//  * 	回调方法
//  * @param array $params
//  * 	回调参数
//  * @return multitype:multitype:string unknown
//  */
// public static function callback($callback = NULL,array $params = array()) {
//     static $_callback  = [];
//     if(is_callable($callback)){
//         $_callback[] =['func'=>$callback,'param'=>$params] ;
//     }
//     return $_callback;
// }

// /**
//  * 触发msg回调
//  * @param unknown $context
//  */
// public static function fireCallback($context) {
//     $_callback = self::callback();
//     foreach ($_callback as $callback){
//         $result = call_user_func_array($callback['func'], array($context,$callback['param']));
//         if($result === false) break;
//     }
// }

