<?php

class PlayersManage {

    const PLAYER_DATA = 'player_data.txt'; //玩家临时数据存放文件

    public static function setPlayerInfo($openId ,$key , $value)
    {
        //读取出玩家信息状态文件内容，转成数组
        $allPlayerInfo = @file_get_contents(PlayersManage::PLAYER_DATA);
        $allPlayerInfoArray = json_decode($allPlayerInfo, true);
        //根据 openId 设置 key : value
        $allPlayerInfoArray[$openId][$key] = $value;
        //再把 数组转回 JSON 
        $fileData = json_encode($allPlayerInfoArray, JSON_UNESCAPED_UNICODE);
        //把 JSON 保存回文件中
        $f = fopen(PlayersManage::PLAYER_DATA, 'w');//w+
        fwrite($f, $fileData);
        fclose($f);
    }

    public static function getPlayerInfo($openId = '', $key = '')
    {

        //读取出玩家信息状态文件内容
        $allPlayerInfo = @file_get_contents(PlayersManage::PLAYER_DATA);
        //将出玩家信息转成数组
        $allPlayerInfoArray = json_decode($allPlayerInfo, true);

        //如果没传 openId 就返回所有玩家信息
        if ($openId == '') {
            return json_encode($allPlayerInfoArray ,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        //如果没传 key 就返回所有玩家信息
        if ($key == '') {
            return json_encode($allPlayerInfoArray[$openId] ,JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }
        //根据 openId : key 取 value
        return $allPlayerInfoArray[$openId][$key];
    }
}
//PlayersManage::setPlayerMenuOperate('openId5345345435','key2323', 'vvvvvvvvvvvvvvvvvvvvvvvvg');
// echo PlayersManage::getPlayerInfo('openId','key1');
// print_r((json_decode(PlayersManage::getPlayerInfo(), true)));
?>