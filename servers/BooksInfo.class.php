<?php

/*###########################################################################
* @作者  :  JerryJin
* @签名  :  大家好，我是笨笨，笨笨的笨，笨笨的笨，谢谢！
* @日期  :  2017-02-09 14:35:34
* @授权  :  表客气，当自己家 ^_^
* @说明  :  微信公众号调用。
实现了扫码查书功能。扫过的图书会存到数据库中，省去手动输入的汗水。用户可从
服务器上下载数据。
###########################################################################*/

include '../class/BookAPI.class.php';

class BooksInfo {

	/**
	 * 传入 ISBN 码，返回用于微信回复的 XML 数据
	 * @param  string $isbn ISBN图书码
	 * @return string       微信公众号回复消息 XML 格式
	 */
	function isbn($isbn) {
		//检测$isbn必须参数 不能为空
        if (!$isbn) { throw new Exception("错误：curl请求地址不能为空"); }

		//查寻图书信息，并处理好回复内容。
		$bookApi = new BookAPI();
		$bookInfo = $bookApi->isbn($isbn);
		
		//提取信息
		$author = join(' / ',$bookInfo['author']);	//作者是个 array 要转 string
		$translator = join(' / ',$bookInfo['translator']);	//译者是个 array 要转 string
		$images	= $bookInfo['images']['large'];	//取出书的封面图

//拼接文本内容
// $bookInfoStr = <<<bookInfoStr
// 作者: {$author}
// 页数: {$bookInfo['pages']}
// 装帧: {$bookInfo['binding']}
// 定价: {$bookInfo['price']}
// 出版年: {$bookInfo['pubdate']}
// ISBN码: {$bookInfo['isbn13']}
// 出版社: {$bookInfo['publisher']}
// bookInfoStr;
// 
//译者只在有的时候，才显示。
$bookInfoStr = "作者: {$author}   定价: {$bookInfo['price']}  出版社: {$bookInfo['publisher']}";

		//$result = sprintf($bookInfoStr, $translator ? "\n译者: ".$translator : '');

		//创建回复用的信息（主程序中回复函数的参数）
		$content = array();
		$content[] = array(
			"Title" => "《".$bookInfo['title']."》", 
			"Description" => $bookInfoStr, 
			"PicUrl" => $images, 
			"Url" => "https://m.douban.com/book/subject/".$bookInfo['id']
			);

		//返回,回复消息内容
		return $content;

    }
}

// //----------------    test post   --------------------

// $BooksInfo = new BooksInfo();
// $output =  $BooksInfo->isbn("9787544270878"); // 9787111135104
// print_r($output) ;
// // echo $output;

