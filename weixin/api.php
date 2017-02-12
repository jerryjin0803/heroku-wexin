<?php
include_once '../lib/FileManage.class.php';
include_once '../lib/media.lib.php';
include_once '../servers/FacePlusPlusWX.class.php';
include_once '../lib/cURL.class.php';
include_once '../lib/PlayersManage.class.php';
include_once '../lib/ServerMsg.class.php';


//设置下时区
date_default_timezone_set('Asia/Shanghai');

//声明一个常量定义一个token值, token
define("TOKEN", "weixin");

//通过Wechat类， 创建一个对象
$wechatObj = new Wechat();

//如果没有通过GET收到echostr字符串， 说明不是再使用token验证
if (!isset($_GET['echostr'])) {
	//调用wecat对象中的方法响应用户消息
		$wechatObj->responseMsg();
}else{
	//调用valid()方法，进行token验证

		$echoStr = $_GET["echostr"];

  		  //valid signature , option
   		 if($wechatObj->valid()){
    		echo $echoStr;
    		exit;
    	}
}


//声明一个Wechat的类， 处理接收消息， 接收事件， 响应各种消息， 以及token验证
class Wechat {
     
    //验证签名, 手册中原代码改写
	public function valid() {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
    	$token = TOKEN;
    	$tmpArr = array($token, $timestamp, $nonce);
       		 // use SORT_STRING rule
    	sort($tmpArr, SORT_STRING);
    	$tmpStr = implode( $tmpArr );
    	$tmpStr = sha1( $tmpStr );
    	
    	if( $tmpStr == $signature ){
    		return true;
    	}else{
    		return false;
    	}
	}

    //响应消息处理
    public function responseMsg()
    {
        //接收微新传过来的xml消息数据    
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //如果接收到了就处理并回复
        if (!empty($postStr)){
            //将接收到的XML字符串写入日志， 用R标记表示接收消息
            $this->logger("<WX Request>---------------- 接收事件推送 ---- ".date('Y-m-d H:i:s')." ---------<WX Request>".PHP_EOL.$postStr);
            //将接收的消息处理返回一个对象
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);

            //从消息对象中获取消息的类型 text  image location voice vodeo link 
            $RX_TYPE = trim($postObj->MsgType);
                 
            //消息类型分离, 通过RX_TYPE类型作为判断， 每个方法都需要将对象$postObj传入
            switch ($RX_TYPE)   
            {
             	case "event":
        			$result = $this->receiveEvent($postObj);     //事件消息
        			break;
                case "text":
                    $result = $this->receiveText($postObj);     //接收文本消息
                    break;
                case "image":
                    $result = $this->receiveImage($postObj);   //接收图片消息
                    break;
                case "location":
                    $result = $this->receiveLocation($postObj);  //接收位置消息
                    break;
                case "voice":
                    $result = $this->receiveVoice($postObj);   //接收语音消息 -----
                    break;
                case "video":
                    $result = $this->receiveVideo($postObj);  //接收视频消息
                    break;
                case "link":
                    $result = $this->receiveLink($postObj);  //接收链接消息
                    break;
                default:
                    $result = "unknown msg type: ".$RX_TYPE;   //未知的消息类型
                    break;
            }
            //输出消息给微信
            echo $result;

            //将响应的消息再次写入日志， 使用T标记响应的消息！
            $this->logger("<Dev Respone>--------开发服回复 ---- ".date('Y-m-d H:i:s')." ---------<Dev Respone>".PHP_EOL.$result);
        }else{
            //如果没有消息则输出空，并退出
            echo "";
            exit;
        }
    }

     //接收事件消息
    private function receiveEvent($object)
    {
    	//包含函数库
    	include "func.inc.php";    

    	//临时定义一个变量， 不同的事件发生时， 给用户反馈不同的内容
    	$content = "";

    	//======================= 对用户触发的不同事件做处理 =========================
        //trtolower($object->Event)
    	switch ($object->Event)
    	{
    		//-------------------------- 用户关注 触发的事件 --------------------------
    		case "subscribe":

                $content = array();
                $content[] = array("Title"=>"欢迎光临！",  "Description"=>"大家好，我是笨笨，笨笨的笨，笨笨的笨，谢谢！", "PicUrl"=>"https://mmbiz.qlogo.cn/mmbiz_jpg/0sSkmBT5m0YSu61LribFl6Q7NfjficDnrclvJeVtMkSEYCY5jVEpnytZaGzmZkqTiaWa841SNnIYR18WdCEXf0kOw/0");
    			//通过事件中的xml转成的object对象中的FromUserName获取openid
    			$openid = $object->FromUserName;
    			//如果是扫描带参数的二维码
    			if(!empty($object->EventKey)) {
    				//将前缀去了， 只留下参数， 这个参数就是二维码中咱们指定的组ID
    				$groupid = str_replace("qrscene_","",$object->EventKey);
    				//调用func.inc.php中的adduser函数， 将用户加入到指定的组， 并加到指定的数据库
    				adduser( $openid, $groupid);

    				//如果用户传来EventKey事件， 则是扫描二维码的
    				$content .= "\n来自二维码场景 ".$groupid;

    			}else{
    				//如果是扫描自带的二维码使用下面函数添加
    				adduser($openid, 0);
    			}

    			break;
    		//-------------------------- 取消关注时触发的事件 --------------------------
    		case "unsubscribe":
    			$content = "取消关注";
    			//用户在取消关注时将wuser表中的关注列设置为0
    			$openid = $object->FromUserName;
                //调用func.inc.php中的 deluser 函数， 将用户从数据库删除
    			deluser($openid);		

    			break;

    		//--------------------------  扫码事件 --------------------------
    		case "scancode_waitmsg":
                // $content = "scancode_waitmsg 扫码得到结果： ".$object->EventKey ."
                // ScanType： ".$object->ScanCodeInfo->ScanType ."
                // ScanResult： ".$object->ScanCodeInfo->ScanResult;
                switch ($object->EventKey)
                {
                    //图书扫码
                    case "rselfmenu_0_0":
                        $muneId = $object->EventKey;//创建菜单时的 "key": "rselfmenu_0_0", 
                        $scanType = $object->ScanCodeInfo->ScanType;
                        $scanResult = $object->ScanCodeInfo->ScanResult;
                        $isbn = explode(',',$scanResult)[1]; 
                        //------------- 扫图书 ISBN 码 -----------------
                        include '../servers/BooksInfo.class.php';
                        $BooksInfo = new BooksInfo(); 
                        $content =  $BooksInfo->isbn($isbn);//获得回复消息XML
                        //----------------------------------------------
                        break;
                    //商品扫码
                    case "rselfmenu_0_1":
                        $content = $object->ScanCodeInfo->ScanResult;

                        break;
                    //其它
                    default:
                        //什么都不做。让微信自己处理
                        break;
                }


    			break;

            case "scancode_push":
                //这事件一触发，微信就显示它息的界面了。后台貌似只能记录下信息什么的。
                $content = "scancode_push 扫码得到结果： ".$object->EventKey;

                break;
    		//--------------------------  菜单点击事件 --------------------------
    		case "CLICK":

                //创建菜单时设定了每个按钮不同的 key 就是这里的 $object->EventKey 了
    			switch ($object->EventKey)
    			{
    				case "Contact":
    					$content = "有事请来电:".PHP_EOL."18607437722";
    					break;
    				default:
    					$content = "不认识的菜单：".$object->EventKey;
    					break;
    			}

    			break;
    		//--------------------------  获取定位信息事件 --------------------------
    		case "LOCATION":
    			$content = "您当前的位置：纬度 ".$object->Latitude.";经度 ".$object->Longitude;
    			break;

    		//--------------------------  跳转到 URL 事件 --------------------------
    		case "VIEW":
    			$content = "跳转链接 ".$object->EventKey;
    			break;

    		//--------------------------  事件推送群发结果 --------------------------
    		case "MASSSENDJOBFINISH":
    			$content = "消息ID：".$object->MsgID."，结果：".$object->Status."，粉丝数：".$object->TotalCount."，过滤：".$object->FilterCount."，发送成功：".$object->SentCount."，发送失败：".$object->ErrorCount;
    			break;
            //--------------------------  调用相机拍照 --------------------------
            //--------------------------  调用 相机 或 相册--------------------------
            case "pic_sysphoto":
            case "pic_photo_or_album":
              // //不管三七二十一，先把触发的事件存下来。后后续如图像识别的功能，可以按不同事件做相应类别的识别。最终用客服消息接口回复
                $openId = "{$object->FromUserName}";//'oCm6Zw0CCqvl4F6Qpuso0mLBouh0'
                $playInfoKey = 'EventKey';
                $playInfoValue = "{$object->EventKey}";//'ocrVehicleLicense'
                PlayersManage::setPlayerInfo($openId, $playInfoKey, $playInfoValue);

                // //准备发送客服消息          
                // $openId = $object->FromUserName;
                // $serverMsg = new ServerMsg();
                // $serverMsg->send($openId, PlayersManage::getPlayerInfo(),'text');

                // $content = "$openId _______  $playInfoKey _______  $playInfoValue ====== ".PlayersManage::getPlayerInfo() ;

                // $content = json_encode($object ,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
//                 $content = "openId = {$object->FromUserName}
// playInfoKey = 'EventKey'
// playInfoValue = {$object->EventKey}";

                break;
    		//--------------------------  如果不属于以上任何事件那么 --------------------------
    		default:
    			$content = "receive a new event: ".$object->Event;
    			break;
    	}
    //================================== 对用户触发的不同事件做处理 END ===================================

    	//--------------------------  判断回复内容是否为数组，如果是继续处理 --------------------------
    	if(is_array($content)){
    		if (isset($content[0])){
    			$result = $this->transmitNews($object, $content);
    		}else if (isset($content['MusicUrl'])){
    			$result = $this->transmitMusic($object, $content);
    		}
    	}else{
    		$result = $this->transmitText($object, $content);
    	}

    	return $result;
    }

    //接收文本消息
    private function receiveText($object)
    {
    //从接收到的消息中获取用户输入的文本内容， 作为一个查询的关键字， 使用trim()函数去两边的空格
        $keyword = trim($object->Content);

        //自动回复模式
    /*
        if (strstr($keyword, "文本")){
    	     $content = "这是个文本消息";

        }else if (strstr($keyword, "单图文")){

            $content = array();
            $content[] = array("Title"=>"小规模低性能低流量网站设计原则",  "Description"=>"单图文内容", "PicUrl"=>"http://mmbiz.qpic.cn/mmbiz/2j8mJHm8CogqL5ZSDErOzeiaGyWIibNrwrVibuKUibkqMjicCmjTjNMYic8vwv3zMPNfichUwLQp35apGhiciatcv0j6xwA/0", "Url" =>"http://mp.weixin.qq.com/s?__biz=MjM5NDAxMDEyMg==&mid=201222165&idx=1&sn=68b6c2a79e1e33c5228fff3cb1761587#rd");

        }else */
        //自动回复模式
        if (strstr($keyword, "简历") || strstr($keyword, "个人简历")){
            $content = array();
            $content[] = array("Title"=>"基本资料", "Description"=>"姓名：金鑫性别专湖南吉首", "PicUrl"=>"http://mmbiz.qpic.cn/mmbiz_jpg/0sSkmBT5m0YSu61LribFl6Q7NfjficDnrcTwGbLnPQwQqCrxtjC644zYjQma8Nib17Vy8z82JL6APUMzE7DUJbJnQ/0", "Url" =>"http://jerryjin0630.oschina.io/jerry/resume/index.pdf");

            $content[] = array("Title"=>"个人简介", "Description"=>"不超过120字", "PicUrl"=>"https://mmbiz.qlogo.cn/mmbiz_gif/0sSkmBT5m0YSu61LribFl6Q7NfjficDnrc8NmVB4CvHVU5UgRkovLHHZiaLOSxnAia5JzJicGUhYPr1opV7ds1CKocA/s640", "Url" =>"http://jerryjin0630.oschina.io/jerry/resume/index.pdf");

            $content[] = array("Title"=>"工作经验", "Description"=>"不超过120字", "PicUrl"=>"https://mmbiz.qlogo.cn/mmbiz_jpg/0sSkmBT5m0YSu61LribFl6Q7NfjficDnrcvURL5Z5NpXAe6UaQ2NhXZrs93ibBbicORicGU7dEpYUiaIUSCO52ibZKCEw/0?wx_fmt=jpeg", "Url" =>"http://jerryjin0630.oschina.io/jerry/resume/index.pdf");

            $content[] = array("Title"=>"联系方式", "Description"=>"手机：18607437722
    微信：Jerryjin_  （可以直接扫码添加）
    邮箱：jerryjin0630@QQ.com", "PicUrl"=>"https://mmbiz.qlogo.cn/mmbiz_png/0sSkmBT5m0YSu61LribFl6Q7NfjficDnrcZDhia8Fz5JT5r1EVNrHrwvbR5HoTnVoOPYq7qnGgpHmQiaFzckTgIdqQ/0", "Url" =>"http://jerryjin0630.oschina.io/jerry/resume/index.pdf");


        }else if (strstr($keyword, "音乐")){
            $content = array();
            $content = array("Title"=>"come some music", "Description"=>"来首歌听听", "MusicUrl"=>"http://jerryjin.5gbfree.com/music/wwd.mp3", "HQMusicUrl"=>"http://jerryjin.5gbfree.com/music/wwd.mp3");
        }else{//如果都没有就。。。
            
            //$content = date("Y-m-d H:i:s",time())."\n你说啥，笨笨听不懂！哈哈哈哈";
            $content = "你说啥，笨笨听不懂！哈哈哈哈";
        }


        //处理回复消息内容
        if(is_array($content)){
            if (isset($content[0]['PicUrl'])){
                $result = $this->transmitNews($object, $content);
            }else if (isset($content['MusicUrl'])){
                $result = $this->transmitMusic($object, $content);
            }
        }else{
            $result = $this->transmitText($object, $content);
        }
     

        return $result;
    }

    //接收图片消息        echo str_pad('',4096);
    private function receiveImage($object)
    {
        //动态输出空字符串，微信收到后就不会报超时了。
        //之后的代码还可以继续。

        //输出空字符串，让微信收到后就不会报超时。
        // print str_repeat(" ", 4096);
        // echo '';
        // ob_flush();
        // flush(); 
        // //图版分析功能。时间较长。
        // $fppi = new FacePlusPlusWX();
        // $content =  $fppi->faceDetectWX($url); 

        //调取菜单点击的记录
        $openId = "{$object->FromUserName}";
        $playInfoKey = 'EventKey';
        $playerLastOperate = PlayersManage::getPlayerInfo($openId, $playInfoKey);
        //trtolower($playerLastOperate)
        //因为图片事件和菜单事件是分开的，所以要靠菜单来判断，这图片拿来作甚。
        switch ($playerLastOperate)//创建菜单时的 "key": "rselfmenu_0_0", 
        {
            //人脸识别        
            case "faceDetect":
                $content = "人脸识别";

                break;
            //场景物体
            case "detectSceneAndObject":
                $content = "场景物体";

                break;
            //驾照识别
            case "ocrDriverLicense":
                $content = "驾照识别";

                break;
            //行驶证识别
            case "ocrVehicleLicense":
                $content = "行驶证识别";
                break;
            //二代身份证
            case "ocrIdCard":
                $content = "二代身份证";
                break;
            //普通发图
            default:
                // $content = array("MediaId"=>$object->MediaId);
                // $result = $this->transmitImage($object, $content);
                $content = "普通图片消息";
                break;
        }
        
        //输出空，免得微信报超时
       // echo '';
       // 
        include_once '../lib/ServerMsg.class.php';
        //从微信公众号服务端下载资源
        $mediaId = "{$object->MediaId}";
        $image = Media::download($mediaId);
        //保存到本地
        $fileManage = new FileManage();
        $fileManage->saveImage($image, $mediaId);
        //heroku 服务器上的 URL 
        $url = 'https://heroku-weixin.herokuapp.com/weixin/images/{$mediaId}.jpg';
        // //$path = '@./images/'.$mediaId.'.jpg';
        // //请求识别图像
        // $fppi = new FacePlusPlusWX();
        // $content =  $fppi->faceDetectWX($url); 

        // if (is_array($content)) {
        //     $content['picurl']= "{$object->PicUrl}";
        //     $content['url']= "{$object->PicUrl}";
        //     //准备发送客服消息
        //     $serverMsg = new ServerMsg();
        //     // $serverMsg->send($openId, '客服消息：'.$result,'text');
        //     $serverMsg->send($openId, $content,'news');
        // }else{
        //     $serverMsg = new ServerMsg();
        //     $serverMsg->send($openId, $content .PHP_EOL.$url,'text');
        // }
        $serverMsg = new ServerMsg();
        $serverMsg->send($openId, $content .PHP_EOL.$url,'text');
        // $result = json_encode($content ,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        //正常回复消息。
        //$result = $this->transmitText($object, $content .PHP_EOL. $openId .PHP_EOL. $playInfoKey.PHP_EOL. $playerLastOperate . PHP_EOL.$mediaId);//.' +++ '. $playerLastOperate);
        
        //处理完了,清空状态。不然普通发图就会被误读了
        PlayersManage::setPlayerInfo($openId, $playInfoKey, 'null');

        //exit;
        return $result;
    }

    //接收位置消息
    private function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    private function receiveVoice($object)
    {

    	/*
    	
    		//如果开启语言识别功能， 就可以使用这个
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
    		$content = "未开启语音识别功能或者识别内容为空";
    		 $result = $this->transmitText($object, $content);
    	}
    	
    	
    	*/

    	//如果开启语言识别功能， 就可以使用这个
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }

    //接收视频消息
    private function receiveVideo($object)
    {
        //$content = array("MediaId"=>$object->MediaId, "Title"=>"视频调试，原样返回!", "Description"=>"pai pai");
        //$result = $this->transmitVideo($object, $content);
        $content = "视频的 MediaId： ".$object->MediaId;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收链接消息
    private function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }
    //======================================  回复消息  ===============================================
    //回复文本消息
    private function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[text]]></MsgType>
    <Content><![CDATA[%s]]></Content>
    </xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息
    private function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
    </Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[image]]></MsgType>
    $item_str
    </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复语音消息
    private function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId>
    </Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[voice]]></MsgType>
    $item_str
    </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复视频消息
    private function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    </Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[video]]></MsgType>
    $item_str
    </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        if(!is_array($newsArray)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
    ";
        $item_str = "";
        foreach ($newsArray as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[news]]></MsgType>
    <ArticleCount>%s</ArticleCount>
    <Articles>
    $item_str</Articles>
    </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
        return $result;
    }

    //回复音乐消息
    private function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
    </Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
    <ToUserName><![CDATA[%s]]></ToUserName>
    <FromUserName><![CDATA[%s]]></FromUserName>
    <CreateTime>%s</CreateTime>
    <MsgType><![CDATA[music]]></MsgType>
    $item_str
    </xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }



    //日志记录
    private function logger($log_content)
    {
      
        $max_size = 10000;   //声明日志的最大尺寸

        $log_filename = "log.xml";  //日志名称

        //如果文件存在并且大于了规定的最大尺寸就删除了
        if(file_exists($log_filename) && (abs(filesize($log_filename)) > $max_size)){
    	    unlink($log_filename);
        }

        //写入日志，内容前加上时间， 后面加上换行， 以追加的方式写入
        //file_put_contents($log_filename, date('H:i:s')." ".$log_content.PHP_EOL, FILE_APPEND);

        $temp=file_get_contents($log_filename); //获取文件原内容
        //$add_str= date('H:i:s')." ".$log_content.PHP_EOL; //准备要添加的新内容
        $add_str= $log_content.PHP_EOL; //准备要添加的新内容
        file_put_contents($log_filename, $add_str.$temp); //写入：新内容.原内容
        
    }
}
