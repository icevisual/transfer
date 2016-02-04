<?php

/**
 * Auto 
 */
namespace App\Gather;

interface InfoProcessorConstInter {

	/**
	 * 执行内部黑名单检测
	 * @param unknown $phone
	 * @param unknown $identity
	 */
	const  INTERNALBLACKLIST = 'InternalBlacklist';

	/**
	 * 执行同盾黑名单检测
	 * @param unknown $name
	 * @param unknown $phone
	 * @param unknown $identity
	 */
	const  FRAUDMETRIX = 'Fraudmetrix';

	/**
	 * 执行三、四要素检测
	 * @param unknown $name
	 * @param unknown $identity
	 * @param unknown $card
	 * @param string $phone
	 */
	const  FACTORSCHECK = 'FactorsCheck';


			
}
			
