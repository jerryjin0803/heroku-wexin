<?php

/*
	---------  POST 发送 string  参数，服务器获取情况  ------------
	var_dump($_SERVER);								//获取到 服务器方面的参数
	var_dump(@file_get_contents('php://input'));	//获取到 所传参数 string 形式
	var_dump($_POST);								//获取到 所传参数 array() 形式
	var_dump($GLOBALS["HTTP_RAW_POST_DATA"]);		//获取到 NULL

	---------  POST 发送 array() 参数，服务器获取情况  ------------
	var_dump($_SERVER);								//获取到 服务器方面的参数
	var_dump(@file_get_contents('php://input'));	//获取到 空 string  
	var_dump($_POST);								//获取到 所传参数 array() 形式
	var_dump($GLOBALS["HTTP_RAW_POST_DATA"]);		//获取到 NULL

	//--------------POST 参数字符串写法----------------
	//$postData = 'api_key=' . $this->api_key . '&api_secret=' . $this->api_secret . '&image_url='. $image . '&alegality=' . $legality;
	//--------------POST 参数数组写法 一  ----------------
	$postData = array(
		'api_key' => $this->api_key,
		'api_secret' => $this->api_secret,
		'image_url' => $image,
		'legality' => $legality
	);
	//--------------POST 参数数组写法 二 ----------------
	// $postData = array();
	// $postData['api_key'] = $this->api_key;
	// $postData['api_secret'] = $this->api_secret;
	// $postData['image_url'] = $image;
	// $postData['legality'] = $legality;

 */

class cURL {

	function get($url = '') {
		//检测url必须参数 不能为空
        if (!$url) {
            throw new Exception("错误：curl请求地址不能为空");
        }

	    //初始化
	    $curl = curl_init();
	    //设置抓取的url
	    curl_setopt($curl, CURLOPT_URL, $url);
	    //设置头文件的信息作为数据流输出
	    curl_setopt($curl, CURLOPT_HEADER, 1);
	    //设置获取的信息以文件流的形式返回，而不是直接输出。
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    //FALSE 禁止 cURL 验证对等证书（peer's certificate）。要验证的交换证书可以在 CURLOPT_CAINFO 选项中设置，或在 CURLOPT_CAPATH中设置证书目录。
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    //禁止头文件的信息作为数据流输出
	    curl_setopt($curl, CURLOPT_HEADER, false);

	    //执行命令
	    $data = curl_exec($curl);
	    //关闭URL请求
	    curl_close($curl);
	    //返回获得的数据
	    return $data;
	}

	function post($url = '', $post_data) {
		//检测url必须参数 不能为空
        if (!$url) {
            throw new Exception("错误：curl请求地址不能为空");
        }

	    //初始化
	    $curl = curl_init();
	    //设置抓取的url
	    curl_setopt($curl, CURLOPT_URL, $url);
	    //设置头文件的信息作为数据流输出
	    //curl_setopt($curl, CURLOPT_HEADER, true);
	    //禁止头文件的信息作为数据流输出
	    curl_setopt($curl, CURLOPT_HEADER, false);	
	    //设置获取的信息以文件流的形式返回，而不是直接输出。
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    //FALSE 禁止 cURL 验证对等证书（peer's certificate）。要验证的交换证书可以在 CURLOPT_CAINFO 选项中设置，或在 CURLOPT_CAPATH中设置证书目录。
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    //设置post方式提交
	    //curl_setopt($curl, CURLOPT_POST, 1);
	    //设置post方式提交
		curl_setopt($curl, CURLOPT_POST, count($post_data));	//值为true 则使用 post 方式请求
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);	//post 提交的数据

	    //执行命令
	    $data = curl_exec($curl);
	    //关闭URL请求
	    curl_close($curl);
	    //返回获得的数据
	    return $data;
	}
}

// //----------------    test post   --------------------

// $url = "http://api.pullword.com/post.php";
// //$url = "http://jerryjin.5gbfree.com/test/post.php";
// $postData = "source=与构造函数相反，当对象结束其生命周期时&param1=0&param2=0";

// // $postData = array(
// // 	'source' => '与构造函数相反，当对象结束其生命周期时',
// // 	'param1' => '0',
// // 	'param2' => '0'
// // );

// $curl = new cURL();	//实例化一个 cURL
// $output =  $curl->post($url, $postData);	//发送 POST 请求
// echo $output ;

