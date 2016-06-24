<?php

/**
 * 通用业务控制器
 *
 * @author Yangshaohuai
 */
namespace Weixin;


use Lib\Fun\Fun;

class WeixinController extends \BaseController {
	
	public $weixinService = '';
	
	public function __construct() {
		parent::__construct ();
		$appId 		= \Config::get('weixin.appid');
		$appSecret 	= \Config::get('weixin.appsecret');
		$this->weixinService = new \Ser\Weixin\WeixinService($appId, $appSecret);
	}
	
	public function getJsTicket(){
		
		$url = \Input::get('url');
// 		Fun::isEmpty($url		, 'URL');

// 		'appid' => 'wxad6eccaa7c586632',
// 		'appsecret' => 'b56fc8785858e10164f9b3ebab7eccea',
		
		
		$result = $this->weixinService->getSignPackage($url);
		
		Fun::msg(200,'ok',$result);
		
	}
	
	
	public function test(){

		// 		Fun::isEmpty($url		, 'URL');
		//wx73dd25b02aaa3c92
		//05f72a07d873bf4c7245c82773b6e901 
		
		$appId 		= 'wx73dd25b02aaa3c92';
		$appSecret 	= '05f72a07d873bf4c7245c82773b6e901';
		$this->weixinService = new \Ser\Weixin\WeixinService($appId, $appSecret);
		
		$result = $this->weixinService->getSignPackage(null);
		
		return \View::make('weixin.sample')->with('signPackage',$result);
		
	}
	
}

















