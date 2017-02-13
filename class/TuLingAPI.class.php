<?php
/*###########################################################################
* @作者  :  JerryJin 
* @签名  :  大家好，我是笨笨，笨笨的笨，笨笨的笨，谢谢！
* @日期  :  2017-02-09 13:19:59
* @授权  :  捡个肥皂
* @说明  :  图灵API 聊天服务  API官网 http://www.tuling123.com/help/h_cent_webapi.jhtml
###########################################################################*/

include_once '../lib/cURL.class.php';

class TuLingAPI {

	private static $api_key = "49f38bd66ccc496cbcf343a0242b2b3f";
	//private $api_secret = "";
	private static $apiUrl = array(
			'图灵API' => "http://www.tuling123.com/openapi/api"
			);
	//======================== 方法定义 ==========================
	/**
	 * 向服务器发送API请求
	 * @param  string 		$url      	API地址
	 * @param  array()		$postData 	Post请求的参数
	 * @return array() 					讲求结果
	 */
 	public static function talk($info)
 	{
 		if (!$info) {
            throw new Exception("不给消息，我发什么呢？");
        }

        $url = self::$apiUrl['图灵API'];
		$postData = array(
			 'key' => self::$api_key,
			 'info' => $info
			 );

 		$ch = new cURL();	//实例化一个 cURL
		$output = $ch->post($url, $postData);	//发送 POST 请求,返回结果
		//把 JSON 编码的字符串转换为 PHP 数组
		$output = (array) json_decode($output, true);
		return  $output;
 	}

}
//==================================================================
 
// // // // // //-------------- test code ------------------------
// $tuLingApi = new TuLingAPI();
// $output =  $tuLingApi->talk('你叫什么名字?');
// // echo $output['text'];
// // print_r($output);
