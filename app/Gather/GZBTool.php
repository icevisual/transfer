<?php

namespace App\Gather;



define('TRACELOG', TRUE);
define('TRACELOGPATH', storage_path().'/trace/');
define('TRACELOG_ECHO', FALSE);

/**
 * Common Tool
 * @author Administrator
 *
 */
class CommonTool{
	
	
	public static function log($title,$msg) {
		if(TRACELOG === FALSE) return ;
		static $referer = '';
		static $date_point = [];
		$message = [];
		$date = date ( "Y-m-d H:i:s" ) ;
		if(!isset($date_point[$date])){
			$date_point[$date] = 1;
			$message[] = $date;
		}else{
			$date_point[$date] ++ ;
		}
		$step = $date_point[$date];
		
		if(!$referer){
			if (isset ( $_SERVER ['HTTP_REFERER'] )) {
				$referer = $_SERVER ['HTTP_REFERER'];
			} elseif (isset ( $_SERVER ['HTTP_HOST'] )) {
				$referer = $_SERVER ['HTTP_HOST'] . $_SERVER ['REQUEST_URI'];
			} else {
				$referer = 'Unknow';
			}
			$message[] = $referer;
		}
		if($message){
			$message = '['.implode(']-[', $message).']'."\n";
		} else {
			$message = '';
		}
		if(!is_dir(TRACELOGPATH)){
			throw new \Exception('TRACELOGPATH Not Found');
		}
		$filePath = TRACELOGPATH . date ( "Ymd" );
		$msg = $message.$step. ".[$title]-[ $msg ]\n";
		file_put_contents ( $filePath, $msg, FILE_APPEND );
		@chmod ( $filePath, 0777 );
		if (TRACELOG_ECHO) {
			echo $msg;
		}
	}
	
	
	public static function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $length; $i ++) {
			$str .= $chars{mt_rand ( 0, 61 )};
		}
		return $str;
	}
	
	/**
	 * 
	 * @param unknown $data
	 * @return string
	 */
	public static function dataString(array $data){
		ksort($data);
		$string = '';
		foreach ($data as $k => $v){
			$string .= '&'.$k.'='.$v;
		}
		$string = substr($string, 1);
		return $string;
	}
	
	public static function dataSummary($data){
		$string = static::dataString($data);
		$signature = sha1 ( $string );
		$data['summary'] = $signature;
		ksort($data);
		//sign_types summary
		return $data;
	}
	
	public static function checkSummary(array $data){
		if(isset($data['summary'])){
			$signature_send = $data['summary'];
			\CommonTool::log('signature_send',$signature_send);
			unset($data['summary']);
			$signature =  sha1(static ::dataString($data));
			\CommonTool::log('signature',$signature);
			return $signature_send == $signature;
		}
		return false;
	}
	
	public static function dataProcess(array $data){
		//TODO : check data
		$data['timestamp'] 	= time ();
		$data['nonceStr'] 	= static::createNonceStr ();
		\CommonTool::log('nonceStr',$data['nonceStr']);
// 		ksort($data);
		$data = static ::dataSummary($data); 
		return json_encode($data);
	}
}




/**
 * Rsa Encrypt Decrypt And Sign
 * @author Administrator
 *
 */
class RsaTool {
	
	/**
	 * 我的私钥
	 * @var unknown
	 */
	private $_privKey;
	/**
	 * client公钥
	 * @var unknown
	 */
	private $_client_pubKey;
	
	private $_privPath;
	private $_pubPath;
	
	
	/**
	 * 
	 * @param unknown $path1
	 * 	privKeyPath Or KeyBasePath
	 * @param string $path2
	 * 	pubKeyPath 
	 * @throws \Exception
	 */
	public function __construct($path1,$path2 = '' ) {
		if (empty ( $path1 ) ) {
			throw new \Exception ( 'Key Set Path Is Required' );
		}
		if( is_dir ( $path1 )){
			$this->_privPath = $path1. DIRECTORY_SEPARATOR . 'priv.pem';
			$this->_pubPath = $path1. DIRECTORY_SEPARATOR . 'cli-pub.pem';
		}else if ( is_file( $path1 ) && is_file( $path2 ) ){
			$this->_privPath = $path1;
			$this->_pubPath = $path2;
		}else{
			throw new \Exception ( 'Valid Path Or File Is Required' );
		}
	}

	/**
	 * Create New RAS Keys
	 * @return multitype:unknown
	 */
	public static function createKey($privKeyPath,$pubKeyPath) {
		$r = openssl_pkey_new ([
				'private_key_bits' => 1024,
				'private_key_type' => OPENSSL_KEYTYPE_RSA,
		]);
		openssl_pkey_export ( $r, $privKey );
		file_put_contents ( $privKeyPath, $privKey );
		$rp = openssl_pkey_get_details ( $r );
		$pubKey = $rp ['key'];
		file_put_contents ( $pubKeyPath, $pubKey );
		return [
				'privKey' => $privKey,
				'pubKey' => $pubKey,
		];
	}
	
	/**
	 * Set Client Public Key 
	 * @param unknown $data
	 * @throws \Exception
	 * @return boolean
	 */
	public function setupClientPubKey($data){
		if (is_resource ( $this->_client_pubKey)) {
			return true;
		}
		if(is_string($data)){
			if(is_file($data)){
				$prk = file_get_contents ( $data );
				$this->_client_pubKey = openssl_pkey_get_public( $prk );
			}else{
				$this->_client_pubKey = openssl_pkey_get_public( $data );
			}
			return true;
		}
		throw new \Exception(__FUNCTION__.' expects Parameter 1 to be string');
	}
	
	
	/**
	 * setup the private key
	 */
	public function setupPrivKey() {
		if (is_resource ( $this->_privKey )) {
			return true;
		}
		$prk = file_get_contents ( $this->_privPath );
		$this->_privKey = openssl_pkey_get_private ( $prk );
		return true;
	}
	/**
	 * setup the public key
	 */
	public function setupPubKey() {
		if (is_resource ( $this->_client_pubKey )) {
			return true;
		}
		$puk = file_get_contents ( $this->_pubPath );
		$this->_client_pubKey = openssl_pkey_get_public ( $puk );
		return true;
	}
	
	
	protected function encrypt($data, $key, $type) {
		if (! is_string ( $data )) {
			throw new \Exception ( 'String Is Needed!' );
		}
		if ($type == 'PRIVATE') {
			$r = openssl_private_encrypt ( $data, $encrypted, $key );
		} else {
			$r = openssl_public_encrypt ( $data, $encrypted, $key );
		}
		if ($r) {
			return base64_encode ( $encrypted );
		} else {
			throw new \Exception ( openssl_error_string () );
		}
	}
	
	/**
	 * decrypt with the private key
	 */
	protected  function decrypt($encrypted,$key,$type) {
		if (! is_string ( $encrypted )) {
			throw new \Exception ( 'String Is Needed!' );
		}
		$encrypted = base64_decode ( $encrypted );
		if ($type == 'PRIVATE') {
			$r = openssl_private_decrypt ( $encrypted, $decrypted, $key );
		} else {
			$r = openssl_public_decrypt( $encrypted, $decrypted, $key );
		}
		
		if ($r) {
			return $decrypted;
		} else {
			throw new \Exception ( openssl_error_string () );
		}
	}
	
	
	/**
	 * encrypt with the private key
	 */
	public function privEncrypt($data) {
		$this->setupPrivKey ();
		return $this->encrypt($data, $this->_privKey, 'PRIVATE');
	}
	/**
	 * decrypt with the private key
	 */
	public function privDecrypt($encrypted) {
		$this->setupPrivKey ();
		return $this->decrypt($encrypted, $this->_privKey, 'PRIVATE');
	}
	
	/**
	 * encrypt with public key
	 */
	public function pubEncrypt($data) {
		$this->setupPubKey ();
		return $this->encrypt($data, $this->_client_pubKey, 'PUBLIC');
	}
	
	/**
	 * * decrypt with the public key
	 */
	public function pubDecrypt($crypted) {
		$this->setupPubKey ();
		return $this->decrypt($crypted, $this->_client_pubKey, 'PUBLIC');
	}
	
	/**
	 * 生成签名
	 *
	 * @param string 签名材料
	 * @param string 签名编码（base64）
	 * @return 签名值
	 */
	public function sign($data){
		$ret = false;
		$this->setupPrivKey();
		if (openssl_sign($data, $ret, $this->_privKey)){
			$ret = base64_encode($ret);
		}
		return $ret;
	}
	
	/**
	 * 验证签名
	 *
	 * @param string 签名材料
	 * @param string 签名值
	 * @param string 签名编码（base64/hex/bin）
	 * @return bool
	 */
	public function verify($data, $sign){
		$ret = false;
		$this->setupPubKey();
		$sign = base64_decode($sign);
		if ($sign !== false) {
			switch (openssl_verify($data, $sign, $this->_client_pubKey)){
				case 1: $ret = true; break;
				case 0:
				case -1:
				default: $ret = false;
			}
		}
		return $ret;
	}
	
	public function __destruct() {
		@ fclose ( $this->_privKey );
		@ fclose ( $this->_client_pubKey );
	}
}

/**
 * 发送和接受请求
 * @author Administrator
 *
 */
class RsaWorker{
	
	private $AES ;
	private $AES_secret ;
	
	private $RSA ;
	
	private $privKeyPath;
	private $pubKeyPath;
	
	public function __construct($privKeyPath , $pubKeyPath){
		$this->AES = new \AESTool();
		$this->init($privKeyPath,$pubKeyPath);
	}
	
	public function sendData ($data){
		$data_string = \CommonTool::dataProcess($data);
		\CommonTool::log('data_string',$data_string);
		$this->AES_secret = \CommonTool::createNonceStr(6);
		\CommonTool::log('AES_secret',$this->AES_secret);
		$this->AES->setSecretKey($this->AES_secret);
		$encrypted_data 	= $this->AES->encrypt($data_string);
		\CommonTool::log('encrypted_data',$encrypted_data);
		$sendData ['data'] 	= $encrypted_data;
		$sendData ['key'] 	= $this->RSA->pubEncrypt($this->AES_secret);
		$sendData ['signature'] = $this->RSA->sign($encrypted_data);
		//TODO :digital signature
		return $sendData;
	}
	
	public function receiveData (array $sendData){
		$data_struct = ['data','key','signature'];
		if(array_diff($data_struct, array_keys($sendData))){
			throw new \Exception('Data Structure Error');
		}
		try {
			$AES_secret = $this->RSA->privDecrypt($sendData['key']);
			\CommonTool::log('AES_secret',$AES_secret);
		}catch(\Exception $e){
			throw new \Exception('Failed To Decode AES Secret');
		}
		if($this->RSA->verify($sendData['data'], $sendData['signature']) === false){
			throw new \Exception('Check Signature Failed');
		}
		$this->AES_secret = $AES_secret;
		$this->AES->setSecretKey($this->AES_secret );
		$data = $this->AES->decrypt($sendData['data']);
		\CommonTool::log('decrypted_data',$data);
		$data = json_decode($data,true);
		if(json_last_error() == JSON_ERROR_NONE){
			if(\CommonTool::checkSummary($data)){
				return $data;
			}
			throw new \Exception('CheckSignature Failed');
		}else{
			throw new \Exception('Received Data json_decode Failed');
		}
	}
	
	public function init($privKeyPath,$pubKeyPath){
		$this->privKeyPath = $privKeyPath;
		$this->pubKeyPath = $pubKeyPath;
		if(!is_file($this->privKeyPath) || !is_file($this->pubKeyPath) ){
			throw new \Exception('Can\'t Find Private Key Or Public Key!');
		}
		$this->RSA = new \RsaTool ( $this->privKeyPath,$this->pubKeyPath);
	}
}

