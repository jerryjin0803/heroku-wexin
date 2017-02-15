<?php

class PlayersManage {

    const PLAYER_DATA = './player_data.txt'; //玩家临时数据存放文件

    public static function setPlayerInfo($openId ,$key , $value)
    {
        //读取出玩家信息状态文件内容，转成数组
        $allPlayerInfo = file_get_contents(PlayersManage::PLAYER_DATA);
        $allPlayerInfoArray = json_decode($allPlayerInfo, true);
        //根据 openId 设置 key : value
        $allPlayerInfoArray[$openId][$key] = $value;
        //再把 数组转回 JSON 
        $fileData = json_encode($allPlayerInfoArray, JSON_UNESCAPED_UNICODE);
        //把 JSON 保存回文件中
        //$f = fopen(PlayersManage::PLAYER_DATA, 'w');//w+
        // fwrite($f, $fileData);
        // fclose($f);
        return self::fileWrite(PlayersManage::PLAYER_DATA, $fileData);
    }

    public static function getPlayerInfo($openId = '', $key = '')
    {

        //读取出玩家信息状态文件内容
        $allPlayerInfo = file_get_contents(PlayersManage::PLAYER_DATA);
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

    public static function removePlayerInfo($openId = '', $key = '')
    {
        //读取出玩家信息状态文件内容
        $allPlayerInfo = file_get_contents(PlayersManage::PLAYER_DATA);
        //转成数组
        $allPlayerInfoArray = json_decode($allPlayerInfo, true);
        //根据 openId 删除指定 key 
        unset($allPlayerInfoArray[$openId][$key]);
        //再把 数组转回 JSON 
        $fileData = json_encode($allPlayerInfoArray, JSON_UNESCAPED_UNICODE);
        //把 JSON 保存回文件中
        // $f = fopen(PlayersManage::PLAYER_DATA, 'w');//w+
        // fwrite($f, $fileData);
        // fclose($f);
        return self::fileWrite(PlayersManage::PLAYER_DATA, $fileData);
    }

    private static function fileWrite($fileName, $fileData)
    {
        if ($f = fopen ($fileName, 'w' )) {
           $startTime = microtime ();
           do {
              $canWrite = flock ( $f, LOCK_EX );
              if (! $canWrite)
              usleep ( round ( rand ( 0, 100 ) * 1000 ) );
           } while ( (! $canWrite) && ((microtime () - $startTime) < 1000) );
           if ($canWrite) {
              fwrite ( $f, $fileData );
           }
           fclose ( $f );
        }

        return false;
    }
    
    private static function fileRead($fileName)
    {
        if ($f = fopen ($fileName, 'r' )) {
           $startTime = microtime ();
           do {
              $canRead = flock ( $f, LOCK_EX );
              if (! $canRead)
              usleep ( round ( rand ( 0, 100 ) * 1000 ) );
           } while ( (! $canRead) && ((microtime () - $startTime) < 1000) );
           if ($canRead) {
                while (!feof($f)) {
                    $buffer = fgets($f)
                }
                return $buffer;
           }
           fclose ( $f );
        }

        return false;
    }
}
// PlayersManage::setPlayerInfo('openIsdfsesd5','key', 'fssssssssssssssssssssss');
// //echo PlayersManage::getPlayerInfo();
// //
// PlayersManage::setPlayerInfo('openI1','key1', 'f11111111111111111ssss');
// PlayersManage::setPlayerInfo('openI1','key2', 'f222222222222222s');
// PlayersManage::setPlayerInfo('openI1','key3', '33333333333333333');

// PlayersManage::removePlayerInfo('openI1','key3');

// print_r((json_decode(PlayersManage::getPlayerInfo(), true)));
