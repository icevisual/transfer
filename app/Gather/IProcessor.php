<?php

/**
 * 通用类
 *
 */
namespace App\Gather;


class IProcessor {
	
	/**
	 * 获取方法注释
	 * @param unknown $methodName
	 * @return multitype:
	 */
	public static function getMethodAnnotation($methodName){
		$export = \ReflectionMethod::export(__CLASS__, $methodName,1);
		$export = explode('Method [ ', $export);
		return "\t".$export[0];
	}
	
	/**
	 * 自动生成const变量接口
	 */
	public static function autoGeneration(){
		$ReflectionClass 	= new \ReflectionClass(__CLASS__);
// 		$ConstantsArray 	= $ReflectionClass->getConstants();
		$InterfaceNames 	= $ReflectionClass->getInterfaceNames();
		$ReflectionInter 	= new \ReflectionClass($InterfaceNames[0]);
		$interConstsArray 	= $ReflectionInter->getConstants();
		$methodsArray 		= $ReflectionClass->getMethods();
		$processorConstsArray = [];
		foreach ($methodsArray as $k => $v){
			$methodName = $v->getName();
			
			if(strpos( $methodName,'do') === 0){
				$name = substr($methodName, 2);
				$processorConstsArray[strtoupper($name)] = ucfirst($name);
			}
		}
		$namespace 			= $ReflectionClass->getNamespaceName();
		$diffArray 			= array_diff($processorConstsArray,$interConstsArray);
		if($diffArray){
			$constString = '';
			$interFileName = __DIR__.'/InfoProcessorConstInter.php';
			$eol = PHP_EOL;
// 			$eol = "\r\n";
			foreach ($processorConstsArray as $k => $v){
				$ann = self::getMethodAnnotation('do'.$v);
				$constString .= "$ann	const  $k = '$v';$eol$eol";
			}
			//有区别,重新生成
			$newContent =<<<EOF
<?php

/**
 * Auto 
 */
namespace $namespace;

interface InfoProcessorConstInter {

$constString
			
}
			

EOF;
			
			@file_put_contents($interFileName, $newContent);
		}
	}
	
	
	/**
	 * External Api for processor
	 * @param string $prcessor
	 * @param array $userDate
	 * @throws \Exception
	 * @return array
	 */
	public function process($prcessor,array $userDate){
		$processor_name = 'do'.ucfirst($prcessor);
		if(is_string($prcessor)  && method_exists($this, $processor_name)){
			$ref 	= new \ReflectionMethod(static::class,$processor_name);
			$params = [];
			foreach ($ref->getParameters() as $k => $v){
				$vName = $v->getName();
				if(isset($userDate[$vName])){
					$params[$vName] = $userDate[$vName];
				}
			}
			return call_user_func_array([$this,$processor_name], $params);
		}
		throw new \Exception('Processor Not Found');		
	}
	
	
	/**
	 * Result data
	 * @var array
	 */
	protected $_result = [];
	
	/**
	 * Stop status
	 * @var boolean
	 */
	protected $_stop = false;
	
	
	/**
	 * Process data
	 * @var array
	 */
	protected $_parameters = [];
	
	
	
	/**
	 * Check the stop status
	 * @return boolean
	 */
	protected function isStop (){
		return $this->_stop ;
	}
	
	/**
	 * Set process data
	 * @param unknown $data
	 * @return \Lib\Fun\IProcessor
	 */
	public function data($data){
		$this->_parameters = $data;
		return $this;
	}
	
	/**
	 * Set process result
	 * @param unknown $key
	 * @param unknown $data
	 */
	protected function setResult($key,$data){
		$this->_result[$key] = $data;
	}
	
	/**
	 * Get process results
	 * @return array
	 */
	public function prs(){
		$this->_stop = false;
		return $this->_result;
	}
	
	
	/**
	 * Internal procedure of processor
	 * @param string $prcessor
	 * @param array $data
	 * @return array
	 */
	protected function _process($prcessor,array $data = []){
		$processData = [];
		if(empty($data)){
			$processData = $this->_parameters;
		}else{
			$processData = $data + $this->_parameters;
		}
		$result = $this->process($prcessor, $data);
		$this->setResult($prcessor, $result);
		return $result;
	}
	
	
	/**
	 * Go across a processor regardless  the result
	 * @param string $prcessor
	 * @param array $data
	 * @return \Lib\Fun\IProcessor
	 */
	public function across($prcessor,array $data = []){
		if($this->isStop() === false){
			$this->_process($prcessor, $data);
		}
 		return $this;
	}
	
	/**
	 * Go through processor , stop when failed
	 * @param string $prcessor
	 * @param array $data
	 * @return \Lib\Fun\IProcessor
	 */
	public function through($prcessor,array $data = []){
		if($this->isStop() === false){
			$result = $this->_process($prcessor, $data);
			if(static ::_failed($result) ){
				$this->_stop = true;
			}
		}
		return $this;
	}
	
	/**
	 * Judge failure from the result data
	 * @param array $result
	 * @return boolean
	 */
	public static function _failed (array $result ){
		return $result['decision'] == 0;
	}
	
	/**
	 * Judge success from the result data
	 * @param array $result
	 * @return boolean
	 */
	public static function _success (array $result ){
		return $result['decision'] == 1;
	}
	
}