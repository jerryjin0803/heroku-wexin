<?php

/*###########################################################################
* @作者  :  JerryJin
* @签名  :  大家好，我是笨笨，笨笨的笨，笨笨的笨，谢谢！
* @日期  :  2017-02-09 14:35:34
* @授权  :  表客气，当自己家 ^_^
* @说明  :  Face++ 图像识别服务的结果，处理成微信要的格式。
###########################################################################*/

include_once '../class/FacePlusPlusAPI.class.php';

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
			return '你确定你拍的是张“脸” ？？？';
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
		$content = array();
		$content[] = array(
			"Title" => $gender . "靓照", 
			"Description" => $contentText, 
			"PicUrl" => $images, 
			"Url" => $images
		);

		//返回,回复消息内容
		return $content;
	}
}


// //----------------    test post   --------------------

// $fppi = new FacePlusPlusWX();
// // // $url = "http://news.xinhuanet.com/photo/2013-07/25/11118125064696_1981d.jpg";
// // $url = "https://timgsa.baidu.com/timg?image&quality=80&size=b9999_10000&sec=1486721316459&di=cb5a7de80a8d7c0a73a8fdd28680ac59&imgtype=0&src=http%3A%2F%2Fimg.taopic.com%2Fuploads%2Fallimg%2F140316%2F318743-1403160PU577.jpg";
// $url = "http://mmbiz.qpic.cn/mmbiz_jpg/6MdIErTYeGibqzsmDiaS3OdxlHHB34GibQEyEiaWg44XImvm9nkQjicx0Cd3GKImiafjJmNfibN2hM2N38hdHqvLyfkog/0";
// $output =  $fppi->faceDetectWX($url); 



