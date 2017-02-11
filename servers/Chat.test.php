<?
function xiaojo($keyword){

        $curlPost=array("chat"=>$keyword);
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,'http://www.xiaojo.com/bot/chata.php');//抓取指定网页
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        if(!empty($data)){
            return $data;
        }else{
            $ran=rand(1,5);
            switch($ran){
                case 1:
                    return "小鸡鸡今天累了，明天再陪你聊天吧。";
                    break;
                case 2:
                    return "小鸡鸡睡觉喽~~";
                    break;
                case 3:
                    return "呼呼~~呼呼~~";
                    break;
                case 4:
                    return "你话好多啊，不跟你聊了";
                    break;
                case 5:
                    return "你真的好无聊";
                    break;
                default:
                    return "今天就不聊了";
                    break;
            }
        }
    }

    echo xiaojo('你好');