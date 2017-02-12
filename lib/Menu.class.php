<?php

include_once '../../lib/accesstoken.lib.php';
include_once '../../lib/cURL.class.php';

class Menu{
//     const MENU_JSON = '{
//     "button": [
//         {
//             "name": "扫码识别", 
//             "sub_button": [
//                 {
//                     "type": "scancode_waitmsg", 
//                     "name": "图书扫码", 
//                     "key": "rselfmenu_0_0", 
//                     "sub_button": [ ]
//                 }, 
//                 {
//                     "type": "scancode_push", 
//                     "name": "扫二维码", 
//                     "key": "rselfmenu_0_1", 
//                     "sub_button": [ ]
//                 }
//             ]
//         }, 
//         {
//             "name": "图像识别", 
//             "sub_button": [
//                 {
//                     "type": "pic_sysphoto", 
//                     "name": "二代身份证", 
//                     "key": "rselfmenu_1_0", 
//                     "sub_button": [ ]
//                 }, 
//                 {
//                     "type": "pic_photo_or_album", 
//                     "name": "人脸识别", 
//                     "key": "rselfmenu_1_1", 
//                     "sub_button": [ ]
//                 }
//             ]
//         }, 
//         {
//             "name": "我的资料", 
//             "sub_button": [
//                 {
//                     "type": "view", 
//                     "name": "简历", 
//                     "url": "http://www.baidu.com", 
//                     "sub_button": [ ]
//                 }, 
//                 {
//                     "type": "click", 
//                     "name": "联系方式", 
//                     "key": "Contact"
//                 }
//             ]
//         }
//     ]
// }';

 const MENU_JSON = '{"button":[{"name":"扫码识别","sub_button":[{"type":"scancode_waitmsg","name":"图书扫码","key":"rselfmenu_0_0","sub_button":[]},{"type":"scancode_push","name":"扫二维码","key":"rselfmenu_0_1","sub_button":[]}]},{"name":"图像识别","sub_button":[{"type":"pic_sysphoto","name":"人脸识别","key":"faceDetect","sub_button":[]},{"type":"pic_photo_or_album","name":"场景物体","key":"detectSceneAndObject","sub_button":[]},{"type":"pic_photo_or_album","name":"驾照识别","key":"ocrDriverLicense","sub_button":[]},{"type":"pic_photo_or_album","name":"行驶证识别","key":"ocrVehicleLicense","sub_button":[]},{"type":"pic_photo_or_album","name":"二代身份证","key":"ocrIdCard","sub_button":[]}]},{"name":"我的资料","sub_button":[{"type":"view","name":"我的博客","url":"http://blog.csdn.net/jx520","sub_button":[]},{"type":"click","name":"联系方式","key":"Contact"}]}]}';

    private static $apiUrl = array(
        '自定义菜单创建接口' => 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=',
        '自定义菜单查询接口' => 'https://api.weixin.qq.com/cgi-bin/menu/get?access_token=',
        '自定义菜单删除接口' => 'https://api.weixin.qq.com/cgi-bin/menu/delete?access_token='
        );

    public static function create($menuJson='')
    {
        $url = self::$apiUrl['自定义菜单创建接口'].AccessToken::getAccessToken();

        $curl = new Curl();
        return $curl->post($url, $menuJson);
    }



}
// //====================== test 下载多媒体========================

//echo Menu::create(Menu::MENU_JSON);
//
print_r(json_decode(Menu::MENU_JSON, true));
              