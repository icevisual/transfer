<?php

namespace App\Gather;

/**
 * AES Encrypt And Decrypt
 * @author Administrator
 *
 */
class AESTool{

    /**
     * 设置默认的加密key
     * @var str
     */
    public static $defaultKey = 'e10adc3949ba59abbe56e057f20f883e';

    public $secretKey = '';

    /**
     * 设置默认加密向量
     * @var str
     */
    private $iv = 'e10adc3949ba59a1';

    /**
     * 设置加密算法
     * @var str
     */
    private $cipher;

    /**
     * 设置加密模式
     * @var str
     */
    private $mode;

    public function __construct($cipher = MCRYPT_RIJNDAEL_128, $mode = MCRYPT_MODE_CBC){
        $this->cipher = $cipher;
        $this->mode = $mode;
    }

    public function setSecretKey($key){
        $this->secretKey = md5($key) ;
    }

    public function getSecretKey(){
        return $this->secretKey ? $this->secretKey : static::$defaultKey;
    }


    /**
     * 对内容加密，注意此加密方法中先对内容使用padding pkcs7，然后再加密。
     * @param str $content    需要加密的内容
     * @return str 加密后的密文
     */
    public function encrypt($content){
        if(empty($content)){
            return false;
        }
        $srcdata = $content;
        $block_size = mcrypt_get_block_size($this->cipher, $this->mode);
        $padding_char = $block_size - (strlen($content) % $block_size);
        $srcdata .= str_repeat(chr($padding_char),$padding_char);
        $resultData =  mcrypt_encrypt($this->cipher, $this->getSecretKey(), $srcdata, $this->mode, $this->iv);
        return base64_encode($resultData);
    }

    /**
     * 对内容解密，注意此加密方法中先对内容解密。再对解密的内容使用padding pkcs7去除特殊字符。
     * @param String $content    需要解密的内容
     * @return String 解密后的内容
     */
    public function decrypt($content){
        if(empty($content)){
            return false;
        }
        $content = base64_decode($content);
        if($content === false) {
            throw new \Exception('Failed To Decode Received Data Using base64_decode');
        }
        $content = mcrypt_decrypt($this->cipher, $this->getSecretKey(), $content, $this->mode, $this->iv);
        $block = mcrypt_get_block_size($this->cipher, $this->mode);
        $pad = ord($content[($len = strlen($content)) - 1]);
        return substr($content, 0, strlen($content) - $pad);
    }


    public static function main(){
         
        $AESTool = new AESTool();

        $AESTool->setSecretKey('icevisual');

        $sourceFileName = 'secret.txt';
        
        $sourceFileContent = file_get_contents($sourceFileName);
        
        $encoding = detect_encoding($sourceFileContent);
        
        $encryptedData = $AESTool->encrypt($sourceFileContent);
        
        $storeFileName = 'secret.ept';
        
        file_put_contents($storeFileName, $encryptedData);
        
        $encryptedFileContent = file_get_contents($storeFileName);
        
        $decryptedData = $AESTool->decrypt($encryptedFileContent);
        
//         $content = iconv('ASCII','UTF-8', $content);
        header('Content-type:text/html;charset='.$encoding);
        
        echo($decryptedData);
        
        exit;
    }
}

		