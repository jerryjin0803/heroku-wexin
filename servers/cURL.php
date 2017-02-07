<?php
//pullword在线分词服务  http://api.pullword.com/
//特别注明：超过30个汉字的分词请求，清用get方法，不要用post方法，否则会效率低，且会出错。
// source=[a paragraph of chinese words] for example: source=清华大学是好学校
// param1=[threshold] for example: param1=0 to pull all word, param1=1 to pull word with probability with 100%.
// param2=[debug] for example: param2=0 debug model is off, 
//                             param2=1 debug mode in on(show all probabilities of each word)

//get例子：http://api.pullword.com/get.php?source=清华大学是好学校&param1=0&param2=0

class Chinese_word_segmentation {
	private  $apiPost = "http://api.pullword.com/post.php";
	private  $apiGet = "http://api.pullword.com/get.php";

	function __construct() {
		//print "--------------- 我构造了，怎么地吧？ -----------------".PHP_EOL;
	}

	function __destruct() {
		//print PHP_EOL."--------------- 么么达，我走了！--------------- ".PHP_EOL;
	}

	function post($word, $param1 = 0, $param2 = 0, $url = null) 
	{ 
		$url || $url = $this->apiPost;
		$postData = "source=$word&param1=$param1&param2=$param2";

		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_URL,$url);	//设置请求的url
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);	//设置获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($curl, CURLOPT_HEADER, false);	//禁止头文件的信息作为数据流输出
		curl_setopt($curl, CURLOPT_POST, count($postData));	//值为true 则使用 post 方式请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);	//post 提交的数据
		$output=curl_exec($curl);
		curl_close($curl); 
		return $output;
	}

	function get($word, $param1 = 0, $param2 = 0, $url = null) 
	{
		$url || $url = $this->apiGet;
		$url = "$url?source=$word&param1=$param1&param2=$param2";

		if (!function_exists('curl_init')) {
			echo "<br>不支持 curl，搞毛啊<br>";
			return "呵呵达！";
        }

		$curl = curl_init(); 
		curl_setopt($curl, CURLOPT_URL, $url);	//设置抓取的url
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);	//设置获取的信息以文件流的形式返回，而不是直接输出。
		curl_setopt($curl, CURLOPT_HEADER, false);	//禁止头文件的信息作为数据流输出
		$output=curl_exec($curl);
		curl_close($curl); 

		return $output;
	}
}

$cws = new Chinese_word_segmentation();

//echo $cws->get("与构造函数相反，当对象结束其生命周期时");
echo $cws->get($_GET["word"]);

//echo $cws->post("我调用别人的API实现了中文分词",0.5,0,"http://api.pullword.com/post.php");



?>