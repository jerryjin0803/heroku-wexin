<?php
/*
>>>>>>>>>>>>>>>>>>> 中文分词服务 <<<<<<<<<<<<<<<<<<
2017-02-08 22:39:03

//------------------------------------------------------------------
//pullword在线分词服务  http://api.pullword.com/
//特别注明：超过30个汉字的分词请求，清用get方法，不要用post方法，否则会效率低，且会出错。
// source=[a paragraph of chinese words] for example: source=清华大学是好学校
// param1=[threshold] for example: param1=0 to pull all word, param1=1 to pull word with probability with 100%.
// param2=[debug] for example: param2=0 debug model is off, 
//                             param2=1 debug mode in on(show all probabilities of each word)
//get例子：http://api.pullword.com/get.php?source=清华大学是好学校&param1=0&param2=0
//------------------------------------------------------------------
*/


include_once '../lib/cURL.class.php';

class ChineseWordSegmentationAPI {
	private  $apiPost = "http://api.pullword.com/post.php";
	private  $apiGet = "http://api.pullword.com/get.php";

	// function __construct() {
	// 	print "--------------- 我构造了，怎么地吧？ -----------------".PHP_EOL;
	// }

	// function __destruct() {
	// 	print PHP_EOL."--------------- 么么达，我走了！--------------- ".PHP_EOL;
	// }
	/**
	 * @param  string
	 * @param  [type]
	 * @param  integer
	 * @param  integer
	 * @return [type]
	 */
	function get($word, $url = null, $param1 = 0, $param2 = 0)
	{
		$url || $url = $this->apiGet;	//如果 $url 为空则赋予 默认值
		$url = "$url?source=$word&param1=$param1&param2=$param2";	//拼接 GET 参数

		$curl = new cURL();	//实例化一个 cURL
		$output = $curl->get($url);	//发送 GET 请求
		return $output;
	}

	function post($word, $url = null, $param1 = 0, $param2 = 0)
	{
		$url || $url = $this->apiPost;	//如果 $url 为空则赋予 默认值
		$postData = "source=$word&param1=$param1&param2=$param2";	//拼接 POST 参数

		$curl = new cURL();	//实例化一个 cURL
		$output =  $curl->post($url, $postData);	//发送 POST 请求
		return $output;
	}
}

// //-------------- test code ------------------------
// $cws = new ChineseWordSegmentationAPI();
// // echo $cws->get("与构造函数相反，当对象结束其生命周期时");
// echo $cws->post("与构造函数相反，当对象结束其生命周期时");


