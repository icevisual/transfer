<?php
namespace App\Gather\RPG;

class RPGCommon {

	public static function trace($msg){
		static $_trace = [];

		$_trace[] = $msg;
	}


	public static function getGCD($a,$b){
		$big = $a > $b ? $a : $b;
		$small = $a >= $b ? $b : $a;
		$mod = $big%$small ;
		while ($mod > 0 ){
			$big = $small;
			$small = $mod;
			$mod = $big%$small ;
		}
		return $small;
	}

	public static function battle2(UnitBase $ua,UnitBase $ub){
		$aSpeed = intval($ua['attack speed'] * 100) ;
		$bSpeed = intval($ub['attack speed'] * 100) ;

		$step = RPGCommon::getGCD($aSpeed, $bSpeed);

		$timeLine = 0;

		while($ua->alive() && $ub->alive()){
			$timeLine += $step;
			if($timeLine % $aSpeed == 0){
				$damage = static::attack($ua, $ub);
			}
			if(! ($ua->alive() && $ub->alive() ) ){
				break;
			}
			if($timeLine % $bSpeed == 0){
				$damage = static::attack($ub, $ua);
			}
		}
		exit;

	}



	/**
	 * 
	 * @param unknown $time
	 * 	执行次数
	 * @param callable $call
	 * 	回调
	 * @param unknown $param
	 * 	参数
	 * @param string $each
	 * 	是否指定每次的参数
	 * @throws \Exception
	 * @return multitype:number
	 */
	public static function multiple_call($time,callable $call,$param,$each = false){
		$result = [];
		if($each){
			if(count($param) != $time){
				throw new \Exception('Params Number Do Not Match!');
			}
		}
		for($i = 0 ; $i < $time ;$i ++ ){
			$rt = call_user_func_array($call, (array)($each ? $param[$i] : $param));
			$rt = is_array($rt) ? json_encode($rt) : $rt;
// 			if(!is_string($rt)){
// 				throw new \Exception('Params Number Do Not Match!');
// 			}
			$rt = 'H '.$rt;
			if(isset($result[$rt])){
				$result[ $rt ] ++;
			}else{
				$result[ $rt ] = 1;
			}
		}
		ksort($result);
		$tlen = strlen($time.'');
		foreach ($result as $k => $v){
			$left = str_pad($v, $tlen,' ',STR_PAD_LEFT);
			$right = bcdiv($v * 100, $time,4).'%';
			$right = str_pad($right, 8,' ',STR_PAD_LEFT);
			$result[$k] =  $left.' '.$right;
		}
		return $result;
	}

	/**
	 * Determine whether you hit the rate or not
	 * @param float $rate
	 * rate (0-100)
	 * @return boolean
	 */
	public static function hitRandom($rate){
		if($rate >= 100) return true;
		$max = $rate * 1000000;
		if(mt_rand(1,100000000) <= $max){
			return true;
		}
		return false;
	}

	/**
	 * Calculate hit rate
	 * @param float $miss
	 * 	miss rate
	 * @param float $dodge
	 *  dodge rate
	 * @return <b>number</b> float from 0 to 100
	 */
	public static function hitRate($miss,$dodge){
			
		return 100 - ($miss * $dodge / 100 + $dodge ) ;//100 - 100 * $miss * $dodge /10000.0 ;
	}


	public static function critHit($critRate){
		$multiple = 1;
		if(static::hitRandom($critRate)){
			$multiple = 2;
// 			$critRate = $critRate * 1.25;
		}
		if(static::hitRandom($critRate * $critRate /100)){
			$multiple = 3;
		}
		if(static::hitRandom($critRate * $critRate * $critRate /10000)){
			$multiple = 4;
		}
		if(static::hitRandom($critRate * $critRate * $critRate * $critRate /1000000)){
			$multiple = 5;
		}
		return $multiple;
	}

	/**
	 * Calculate attack damage
	 * @param UnitBase $attacker
	 * @param UnitBase $defender
	 * @return number
	 */
	public static function attack(UnitBase $attacker ,UnitBase $defender){
		$def 	 = $defender['defence'];
		$atk 	 = $attacker['attack'];
		$miss 	 = 100 - $attacker['hit rate'];
		$dodge 	 = $defender['dodge rate'];
		$hitRate = static::hitRate($miss,$dodge);
		$damage  = 0;

		$msg 	 = [];
		$msg ['title']  = $attacker->getName().' attack '.$defender->getName();
		if(static::hitRandom($hitRate) > 0 ){
			$multiple = 1;
			$msg ['hit']  =  'Hit';
			$multiple = static ::critHit($attacker['crit rate']);
			$multiple > 1 && $msg ['hit'] = 'Crit Hit '.($multiple - 1);
			$damage = $multiple * $atk * (1 - ($def * 0.06 ) / ($def * 0.06 + 1 )  ) ;
			$damage = bcmul ( $damage, 1, 4);
		}else{
			$msg ['hit']  =  'Miss';
		}
		$msg ['damage']  = $damage;
		echo "{$msg['title']} , {$msg['hit']} ". ($msg ['damage'] ? ' '.$msg ['damage'] : '').PHP_EOL;
		//Miss
		//Crit
		$damage > 0 && $defender->injured($damage);
		return $damage;
	}
}