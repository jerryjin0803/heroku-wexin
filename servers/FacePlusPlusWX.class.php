<?php

/*###########################################################################
* @作者  :  JerryJin
* @签名  :  大家好，我是笨笨，笨笨的笨，笨笨的笨，谢谢！
* @日期  :  2017-02-09 14:35:34
* @授权  :  表客气，当自己家 ^_^
* @说明  :  Face++ 图像识别服务的结果，处理成微信要的格式。
###########################################################################*/

include_once '../class/FacePlusPlusAPI.class.php';
include_once '../lib/ServerMsg.class.php';

class FacePlusPlusWX {
	private $faceppApi;

	function __construct() {
		//创建 facc++ 对象
		$this->faceppApi = new FacePlusPlusAPI();
	}

	//人脸识别
	function faceDetectWX($images)
	{
		//是否检测人脸判断年龄，性别，微笑、人脸质量等属性
		//$faceAttr = "gender,age,smiling,glass,headpose,facequality,blur";
		//上传图片识别返回结果
		$imageInfo = $this->faceppApi->faceDetect($images);
		//取出面部信息
		$faceInfo = $imageInfo['faces'][0]['attributes'];


		//如果出错，直接返回错误信息
		if (isset($imageInfo['error_message'])){
			return '我靠。。。操作失败：' . PHP_EOL . $imageInfo['error_message'];
		}
		if (!count($imageInfo['faces'])) {
			return '你确定你拍的是张“脸” [抠鼻]？可能离太远或太近了，给个好点的角度嘛[撇嘴]';
		}

		// $attributes= Array(
		// 	'headpose' = Array(
		// 		'pitch_angle' => '抬头',
		// 		'roll_angle' => '歪脖子',
		// 		'yaw_angle' => '摇头'
		// 	)
		// )

		//检测准确度
		$facequalityThreshold = $faceInfo['facequality']['threshold'] < $faceInfo['facequality']['value'];
		$facequality = Array('我很很定！这应该','我觉的...大概可能也许这')[$facequalityThreshold];

		$age = $faceInfo['age']['value'];
		//严肃、微笑、大笑、狂笑 值
		$smileThreshold = (integer)($faceInfo['smile']['value'] / 24);
		$smile = Array('表情严肃', '面带微笑', '心情愉快', '开怀大笑')[$smileThreshold];
		$gender = Array('Male' => '帅哥', 'Female' => '美女')[$faceInfo['gender']['value']];

		$glass = Array('None' => '木有戴眼镜', 'Normal' => '戴着一副眼镜', 'Dark' => '戴着墨镜')[$faceInfo['glass']['value']];

		$gaussianblur = (integer)(100 - $faceInfo['blur']['gaussianblur']['value']);
		$motionblur = $faceInfo['blur']['motionblur']['value'];
		echo $motionblur;
		//拼接结果文本内容
		$responseInfo = <<<responseInfo
%s是个%s岁左右，%s的【%s】，%s。
图片清晰度可以打: %s分。
摄影师帕金森指数：%s
responseInfo;
//头%s，%s眼镜，图像%s，应该%
		$contentText = sprintf($responseInfo, $facequality, $age, $smile, $gender, $glass, $gaussianblur, $motionblur);

		//创建回复用的信息（主程序中回复函数的参数）
		$content = array(
			"title" => 'WARNING！！！发现'.$gender . "靓照", 
			"description" => $contentText, 
			"picurl" => $images, 
			"url" => $images
		);

		//返回,回复消息内容
		return $content;
	}

	//场景与对象
	function detectSceneAndObjectWX($images)
	{
		//上传图片识别返回结果
		$imageInfo = $this->faceppApi->detectSceneAndObject($images);
		$objects = $imageInfo['objects'][0]['value'];
		$scenes = $imageInfo['scenes'][0]['value'];
		$contentText = "这可能是 $objects ，在 $scenes。"; 

		//创建回复用的信息（主程序中回复函数的参数）
		$content = array(
			"title" => "鉴定结果", 
			"description" => $contentText, 
			"picurl" => $images, 
			"url" => $images
		);

		//返回,回复消息内容
		return $content;
	}

	//驾照识别
	function ocrDriverLicenseWX($images)
	{
		//上传图片识别返回结果
		$imageInfo = $this->faceppApi->ocrDriverLicense($images);
		$imageInfo = $imageInfo['cards'][0];

		// print_r($imageInfo);
		// return '测试直接跳出';
		$version = array('1'=>'2013版本驾驶证','2'=>'2008或更早版本驾驶证')[$imageInfo['version']];
		$contentText = "[驾驶证号]  :  {$imageInfo['license_number']}
[姓名]  :  {$imageInfo['name']}
[性别]  :  {$imageInfo['gender']}
[国籍]  :  {$imageInfo['nationality']}
[住址]  :  {$imageInfo['address']}
[生日]  :  {$imageInfo['birthday']}
[驾照版本]  :  {$version}
[初次领证]  :  {$imageInfo['issue_date']}
[准驾车型]  :  {$imageInfo['class']}
[有效日期]  :  {$imageInfo['valid_from']}
[有效年限]  :  {$imageInfo['valid_for']}
[签发机关]  :  {$imageInfo['issued_by']}";

		//创建回复用的信息（主程序中回复函数的参数）
		$content = array(
			"title" => "中华人民共和国机动车驾驶证", 
			"description" => $contentText, 
			"picurl" => $images, 
			"url" => $images
		);
/*		print_r($content);
		return '测试直接跳出';*/
		//返回,回复消息内容
		return $content;
	}

	//机动车行驶证
	function ocrVehicleLicenseWX($images)
	{
		//上传图片识别返回结果
		$imageInfo = $this->faceppApi->ocrVehicleLicense($images);
		$imageInfo = $imageInfo['cards'][0];

		// print_r($imageInfo);
		// return '测试直接跳出';
		$contentText = "[号牌号码]  :  {$imageInfo['plate_no']}
[车辆类型]  :  {$imageInfo['vehicle_type']}
[所有人]  :  {$imageInfo['owner']}
[住址]  :  {$imageInfo['address']}
[使用性质]  :  {$imageInfo['use_character']}
[品牌型号]  :  {$imageInfo['model']}
[车辆识别代号]  :  {$imageInfo['vin']}
[发动机号码]  :  {$imageInfo['engine_no']}
[注册日期]  :  {$imageInfo['register_date']}
[发证日期]  :  {$imageInfo['issue_date']}
[签发机关]  :  {$imageInfo['issued_by']}";

		//创建回复用的信息（主程序中回复函数的参数）
		$content = array(
			"title" => "中华人民共和国机动车行驶证", 
			"description" => $contentText, 
			"picurl" => $images, 
			"url" => $images
		);
/*		print_r($content);
		return '测试直接跳出';*/
		//返回,回复消息内容
		return $content;
	}

	public function ocrIdCardWX($images)
	{
		//上传图片识别返回结果
		$imageInfo = $this->faceppApi->ocrIdCard($images);
		$imageInfo = $imageInfo['cards'][0];

		$side = array('front'=>'正面','back'=>'反而')[$imageInfo['side']];

		if ($side == '正面') {
			$contentText = "[姓名]  :  {$imageInfo['name']} 
[性别]  :  {$imageInfo['gender']}
[民族]  :  {$imageInfo['race']}
[生日]  :  {$imageInfo['birthday']}
[住址]  :  {$imageInfo['address']}
[身份证号]  :  {$imageInfo['id_card_number']}";
		}else{
			$contentText = "[签发机关]  :  {$imageInfo['issued_by']}
[有效日期]  :  {$imageInfo['valid_date']} ";
		}

//可信度参数
// [legality] => Array
//     (
//         [Edited] => 0.001
//         [Photocopy] => 0
//         [ID Photo] => 0.979
//         [Screen] => 0.02
//         [Temporary ID Photo] => 0
//     )

		//创建回复用的信息（主程序中回复函数的参数）
		$content = array(
			"title" => "中华人民共和国第二代身份证 ". $side , 
			"description" => $contentText, 
			"picurl" => $images, 
			"url" => $images
		);
/*		print_r($content);
		return '测试直接跳出';*/
		//返回,回复消息内容
		return $content;
	}

}
// //----------------    test post   --------------------

// $fppi = new FacePlusPlusWX();
// //  $url = "http://news.xinhuanet.com/photo/2013-07/25/11118125064696_1981d.jpg";
// // // $url = "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1486721316459&di=cb5a7de80a8d7c0a73a8fdd28680ac59&imgtype=0&src=http%3A%2F%2Fimg.taopic.com%2Fuploads%2Fallimg%2F140316%2F318743-1403160PU577.jpg";


// // 人脸识别
// // $output =  $fppi->faceDetectWX($url); 
// // print_r($output);

// //场景对象
// // $output =  $fppi->detectSceneAndObjectWX($url); 
// // 
// // 驾照识别
// $url = "https://ss0.baidu.com/-Po3dSag_xI4khGko9WTAnF6hhy/zhidao/pic/item/f9dcd100baa1cd11a167b767bb12c8fcc2ce2da0.jpg";
// $output =  $fppi->ocrDriverLicenseWX($url); 

//机动车行驶证
// $url = "http://imgsrc.baidu.com/zhangbai/pic/item/b8389b504fc2d56214291076e71190ef76c66c0c.jpg";
// $output = $fppi->ocrVehicleLicenseWX($url);

// //身份证识别
// // $url = "http://www.qq-ex.com/user/uploads/125377/addressphoto/11010419871229301X2.jpg";
// // $url = "http://img.ptfish.com/attachment/forum/201304/26/134127y2y6ilgxaxe746gq.jpg";
// // $output = $fppi->ocrIdCardWX($url);

// print_r($output);
// // 发消息部分。可以公用
// $url = "http://mmbiz.qpic.cn/mmbiz_jpg/6MdIErTYeGibqzsmDiaS3Od1CjVMGuX9yYOXiaEzGWKJcK3s88dtcW2kxGR4lYv8TvpEjdBI44n1Nw4vD5VHDAWicA/0";
// $output['picurl'] = $url;
// $output['url'] = $url;

// $openId = "oCm6Zw0CCqvl4F6Qpuso0mLBouh0";
// $serverMsg = new ServerMsg();
// $serverMsg->send($openId, $output,'news');