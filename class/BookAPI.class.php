<?php
/*###########################################################################
* @作者  :  JerryJin 
* @签名  :  大家好，我是笨笨，笨笨的笨，笨笨的笨，谢谢！
* @日期  :  2017-02-09 13:19:59
* @授权  :  捡个肥皂
* @说明  :  豆瓣API 做图书搜索功能
###########################################################################*/
/*
错误码	错误信息	含义	status code
6000	book_not_found	图书不存在	404
6002	unauthorized_error	没有修改权限	403
6004	review_content_short(should more than 150)	书评内容过短（需多于150字）	400
6006	review_not_found	书评不存在	404
6007	not_book_request	不是豆瓣读书相关请求	403
6008	people_not_found	用户不存在	404
6009	function_error	服务器调用异常	400
6010	comment_too_long(should less than 350)	短评字数过长（需少于350字）	400
6011	collection_exist(try PUT if you want to update)	该图书已被收藏（如需更新请用PUT方法而不是POST）	409
6012	invalid_page_number(should be digit less than 1000000)	非法页码（页码需要是小于1000000的数字）	400
6013	chapter_too_long(should less than 100)	章节名过长（需小于100字）	400
*/

include '../lib/cURL.class.php';

class BookAPI {
	private $serverUrl = "https://api.douban.com";
	private $api = array(
		'获取图书信息' => '/v2/book/',
		'根据isbn获取图书信息' => '/v2/book/isbn/',
		'搜索图书' => '/v2/book/search/',
		'获取某个图书中标记最多的标签' => '/v2/book/',
		'获取用户对图书的所有标签' => '/v2/book/user/',
		'获取某个用户的所有图书收藏信息' => '/v2/book/user/',
		'获取用户对某本图书的收藏信息' => '/v2/book/',
		'获取某个用户的所有笔记' => '/v2/book/user/',
		'获取某本图书的所有笔记' => '/v2/book/',
		'获取某篇笔记的信息' => '/v2/book/annotation/',
		'获取丛书书目信息' => '/v2/book/series/'
	);

	/**
	 * 豆瓣API：通过 isbn 查书
	 * @param string $isbn 图的ISBN码
	 * @return string 查寻结果JSON
	 */
	function isbn($isbn)
	{
		//拼接出完整的 API 地址
		$url = $this->serverUrl . $this->api['根据isbn获取图书信息'];	
		//拼接 GET 参数
		$url = $url.$isbn;	
		//echo $url;
		$curl = new cURL();	//实例化一个 cURL
		$book = $curl->get($url);	//发送 GET 请求,返回结果
		$book = $this->analyzeBookInfo($book);	//解析数据，格式化，以便调用

		return $book;
	}

	/**
	 * 解析数据，将JSON字符串转成数组
	 * @param  string $book 原始数据JSON
	 * @return array       格式化后的数组
	 */
	function analyzeBookInfo($book){
		$book_array = (array) json_decode($book, true);
		return $book_array;
	}
	
}

// //-------------- test code ------------------------
// $BookAPI = new BookAPI();
// //print_r($BookAPI->isbn("9787111135104")) ;
// print_r($BookAPI->isbn("9787547609491")) ;



