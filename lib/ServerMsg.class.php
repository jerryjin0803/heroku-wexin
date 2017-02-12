<?php

include_once '../lib/cURL.class.php';
include_once '../lib/accesstoken.lib.php';

class ServerMsg{
    //接口URL
    private $apiUrl = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='; 

    //按类型别分处理JSON格式数据，然后调用发送
    public function send($openId, $msgData, $msgType){

        $jsonMsg = '';
        //获取对应类型的回复消息(格式为数组,后面正式发之前会转成JSON)
        switch ($msgType) {
            case 'text':
                $jsonMsg = $this->_msgtype_text($openId, $msgData);
                break;  
            case 'news':
                $jsonMsg = $this->_msgtype_news($openId, $msgData);
                break;
            default:
                # code...
                break;
        }

        //消息数组转成JSON 
        $jsonMsg = json_encode($jsonMsg,JSON_UNESCAPED_UNICODE);

        //用客服消息接口回复消息
        $this->_send($jsonMsg);

    }
    /**
     * 调用接口回复消息 POST
     * @param  string $postData 接口接受的JSON格式
     * @return [type]           完事了
     */
    private function _send($postData){
        $url = $this->apiUrl . AccessToken::getAccessToken();

        echo $url.PHP_EOL;
        echo $postData;
        // return '';
        $ch = new cURL();   //实例化一个 cURL
        $output = $ch->post($url, $postData);   //发送 POST 请求,返回结果
        $output = (array) json_decode($output, true);        //把 JSON 编码的字符串转换为 PHP 数组
    }


    private function _msgtype_text($openId, $data) {

        $jsonMsg = array(
            "touser"=> $openId,
            "msgtype"=>"text",
            "text"=> array(
                "content"=> '笨笨：'.PHP_EOL.$data
                )
            );
 
        return $jsonMsg;
    }

    private function _msgtype_news($openId, $data) {

        $jsonMsg = Array(
            'touser' => $openId,
            'msgtype' => 'news',
            'news' => Array(
                    'articles' => Array(
                            '0' => Array(
                                    'title' => '笨笨：'.PHP_EOL.$data['title'],
                                    'description' =>  $data['description'],
                                    'picurl' =>  $data['url'],
                                    'url' =>  $data['url']
                                )
                        )
                )
        );
        return $jsonMsg;
    }
}

//===================================


// $openId = "oCm6Zw0CCqvl4F6Qpuso0mLBouh0";
// $serverMsg = new ServerMsg();
// $serverMsg->send($openId, '测试内容','text');

// $openId = "oCm6Zw0CCqvl4F6Qpuso0mLBouh0";
// $images = "http://mmbiz.qpic.cn/mmbiz_jpg/6MdIErTYeGibqzsmDiaS3OdyjqcnA2nLGiaVavwyGFIwgTk122UUxzpOibA6gEkZjv4UC91oN7hM9XUWOPQZ720tTQ/0";
// $postData = array(
//     "title" => 'WARNING！！！发现'.$gender . "靓照", 
//     "description" => '图文消息测试', 
//     'picurl' =>  $images,
//     "url" => $images
// );
// $serverMsg = new ServerMsg();
// $serverMsg->send($openId, $postData,'news');



//$postData = '{"touser":"oCm6Zw0CCqvl4F6Qpuso0mLBouh0","msgtype":"news","news":{"articles":[{"title":"Happy Day","description":"Is Really A Happy Day","url":"http://www.baidu.com","picurl":"http://mmbiz.qpic.cn/mmbiz_jpg/6MdIErTYeGibqzsmDiaS3OdyjqcnA2nLGiaVavwyGFIwgTk122UUxzpOibA6gEkZjv4UC91oN7hM9XUWOPQZ720tTQ/0"},{"title":"Happy Day","description":"Is Really A Happy Day","url":"http://www.baidu.com","picurl":"http://mmbiz.qpic.cn/mmbiz_jpg/6MdIErTYeGibqzsmDiaS3OdyjqcnA2nLGiaVavwyGFIwgTk122UUxzpOibA6gEkZjv4UC91oN7hM9XUWOPQZ720tTQ/0"}]}}';
// $url = $apiUrl . AccessToken::getAccessToken();
// echo  $url ;
// $ch = new cURL();   //实例化一个 cURL
// $output = $ch->post($url, $postData);   //发送 POST 请求,返回结果
// // $output = (array) json_decode($output, true);        //把 JSON 编码的字符串转换为 PHP 
// print_r($output) ;