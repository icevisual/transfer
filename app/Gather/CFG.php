<?php

namespace App\Gather;

/**
 * 从后台获取配置信息
 * @author Administrator
 *
 */
class CFG extends CFGAuto{
	
	/**
	 * 环境相关的配置交给\Config::get
	 * @var unknown
	 */
	private static $_config_example = [
			'time_interval' =>[//提交频度（s）
					'status' 	=> '1|0|1',
					'default' 	=> '10',
					'rules' 	=> [// routes
							'v2.0/user/create_userinfo' => 1,
							'v2.0/order/put_credit'    	=> 1,
					],
			],
			'ump_factor_validate' => [//三四要素验证配置，只在loacl、developemnt、stage有效
					'status' 	=> '0|1|0',         	//是否开启
					'always' 	=> 'deny|allow|deny',	//关闭的话 全通过，或者全拒绝
					'error' 	=> '[errors]',    	// 拒绝的错误提示
					'rules'		=> [
							'create_userinfo' 	=> '3|4',
					],
			],
			'auto_rejection_rules' => [//自动拒绝配置
					'status'	=> '1|0',
					'rules' 	=> [//services
							'open_area' 	=> '1|0',  //开放区域
							'black_list' 	=> '1|0', //黑名单
							'factor_error'	=> '1|0',//三要素、四要素不一致
							'over_due'		=> '0|1|0',//逾期超5天
					],
			],
			'single_log' => [// 单个uid日志，位置：storage_path()/logs/{uid}/ReqLogyyyy-mm-dd
					'status'	=> '1|0',
			]
	];
	
	/**
	 * 写入配置文件
	 * @var unknown
	 */
	private  static $_config = [];
	
	public static function getConfig(){
		empty(self::$_config ) && self::$_config = \Config::get('cfg');
		return self::$_config ;//$_config_example;//
	}
	
	
	/**
	 * 自动生成父类，内含CfgXX方法
	 * @return boolean
	 */
	public static function autoAdjustment(){
		$classname = 'CFGAuto';
		$str = 'abstract class '.$classname;
		$file_content = file_get_contents(__FILE__);
		if(strpos( $file_content,$str) !== false){
			$file_content = substr($file_content, 0,strpos( $file_content,$str));
		}
		$_config = self::getConfig();
		$content = '';
		foreach ($_config as $k =>$v){
			$key 	= str_replace('_', ' ', $k);
			$name 	= str_replace(' ', '', ucwords(strtolower($key)));
			$content .=<<<EOL

	public static function Cfg$name(\$key){
		return static::cfgGet('$k',\$key);
	}
EOL;
		}
		$abstract=<<<EOF

abstract class $classname{
	$content
}
		
EOF;
		file_put_contents(__FILE__, rtrim($file_content) .$abstract);
		return true;
	}
	
	
	/**
	 * @param unknown $type
	 * 	$_config 第一层的key
	 * @param unknown $key
	 * 	[$key] 代表 第二层的key
	 * 	$key 代表 $type.rules.$key
	 * @return Ambigous <unknown, boolean, multitype:multitype:string  multitype:string multitype:number   multitype:string multitype:string   >
	 */
	protected static function cfgGet($type, $key){
		if($key{0} == '['){
			return static::get([$type,substr($key, 1,strlen($key) -2 )]);
		}
		return static::get([$type,'rules',$key]);
	}

	
	public static function autoGenerateSql(){
		$_config = self::getConfig();
		$_config = self::$_config_example;
		$_keys = [];
// 		$_sqlCol = ['key','value','info'];
		foreach ($_config as $k => $v){
			if(is_array($v)){
				foreach ($v as $k1 => $v1){
					if(is_array($v1)){
						foreach ($v1 as $k2 => $v2){
							$_key = $k.'.'.$k1.'.'.$k2;
							$_keys[$_key] = $v2;
						}
					}else{
						$_key = $k.'.'.$k1;
						$_keys[$_key] = $v1;
					}
				}
			}else {
				$_key = $k;
				$_keys[$_key] = $v;
			}
		}
		
		foreach ($_keys as $k => $v){
			$_sqlCol ['key'] 	=  $k;
			$_sqlCol ['value'] 	=  $v;
			$_sqlCol ['info'] 	=  'info';
			$sql[] = createInsertSql('gzb_topic_config', $_sqlCol);
			line(createInsertSql('gzb_topic_config', $_sqlCol),';<br/>');
		}
		dump($sql);
		edump($_keys);
	
	}
	
	/**
	 * 获取数据库配置信息
	 */
	protected static function getDbDataSource(){
		static  $_result = [];
		if(empty($_result)){
			//explode('.',$str ,3)
		}
		return  $_result;
	}
	
	
	/**
	 * @param array $keys
	 *  $keys[0] 为第一层key
	 * @return Ambigous <boolean, unknown, multitype:multitype:string  multitype:string multitype:number   multitype:string multitype:string   >|Ambigous <unknown, boolean, multitype:multitype:string  multitype:string multitype:number   multitype:string multitype:string   >
	 */
	public static function get(array $keys){
		// TODO : Get Configration From DB
// 		$keys = explode($delimiter, $key);
		$keys = array_filter($keys);
		$result = self::getConfig();
		foreach ($keys as $k => $v){
			$result = isset($result[$v]) ?  $result[$v] : false;
			if($result === false){
				return $result;
			}
		}
		if(is_string($result)){
			$result = explode('|', $result);
			$result = $result[0];
		}
		
		return $result;
	}
	
	
	/**
	 * @param unknown $key
	 * @param unknown $value
	 */
	public static function set($key , $value){
		
	}
	
}
abstract class CFGAuto{
	
	public static function CfgTimeInterval($key){
		return static::cfgGet('time_interval',$key);
	}
	public static function CfgUmpFactorValidate($key){
		return static::cfgGet('ump_factor_validate',$key);
	}
	public static function CfgAutoRejectionRules($key){
		return static::cfgGet('auto_rejection_rules',$key);
	}
	public static function CfgSingleLog($key){
		return static::cfgGet('single_log',$key);
	}
}
		