<?php

class FileManage {

    public function saveImage($image, $filename = '', $save_dir = '')
    {

        $ext=".jpg";//以jpg的格式结尾  
        clearstatcache();//清除文件缓存  

        if(trim($save_dir)==''){  
            $save_dir='./images';  
        }  
        if(trim($filename)==''){//保存文件名  
            $filename=time().$ext;  
        }else{  
            $filename = $filename.$ext;  
        }  
        if(0!==strrpos($save_dir,'/')){  
            $save_dir.='/';  
        }  

        //创建保存目录  
        if(!is_dir($save_dir)){//文件夹不存在，则新建  
            //print_r($save_dir."文件不存在");  
            mkdir(iconv("UTF-8", "GBK", $save_dir),0777,true);  
            //mkdir($save_dir,0777,true);  
        }  

        //保存文件
        $fp2=@fopen($save_dir.$filename,'w');  
        fwrite($fp2,$image);  
        fclose($fp2);  
    }
}

?>