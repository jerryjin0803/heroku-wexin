<?php 
	//如果文件存在并且大于了规定的最大尺寸就删除了
	$log_filename = 'log.xml'; 
    if(file_exists($log_filename)){
	    unlink($log_filename);
    }
    echo "日志已清除!";
?> 