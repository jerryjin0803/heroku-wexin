<?php
/*###########################################################################
* @作者  :  JerryJin 
* @签名  :  大家好，我是笨笨，笨笨的笨，笨笨的笨，谢谢！
* @日期  :  2017-02-09 13:19:59
* @授权  :  捡个肥皂
* @说明  :  Face++ 的图像识别服务  API官网 https://www.faceplusplus.com
###########################################################################*/

//include '../lib/media.lib.php';
include_once '../lib/cURL.class.php';

class FacePlusPlusAPI {

	private $api_key = "QsJDwluGz4ppNK-C4qxOZ0e7EcxTnDOC";
	private $api_secret = "8QtSCU8cfwg59hzMdn8NJ7TLwscjHbIV";
	private $apiUrl = array(
			'人脸识别' => "https://api-cn.faceplusplus.com/facepp/v3/detect",
			'图像识别' => "https://api-cn.faceplusplus.com/imagepp/beta/detectsceneandobject",
			'文字识别' => "https://api-cn.faceplusplus.com/imagepp/beta/recognizetext",
			'二代身份证识别' => "https://api-cn.faceplusplus.com/cardpp/v1/ocridcard",
			'机动车驾驶证识别' => "https://api-cn.faceplusplus.com/cardpp/v1/ocrdriverlicense",
			'机动车行驶证识别' => "https://api-cn.faceplusplus.com/cardpp/v1/ocrvehiclelicense"
			);
	//======================== 方法定义 ==========================
	/**
	 * 向服务器发送API请求
	 * @param  string 		$url      	API地址
	 * @param  array()		$postData 	Post请求的参数
	 * @return array() 					讲求结果
	 */
 	private function faceppPost($url, $postData)
 	{
 		if (!$postData['image_url']) {
            throw new Exception("图呢？图呢？你不给我图片，我怎么识别啊？");
        }

 		$ch = new cURL();	//实例化一个 cURL
		$output = $ch->post($url, $postData);	//发送 POST 请求,返回结果
		//把 JSON 编码的字符串转换为 PHP 数组
		$output = (array) json_decode($output, true);
		return  $output;
 	}

	/**
	 * 人脸识别。可以分析 gender,age,smiling,glass,headpose,facequality,blur 这些
	 * @param  string  $image             要识别的图像URL
	 * @param  string  $face_attr 是否检测人脸判断年龄，性别，微笑、人脸质量等属性
	 * @param  integer $return_landmark   是否检测并返回人脸五官和轮廓的83个关键点。
	 * @return array()         			  返回识别出来的信息。
	 */
	function faceDetect($image, $face_attr = 'none', $landmark = 0 )
	{
		$url = $this->apiUrl['人脸识别'];	//取出 API 的地址
		//没有传参，默认全搜
		$face_attr == 'none' && $face_attr = "gender,age,smiling,glass,headpose,facequality,blur";

		// if (gettype($image)  === gettype("http://")) {
		// 	echo '传入的是URL';
		// }else{
		// 	echo '传入的是文件';
		// }

		$postData = array(
			'api_key' => $this->api_key,
			'api_secret' => $this->api_secret,
			'image_url' => $image,
			//'image_url' => gettype($image)  === gettype("http://") ? $image : '',
			//'image_file' => gettype($image)  !== gettype("http://") ? '' : $image,
			'landmark' => $landmark,
			'return_attributes' => $face_attr
		);
		return  $this->faceppPost($url, $postData);
	}

	/**
	 * 调用者提供图片文件或者图片URL，进行图片分析，识别图片场景和图片主体。
	 * @param  string $image 要识别的图像URL
	 * @return array()       返回识别出来的信息。
	 */
	function detectSceneAndObject($image)
	{
		$url = $this->apiUrl['图像识别'];	//取出 API 的地址
		$postData = array(
			'api_key' => $this->api_key,
			'api_secret' => $this->api_secret,
			'image_url' => $image
		);
		return  $this->faceppPost($url, $postData);
	}

	/**
	 * 调用者提供图片文件或者图片URL，进行图片分析，找出图片中出现的文字信息。
	 * @param  string $image 要识别的图像URL
	 * @return array()       返回识别出来的信息。
	 */
	function recognizeText($image)
	{
		$url = $this->apiUrl['文字识别'];	//取出 API 的地址
		$postData = array(
			'api_key' => $this->api_key,
			'api_secret' => $this->api_secret,
			'image_url' => $image
		);
		return  $this->faceppPost($url, $postData);
	}

	/**
	 * 检测和识别中华人民共和国第二代身份证。。传入一张身份证图片，返回识别出的信息。
	 * @param  string  $image    要识别的图像URL
	 * @param  integer $legality 返回身份证照片合法性检查结果。“1”：返，“0”：不返。
	 * @return array()           返回识别出来的信息。
	 */
	public function ocrIdCard($image, $legality = 1)
	{
		$url = $this->apiUrl['二代身份证识别'];	//取出 API 的地址
		$postData = array(
			'api_key' => $this->api_key,
			'api_secret' => $this->api_secret,
			'image_url' => $image,
			'legality' => $legality
		);
		return  $this->faceppPost($url, $postData);
	}

	/**
	 * 检测和识别中华人民共和国机动车驾驶证（以下称“驾照”）图像为结构化的文字信息。目前只支持驾照主页正面，不支持副页正面反面。
	 *  驾照图像须为正拍（垂直角度拍摄），但是允许有一定程度的旋转角度；
	 *  仅支持图像里有一个驾照的主页正面，如果同时出现多页、或正副页同时出现，可能会返回空结果。 
	 * @param  string  $image    要识别的图像URL
	 * @return array()           返回识别出来的信息。
	 */
	public function ocrDriverLicense($image)
	{
		$url = $this->apiUrl['机动车驾驶证识别'];	//取出 API 的地址
		$postData = array(
			'api_key' => $this->api_key,
			'api_secret' => $this->api_secret,
			'image_url' => $image
		);
		return  $this->faceppPost($url, $postData);
	}

	/**
	 * 检测和识别中华人民共和国机动车行驶证（以下称“行驶证”）图像为结构化的文字信息。目前只支持行驶证主页正面，不支持副页正面反面。
	 *  行驶证图像须为正拍（垂直角度拍摄），但是允许有一定程度的旋转角度；
	 *  仅支持图像里有一个行驶证的主页正面，如果同时出现多页、或正副页同时出现，可能会返回空结果。
	 * @param  string $image 	要识别的图像URL
	 * @return array()       	返回识别出来的信息。
	 */
	public function ocrVehicleLicense($image)
	{
		$url = $this->apiUrl['机动车行驶证识别'];	//取出 API 的地址
		$postData = array(
			'api_key' => $this->api_key,
			'api_secret' => $this->api_secret,
			'image_url' => $image
		);
		return  $this->faceppPost($url, $postData);
	}
}
//==================================================================
 
// // // // //-------------- test code ------------------------
// $fppa = new FacePlusPlusAPI();

// // //人脸识别
// $url = "https://api-cn.faceplusplus.com/facepp/v3/detect?"; 
// // $url = "http://jerryjin.5gbfree.com/test/post.php?"; 
// // $url = "http://localhost:8080/test/post.php?"; 
// // $imges = "http://news.xinhuanet.com/photo/2013-07/25/11118125064696_1981d.jpg";
// $imges = "http://mmbiz.qpic.cn/mmbiz_jpg/6MdIErTYeGibqzsmDiaS3Od1CjVMGuX9yYA1JEx6u20ic2hQxfsRY4AKDxOL1RaZQkNWricc32bMr92WSM0icjlnDcA/0";
// print_r($fppa->faceDetect($imges, "gender,age,smiling,glass,headpose,facequality,blur")) ;

// // $mediaId = "TUB0RI1eVGSlEpEm_tvmLkfLL7WpbS55VzKqqjnLMsoqErzdMHG9lRozzV-WtM6v";
// // $imges = Media::download($mediaId);
// $api_key = "QsJDwluGz4ppNK-C4qxOZ0e7EcxTnDOC";
// $api_secret = "8QtSCU8cfwg59hzMdn8NJ7TLwscjHbIV";
// $face_attr = "gender,age,smiling,glass,headpose,facequality,blur";

// // $url .= 'api_key='.$api_key;
// // $url .= '&api_secret='.$api_secret;
// // $url .= '&return_attributes='.$face_attr;
// // $imges = realpath('../lib/imgs/a.jpg');

// // echo $url.PHP_EOL;
// // echo $imges.PHP_EOL;

// $postData = array(
// 	'api_key' => $api_key,
// 	'api_secret' => $api_secret,
// 	// 'image_url' => $imges,
// 	'image_file' => '@a.jpg',
// 	'landmark' => 0,
// 	'return_attributes' => $face_attr
// );

// $ch = curl_init();  
// curl_setopt($ch, CURLOPT_URL, $url);  
// curl_setopt($ch, CURLOPT_HEADER, 0); 
// curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: multipart/form-data"));
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
// curl_setopt($ch, CURLOPT_POST, 1); //POST提交  
// curl_setopt($ch, CURLOPT_POSTFIELDS,$postData);  

// $output =curl_exec($ch);  
// curl_close($ch); 


// //$output = (array) json_decode($output, true);
// // $output = explode(PHP_EOL, $output);
// // echo $output[count($output)-1];
// //echo $output;
// print_r($output);

//图像识别
// $url = "http://s10.sinaimg.cn/mw690/002F9Siagy6TG6iYk4hd9&690";
// // $url = "http://imgsrc.baidu.com/zhangbai/pic/item/b8389b504fc2d56214291076e71190ef76c66c0c.jpg";
// print_r($fppa->detectSceneAndObject($url));

// //文字识别
// $url = "http://cdn.duitang.com/uploads/blog/201403/12/20140312223147_P3Zei.thumb.600_0.jpeg";
// print_r($fppa->recognizeText($url));

//身份证识别
// $url = "http://www.qq-ex.com/user/uploads/125377/addressphoto/11010419871229301X2.jpg";
// echo $fppa->ocrIdCard($url);
// print_r($fppa->ocrIdCard($url)) ;

// //机动车驾驶证
// $url = "https://ss0.baidu.com/-Po3dSag_xI4khGko9WTAnF6hhy/zhidao/pic/item/f9dcd100baa1cd11a167b767bb12c8fcc2ce2da0.jpg";
// print_r($fppa->ocrDriverLicense($url));

// //机动车行驶证
// $url = "http://imgsrc.baidu.com/zhangbai/pic/item/b8389b504fc2d56214291076e71190ef76c66c0c.jpg";
// print_r($fppa->ocrVehicleLicense($url));