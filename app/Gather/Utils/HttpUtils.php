<?php
namespace App\Gather\Utils;

class HttpUtils
{

    const ERR_NOT_SUPPORT_EXT = 0;

    public function handleException($code, $message)
    {
        throw new \Exception($message, $code + 67890);
    }

    public function UrlAnalysis($url)
    {
        $pathinfo = pathinfo($url);
        $originFilename = $pathinfo['filename'];
        $originExtension = array_get($pathinfo, 'extension', 'jpg');
        $extension = '';
        $acceptExtensions = [
            'png',
            'jpg',
            'gif',
            'jpeg'
        ];
        // .jpg!200x200.jpg
        // 2.jpg?size=268x268"
        // _CfHUa.thumb.200_200_c.jpeg
        // /d5/original_200_200.jpg
        $pattern = [
            '/\.(png|jpg|gif|jpeg)!(\d*x\d*)\.\\1$/',
            '/\.(png|jpg|gif|jpeg)\?size\=(\d*x\d*)$/',
            '/\.thumb\.\d*_\d*(?:_c)?\.(png|jpg|gif|jpeg)$/',
            '/_\d*_\d*\.(png|jpg|gif|jpeg)$/'
        ];
        foreach ($pattern as $ptn){
            if (preg_match($ptn, $url, $matches)) {
                $url = substr($url, 0, 0 - strlen($matches[0])) . '.' . $matches[1];
                break;
            }
        }
        
        $acceptExtensions = array_flip($acceptExtensions);
        if (! isset($acceptExtensions[$extension = $originExtension])) {
            if (! isset($acceptExtensions[$extension = substr($originExtension, 0, 3)])) {
                if (! isset($acceptExtensions[$extension = substr($originExtension, 0, 4)])) {
                    $this->handleException(SELF::ERR_NOT_SUPPORT_EXT, '不支持扩展名');
                    return false;
                }
            }
        }
        
        $url = str_replace(" ", "%20", $url);
        
        return [
            'url' => $url,
            'originName' => $originFilename,
            'extension' => $extension
        ];
    }

    public function httpDownloadSha1($url, $filePath = "Download", $timeout = 60)
    {
        ! is_dir($filePath) && @mkdir($filePath, 0755, true);
        $info = $this->UrlAnalysis($url);
        extract($info);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        $User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
        curl_setopt($ch, CURLOPT_USERAGENT, $User_Agen); // 用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 跟踪301
        $temp = curl_exec($ch);
        if (! curl_error($ch)) {
            if ($temp{0} == '<') {
                return false;
            }
            curl_close($ch);
            $sha1 = sha1(base64_encode($temp));
            $fileName = $filePath . '/' . $sha1 . '.' . $extension;
            if (! is_file($fileName)) {
                
                try {
                    file_put_contents($fileName, $temp);
                } catch (\Exception $e) {
                    throw $e;
                    return false;
                }
                return $fileName;
            } else {
                return false;
            }
        } else {
            dump(curl_errno($ch));
            dump(curl_error($ch));
            curl_close($ch);
            return false;
        }
    }

    public function call_curl_reconnect($url, array $data = [], $json = true)
    {
        $t_count = 10;
        $count = $t_count;
        while ($count) {
            $result = curl_get($url, $data, $json);
            if ($result) {
                return $result;
            }
            $slpSecd = intval(sqrt($t_count - $count) * 1000000);
            dump($slpSecd);
            usleep($slpSecd);
            // sleep($seconds);
            $count --;
        }
    }

    public function curl_get($url, array $data = [], $json = true)
    {
        // $api = 'http://v.showji.com/Locating/showji.com20150416273007.aspx?output=json&m='.$phone;
        $ch = curl_init();
        if (! empty($data)) {
            $url = $url . '?' . http_build_query($data);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 设置超时
                                              // curl_setopt($ch, CURLOPT_USERAGENT, $User_Agen); //用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 跟踪301
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 返回结果
        $info = curl_exec($ch);
        // echo curl_errno($ch);
        // echo curl_error($ch);
        curl_close($ch);
        return $json ? json_decode($info, 1) : $info;
    }

    public function curl_post($url, array $data, $json = true)
    {
        // $cookie_file = 'cookie.txt';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); // url
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        $User_Agen = 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 Safari/537.36';
        curl_setopt($ch, CURLOPT_USERAGENT, $User_Agen);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        // curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file); //存储cookies
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // 数据
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $info = curl_exec($ch);
        curl_close($ch);
        return $json ? json_decode($info, 1) : $info;
    }

    public function curl_multi_request($query_arr, $data, $method = 'POST')
    {
        $ch = curl_multi_init();
        $count = count($query_arr);
        $ch_arr = array();
        for ($i = 0; $i < $count; $i ++) {
            $query_string = $query_arr[$i];
            $ch_arr[$i] = curl_init($query_string);
            curl_setopt($ch_arr[$i], CURLOPT_RETURNTRANSFER, true);
            
            curl_setopt($ch_arr[$i], CURLOPT_POST, 1);
            curl_setopt($ch_arr[$i], CURLOPT_POSTFIELDS, $data); // post 提交方式
            
            curl_multi_add_handle($ch, $ch_arr[$i]);
        }
        $running = null;
        do {
            curl_multi_exec($ch, $running);
        } while ($running > 0);
        for ($i = 0; $i < $count; $i ++) {
            $results[$i] = curl_multi_getcontent($ch_arr[$i]);
            curl_multi_remove_handle($ch, $ch_arr[$i]);
        }
        curl_multi_close($ch);
        return $results;
    }
}



