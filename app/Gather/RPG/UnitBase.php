<?php
namespace App\Gather\RPG;

class UnitBase implements \ArrayAccess{

	protected  $_attr ;

	protected  $_name ;

	public static $_attributes = [
			'HP' => 100,
			'attack' => 1,
			'defence' => 1,//伤害减少 （装甲值 * 0.06）／（装甲值 * 0.06 ＋ 1）
			'hit rate'	=> 10, //攻击命中率
			'crit rate'	=> 1, //暴击率
			'dodge rate' => 1, //闪避率
			'attack speed' => 1, //attack 1 time  per second
	];
	
	
	public static $_show_conf = [
	    'InjuredMessage' => 1,
	    'DiedMessage' => 1,
	];
	

	public function init(){
		$this->_attr = static::$_attributes;
	}

	public function __construct($name ,array $initData = []){
		$this->_name = $name;
		if(empty($initData)){
			$this->init();
		}else {
			$initData = $initData + self::$_attributes;
			if(count($initData) > count(self::$_attributes) ){
				throw new \Exception('Attr Number Error');
			}
			$this->_attr = $initData;
		}
	}
	
	
	protected function runShow($message){
	    echo $message;
	}
	
	
	protected function show($message){
	    
	    if (version_compare(PHP_VERSION, '5.3.6', '>=')) {
	        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS,2);
	    } else {
	        $backtrace = debug_backtrace(false,2);
	    }
	    array_shift($backtrace);
	    $function = $backtrace[0]['function'];
	    
	    $segments = explode('#', preg_replace('/[A-Z]/', '#\\0', $function));
	    
	    $cfgPropertyName = "_{$segments[0]}_conf";
	    
	    $ReflectionObject = new \ReflectionObject($this);
	    
	    if( $ReflectionObject->hasProperty($cfgPropertyName)){
	        $property = $ReflectionObject->getProperty($cfgPropertyName);
	        $cfg =  $property->getValue();
	        
	        $cfgKey = str_replace($segments[0], '', $function);
	        
	        if(isset($cfg[$cfgKey]) && $cfg[$cfgKey]){
	            return $this->runShow($message);
	        }
	        return false;
	    }
	    return $this->runShow($message);
	}

	
	public function showInjuredMessage($currHP,$damageHP){
	    $decreaseHP = $damageHP > $currHP ? $damageHP : $currHP;
	    $restHP = $damageHP > $currHP ? 0 : ($currHP - $damageHP);
	    $message = $this->_name.' is hurted at '.
	        $damageHP.' Point , decrease '.
	        $decreaseHP.' HP,Rest HP '.$restHP.PHP_EOL;
	    return $this->show($message);
	}
	public function injured($damage){
	    $this->showInjuredMessage($this['HP'], $damage);
		$this['HP'] -= Utils::toFix($damage,4);
		if($this['HP'] <= 0 ){
			$this->died();
		}
	}
	
	
	public function showLiveMessage(){
	    $message = '';
	    return $this->show($message);
	}
	

	public function alive(){
		return $this['HP'] > 0 ;
	}
	
	
	public function showDiedMessage(){
	    $message = $this->_name.' Died'.PHP_EOL;
	    return $this->show($message);
	}
	

	public function died(){
		$this->showDeadMessage();
	}

	public function getName(){
		return $this->_name;
	}

	public function __get($key){
		if(isset($this->_attr[$key])){
			return $this->_attr[$key];
		}
		return false;
	}


	public function getAttack(){
		$waveRange = mt_rand(1,100) > 50 ? -1 : 1;
		return $this->_attr['attack'] + $waveRange * mt_rand(1,10);
	}
	
	public function getHp(){
	    return $this->_attr['HP'] >= 0 ? $this->_attr['HP'] : 0 ;
	}

	/**
	 * @param offset
	 */
	public function offsetExists ($offset) {
		return isset($this->_attr[$offset]);
	}

	/**
	 * @param offset
	 */
	public function offsetGet ($offset) {
		$method = 'get'.ucfirst($offset);
		if(method_exists($this, 'get'.ucfirst($offset))){
			return call_user_func(array($this,$method));
		}
		return isset($this->_attr[$offset]) ? $this->_attr[$offset] : null;
	}

	/**
	 * @param offset
	 * @param value
	 */
	public function offsetSet ($offset, $value) {
		$this->_attr[$offset]  = $value;
	}

	/**
	 * @param offset
	 */
	public function offsetUnset ($offset) {
		unset($this->_attr[$offset]);
	}

}
