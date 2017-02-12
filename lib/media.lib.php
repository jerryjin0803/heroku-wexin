<?php
//namespace LaneWeChat\Core;
//include '../lib/accesstoken.lib.php';
include_once 'accesstoken.lib.php';
//include 'msg.lib.php';

/**
 * 多媒体的上传与下载
 * Created by Lane.
 * User: lane
 * Date: 14-8-11
 * Time: 上午9:51
 * E-mail: lixuan868686@163.com
 * WebSite: http://www.lanecn.com
 */
class Media{
    /**
     * 多媒体上传。上传图片、语音、视频等文件到微信服务器，上传后服务器会返回对应的media_id，公众号此后可根据该media_id来获取多媒体。
     * 上传的多媒体文件有格式和大小限制，如下：
     * 图片（image）: 1M，支持JPG格式
     * 语音（voice）：2M，播放长度不超过60s，支持AMR\MP3格式
     * 视频（video）：10MB，支持MP4格式
     * 缩略图（thumb）：64KB，支持JPG格式
     * 媒体文件在后台保存时间为3天，即3天后media_id失效。
     *
     * @param $filename，文件绝对路径
     * @param $type, 媒体文件类型，分别有图片（image）、语音（voice）、视频（video）和缩略图（thumb）
     * @return {"type":"TYPE","media_id":"MEDIA_ID","created_at":123456789}
     */
    public static function upload($filename, $type){
        //获取ACCESS_TOKEN
        $accessToken = AccessToken::getAccessToken();
        $queryUrl = 'http://file.api.weixin.qq.com/cgi-bin/media/upload?access_token='.$accessToken.'&type='.$type;
        $data = array();
        $data['media'] = '@'.$filename;
        return Curl::callWebServer($queryUrl, $data, 'POST', 1 , 0);
    }

    /**
     * 下载多媒体文件
     * @param $mediaId 多媒体ID
     * @return 头信息如下
     *
     * HTTP/1.1 200 OK
     * Connection: close
     * Content-Type: image/jpeg
     * Content-disposition: attachment; filename="MEDIA_ID.jpg"
     * Date: Sun, 06 Jan 2013 10:20:18 GMT
     * Cache-Control: no-cache, must-revalidate
     * Content-Length: 339721
     * curl -G "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID"
     */
    public static function download($mediaId){
        //获取ACCESS_TOKEN
        $accessToken = AccessToken::getAccessToken();
        //https://api.weixin.qq.com/cgi-bin/media/get?access_token=ACCESS_TOKEN&media_id=MEDIA_ID
        //$queryUrl = 'http://file.api.weixin.qq.com/cgi-bin/media/get?access_token='.$accessToken.'&media_id='.$mediaId;
        $queryUrl = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$accessToken.'&media_id='.$mediaId;
        echo $queryUrl;
        return Curl::callWebServer($queryUrl, '', 'GET', 0);
    }
}
// //====================== test 下载多媒体========================
// $mediaId = "TUB0RI1eVGSlEpEm_tvmLkfLL7WpbS55VzKqqjnLMsoqErzdMHG9lRozzV-WtM6v";
// $img = Media::download($mediaId);
// echo '一样吗：'.gettype($img)  !== gettype("http://");

// $save_dir = "./imgs/";
// $filename = $mediaId.".jpg";

// //创建保存目录  
// if(!is_dir($save_dir)){//文件夹不存在，则新建  
//     //print_r($save_dir."文件不存在");  
//     mkdir(iconv("UTF-8", "GBK", $save_dir),0777,true);  
//     //mkdir($save_dir,0777,true);  
// }  

// //保存文件
// $fp2=@fopen($save_dir.$filename,'w');  
// fwrite($fp2,$img);  
// fclose($fp2);  

// //如果文件不存在就创建一下
// // If(!file_exists("fileName")){
// //     echo file_put_contents("fileName",'contents');
// // }