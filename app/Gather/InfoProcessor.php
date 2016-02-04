<?php

/**
 * 通用类
 *
 */
namespace App\Gather;


class InfoProcessor extends IProcessor implements InfoProcessorConstInter{
	
	/**
	 * 执行内部黑名单检测
	 * @param unknown $phone
	 * @param unknown $identity
	 */
	protected function doInternalBlacklist($phone,$identity){
		
		$lendService = new \Ser\Lend\LendService();
		$is_local_black = $lendService->check_local_blacklist($phone, $identity);
		$local_msg = '通过';
		if($is_local_black){
			$local_msg = '不通过';
		}
		$data = [
				'decision'		=> $is_local_black ? 0 : 1 ,
				'status'      	=> $is_local_black,
				'message'     	=> $local_msg,
		];
		return $data;
	}
	
	
	/**
	 * 执行同盾黑名单检测
	 * @param unknown $name
	 * @param unknown $phone
	 * @param unknown $identity
	 */
	protected function doFraudmetrix($name,$phone,$identity){
		// Add Test blacklist For test
		$testData = [];
		if(\App::environment('developemnt')){
			// Make effect at development enviroment
			$testData = [
					[
							'account_name' => '陈夏',
							'account_mobile' => '18767109019',
							'id_number' => '330327199201066107',
							'final_decision' => 'Reject',
					]
			];
		}
		$fraudmetrix = [];
		foreach ($testData as $k => $v){
			if($v ['account_name'] == $name &&
					$v ['account_mobile'] == $phone  &&
					 $v ['id_number'] == $identity ){
				$fraudmetrix['final_decision'] = $v['final_decision'];
			}
		}
		
		if(empty($fraudmetrix)){
			// 验证录入信息
			$array = array (
					'account_name' 		=> $name,
					'account_mobile' 	=> $phone,
					'id_number' 		=> $identity
			);
			$fraudmetrix = \Lib\Fun\Fraudmetrix::doit ( $array );
		}
		if (isset ( $fraudmetrix ['final_decision'] )) {
			switch ($fraudmetrix ['final_decision']) {
				case 'Reject' : // 拒绝
					$credit_msg = '黑名单';
					$credit_status = 3;
					break;
				case 'Review' : // 可疑账户
					$credit_msg = '可疑账户';
					$credit_status = 4;
					break;
				case 'Accept' : // 通过
					$credit_msg = '通过';
					$credit_status = 0;
					break;
				default :
					$credit_msg = '通过';
					$credit_status = 0;
			}
		}else {
			$credit_msg = '同盾接口错误';
			$credit_status = -1;
		}
		$data = [
				'decision'			=> $credit_status == 3 ? 0 : 1,
				'status'            => $credit_status,
				'message'           => $fraudmetrix ['final_decision'] ,
		];
		return $data;
	}
	
	
	/**
	 * 执行三、四要素检测
	 * @param unknown $name
	 * @param unknown $identity
	 * @param unknown $card
	 * @param string $phone
	 */
	protected function doFactorsCheck($name,$identity,$card,$phone = ''){
		$UserService = new \Ser\User\UserService();
		$result =  $UserService->common_Validate($card, $name, $identity,$phone);
		$data =[
			'decision'			=> $result['return'] === true ? 1 : 0,
			'status'            => $result['return'] === true ? 1 : 0,
			'message'        	=> isset($result['data']) ? $result['data'] : '',
		];
		return $data;
	}
}