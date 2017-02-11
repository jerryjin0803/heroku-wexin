<?php
//namespace LaneWeChat\Core;
/**
 *
 * CURL工具
 *
 * Class Curl
 * Created by Lane.
 * @Author: lane
 * @Mail: lixuan868686@163.com
 * @Date: 14-1-10
 * @Time: 下午4:22
 * Mail: lixuan868686@163.com
 * Website: http://www.lanecn.com
 */
class Curl {
	private static $_ch;
	private static $_header;
	private static $_body;
	
	private static $_cookie = array();
    private static $_options = array();
    private static $_url = array ();
    private static $_referer = array ();

    /**
     * 调用外部url
     * @param $queryUrl
     * @param $param 参数
     * @param string $method
     * @return bool|mixed
     */
    public static function callWebServer($queryUrl, $param='', $method='get', $is_json=true, $is_urlcode=true) {
        if (empty($queryUrl)) {
            return false;
        }
        $method = strtolower($method);
        $ret = '';
        $param = empty($param) ? array() : $param;
        self::_init();
        if ($method == 'get') {
            $ret = self::_httpGet($queryUrl, $param);
        } elseif($method == 'post') {
            $ret = self::_httpPost($queryUrl, $param, $is_urlcode);
        }
        
        if(!empty($ret)){            
            if($is_json){
                return json_decode($ret, true);
            }else{
                return $ret;
            }
        }
        return true;
    }

    private static function _init() {
        self::$_ch = curl_init();

        curl_setopt(self::$_ch, CURLOPT_HEADER, true);
        curl_setopt(self::$_ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt(self::$_ch, CURLOPT_FRESH_CONNECT, true);
    }

    public static function setOption($optArray=array()) {
		foreach($optArray as $opt) {
			curl_setopt(self::$_ch, $opt['key'], $opt['value']);
		} 
	}
	
	private static function _close() {
		if (is_resource(self::$_ch)) {  
            curl_close(self::$_ch);  
        }
        
        return true;
	}
	
	private static function _httpGet($url, $query=array()) {
          
        if (!empty($query)) {  
            $url .= (strpos($url, '?') === false) ? '?' : '&';  
            $url .= is_array($query) ? http_build_query($query) : $query;  
        }  
          
        curl_setopt(self::$_ch, CURLOPT_URL, $url);
        curl_setopt(self::$_ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(self::$_ch, CURLOPT_HEADER, 0);
        curl_setopt(self::$_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt(self::$_ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt(self::$_ch, CURLOPT_SSLVERSION, 1);

        $ret = self::_execute();
        self::_close();
        return $ret;  
	}
	
	private static function _httpPost($url, $query=array(), $is_urlcode=true) {
  		if (is_array($query)) {
            foreach ($query as $key => $val) {  
				if($is_urlcode){
                    $encode_key = urlencode($key);
                }else{
                    $encode_key = $key;
                }
				if ($encode_key != $key) {  
					unset($query[$key]);  
				}
                if($is_urlcode){
                    $query[$encode_key] = urlencode($val);
                }else{
                    $query[$encode_key] = $val;
                }

            }  
        }
        curl_setopt(self::$_ch, CURLOPT_URL, $url);
        curl_setopt(self::$_ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(self::$_ch, CURLOPT_HEADER, 0);
        curl_setopt(self::$_ch, CURLOPT_POST, true );
        curl_setopt(self::$_ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt(self::$_ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt(self::$_ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt(self::$_ch, CURLOPT_SSLVERSION, 1);


        $ret = self::_execute();
        self::_close();
        return $ret;  
	}
	
	private static function _put($url, $query = array()) {
		curl_setopt(self::$_ch, CURLOPT_CUSTOMREQUEST, 'PUT');  
	
		return self::_httpPost($url, $query);
	}  

	private static function _delete($url, $query = array()) {
		curl_setopt(self::$_ch, CURLOPT_CUSTOMREQUEST, 'DELETE');  
	
		return self::_httpPost($url, $query);
	}  

	private static function _head($url, $query = array()) {
		curl_setopt(self::$_ch, CURLOPT_CUSTOMREQUEST, 'HEAD');
		
		return self::_httpPost($url, $query);
	}  
	
	private static function _execute() {
		$response = curl_exec(self::$_ch);
		$errno = curl_errno(self::$_ch);

		if ($errno > 0) {
			throw new \Exception(curl_error(self::$_ch), $errno);
		}
		return  $response;
	}

	public function get($url, $query=array()) {
		//检测url必须参数 不能为空
        if (!$url) {
            throw new Exception("错误：curl请求地址不能为空");
        }
        if (!empty($query)) {  
            $url .= (strpos($url, '?') === false) ? '?' : '&';  
            $url .= is_array($query) ? http_build_query($query) : $query;  
        }  
        self::_init();
		$data = self::_httpGet($url, $query);
//	    //初始化
//	    $curl = curl_init();
//	    //设置抓取的url
//	    curl_setopt($curl, CURLOPT_URL, $url);
//	    //设置头文件的信息作为数据流输出
//	    curl_setopt($curl, CURLOPT_HEADER, 1);
//	    //设置获取的信息以文件流的形式返回，而不是直接输出。
//	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//	    //FALSE 禁止 cURL 验证对等证书（peer's certificate）。要验证的交换证书可以在 CURLOPT_CAINFO 选项中设置，或在 CURLOPT_CAPATH中设置证书目录。
//	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//	    //禁止头文件的信息作为数据流输出
//	    curl_setopt($curl, CURLOPT_HEADER, false);
//
//	    //执行命令
//	    $data = curl_exec($curl);
//	    //关闭URL请求
//	    curl_close($curl);
//	    //返回获得的数据
	    return $data;
	}

	public function post($url = '', $query=array(), $is_urlcode = true) {
		//检测url必须参数 不能为空
        if (!$url) {
            throw new Exception("错误：curl请求地址不能为空");
        }
        if (!empty($query)) {  
            $url .= (strpos($url, '?') === false) ? '?' : '&';  
            $url .= is_array($query) ? http_build_query($query) : $query;  
        }  
        self::_init();
		$data = self::_httpPost($url, $query, $is_urlcode);
//	    //初始化
//	    $curl = curl_init();
//	    //设置抓取的url
//	    curl_setopt($curl, CURLOPT_URL, $url);
//	    //设置头文件的信息作为数据流输出
//	    //curl_setopt($curl, CURLOPT_HEADER, true);
//	    //禁止头文件的信息作为数据流输出
//	    curl_setopt($curl, CURLOPT_HEADER, false);	
//	    //设置获取的信息以文件流的形式返回，而不是直接输出。
//	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//	    //FALSE 禁止 cURL 验证对等证书（peer's certificate）。要验证的交换证书可以在 CURLOPT_CAINFO 选项中设置，或在 CURLOPT_CAPATH中设置证书目录。
//	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//	    //设置post方式提交
//	    //curl_setopt($curl, CURLOPT_POST, 1);
//	    //设置post方式提交
//		curl_setopt($curl, CURLOPT_POST, count($post_data));	//值为true 则使用 post 方式请求
//		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);	//post 提交的数据
//
//	    //执行命令
//	    $data = curl_exec($curl);
//	    //关闭URL请求
//	    curl_close($curl);
	    //返回获得的数据
	    return $data;
	}
}

?>