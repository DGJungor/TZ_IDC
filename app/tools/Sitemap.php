<?php
/*
 * SiteMap接口类
 */

class SitemapAction extends Action
{
	private static $baseURL = ''; //URL地址
	private static $askMobileUrl = 'http://m.xxx.cn/ask/'; //问答移动版地址
	private static $askPcUrl = "http://www.xxx.cn/ask/";   //问答pc地址
	private static $askZonePcUrl = "http://www.xxx.cn/ask/jingxuan/"; //问答精选Pc链接
	private static $askZoneMobileUrl = "http://m.xxx.cn/ask/jx/"; //问答精选移动版链接

	//问答setmaps
	public function askSetMap()
	{
		header('Content-type:text/html;charset=utf-8');
//获取问题列表
		$maxid  = 0;    //索引文件最大id
		$minid  = 0;    //索引文件最小id
		$psize  = 1000; //数据库每次取数量
		$maxXml = 5000; //xml写入记录数量
		$where  = array();
//读取索引文件
		$index = APP_PATH . 'setmapxml/Index.txt';
//关联setmaps路径
		$askXml = "../siteditu/ask/ask.xml";
		if (!file_exists($index)) {
			$fp = fopen("$index", "w+");
			if (!is_writable($index)) {
				die("文件:" . $index . "不可写，请检查！");
			}
			fclose($fp);
		} else {
//index.txt文件说明 0:xml文件名称(从1开始)、1:文件最大id、2:文件最小id、3:文件当前记录数
			$fp     = file($index);
			$string = $fp[count($fp) - 1];//显示最后一行
			$arr    = explode(',', $string);
		}
//索引文件数量是否小于$maxXml
//如果为第一次运行
		if (!$arr[1]) {
			$bs       = 1;
			$filename = 0;
		} else {
			if ($arr && $arr[3] < $maxXml) {
				$filename = $arr[0];
				$psize    = $maxXml - $arr[3] > $psize ? $psize : ($maxXml - $arr[3]);
				$bs       = 0;
			} else {
				$filename = $arr[0] + 1;
				$bs       = 1;
			}
		}
		$maxid = empty($arr[1]) ? 0 : $arr[1];
		$minid = empty($arr[2]) ? 0 : $arr[2];
		echo "文件名称：" . $filename . ".xml" . "<br/ >";
		echo "最大id:" . $maxid . "<br />";
		echo "最小id:" . $minid . "<br />";
		echo "xml写入最大记录：" . $maxXml . "<br />";
		echo "数据库每次读取数量：" . $psize . "<br />";
		$list = self::$questionObj->getQuestionSetMap($where, $maxid, $psize);
		if (count($list) <= 0) {
			echo 1;
			exit;
		}
		$record   = $arr[3] + count($list); //索引文件写入记录数
		$indexArr = array('filename' => $filename, 'maxid' => $maxid, 'minid' => $minid, 'maxXml' => $record);
		$start    = '<?xml version="1.0" encoding="UTF-8" ?> ' . chr(10);
		$start    .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:mobile=\"http://www.baidu.com/schemas/sitemap-mobile/1/\">" . chr(10);
		$start    .= "</urlset>";
		foreach ($list as $k => $qinfo) {
			if ($k == 0)
				$indexArr['minid'] = $qinfo['id'];
			$qinfo['lastmod']   = substr($qinfo['lasttime'], 0, 10);
			$qinfo['mobielurl'] = self::$askMobileUrl . $qinfo['id'] . '.html'; //移动版链接
			$qinfo['pcurl']     = self::$askPcUrl . $qinfo['id'] . '-p1.html'; //pc版链接
			$xml                .= $this->askMapMobileUrl($qinfo); //移动版
			$xml                .= $this->askMapPcUrl($qinfo);     //pc版
		}
		$maxid             = end($list);
		$indexArr['maxid'] = $maxid['id'];
//更新索引文件
		if ($bs == 0) {
//更新最后一行
			$txt                  = file($index);
			$txt[count($txt) - 1] = $indexArr[filename] . ',' . $indexArr[maxid] . ',' . $indexArr['minid'] . ',' . $indexArr['maxXml'] . "\r\n";
			$str                  = join($txt);
			if (is_writable($index)) {
				if (!$handle = fopen($index, 'w')) {
					echo "不能打开文件 $index";
					exit;
					exit;
				}
				if (fwrite($handle, $str) === FALSE) {
					echo "不能写入到文件 $index";
					exit;
					exit;
				}
				echo "成功地写入文件$index";
				fclose($handle);
			} else {
				echo "文件 $index 不可写";
				exit;
			}
			fclose($index);
		} elseif ($bs == 1) {
//新加入一行
			$fp     = fopen($index, 'a');
			$num    = count($list);
			$string = $indexArr[filename] . ',' . $indexArr[maxid] . ',' . $indexArr['minid'] . ',' . $num . "\r\n";
			if (fwrite($fp, $string) === false) {
				echo "追加新行失败。。。";
				exit;
			} else {
				echo "追加成功<br />";
//更新sitemap索引文件
				$xmlData = "<?xml version=\"1.0\"  encoding=\"UTF-8\" ?>" . chr(10);
				$xmlData .= "<sitemapindex>" . chr(10);
				$xmlData .= "</sitemapindex>";
				if (!file_exists($askXml))
					file_put_contents($askXml, $xmlData);
				$fileList                 = file($askXml);
				$fileCount                = count($fileList);
				$setmapxml                = "http://www.xxx.cn/ask/setmapxml/{$filename}.xml";//正常问题链接
				$txt                      = $this->setMapIndex($setmapxml);
				$fileList[$fileCount - 1] = $txt . "</sitemapindex>";
				$newContent               = '';
				foreach ($fileList as $v) {
					$newContent .= $v;
				}
				if (!file_put_contents($askXml, $newContent)) exit('无法写入数据');
				echo '已经写入文档' . $askXml;
			}
			fclose($fp);
		}
		$filename = APP_PATH . 'setmapxml/' . $filename . '.xml';
		//更新到xml文件中,增加结尾
		if (!file_exists($filename))
			file_put_contents($filename, $start);
		$xmlList                = file($filename);
		$xmlCount               = count($fileList);
		$xmlList[$xmlCount - 1] = $xml . "</urlset>";
		$newXml                 = '';
		foreach ($xmlList as $v) {
			$newXml .= $v;
		}
		if (!file_put_contents($filename, $newXml)) exit("写入数据错误");
		else
			echo "写入数据成功<br />";
	}

//问答移动版xml
	private function askMapMobileUrl($data)
	{
		$xml = '';
		if (is_array($data) && !empty($data)) {
			$xml .= "<url>" . chr(10);
			if ($data['id'])
				$xml .= '<loc>' . $data['mobielurl'] . '</loc>' . chr(10);//移动版链接
			$xml .= "<mobile:mobile type=\"mobile\"/>" . chr(10);
			if ($data['lastmod'])
				$xml .= '<lastmod>' . $data['lastmod'] . '</lastmod>' . chr(10);
			$xml .= '<changefreq>daily</changefreq>' . chr(10);
			$xml .= '<priority>0.8</priority>' . chr(10);
			$xml .= "</url>" . chr(10);
			return $xml;
		}
	}

//问答pc版xml
	private function askMapPcUrl($data)
	{
		$xml = '';
		if (is_array($data) && !empty($data)) {
			$xml .= '<url>' . chr(10);
			if ($data['id'])
				$xml .= '<loc>' . $data['pcurl'] . '</loc>' . chr(10);//pc版链接
			if ($data['lastmod'])
				$xml .= '<lastmod>' . $data['lastmod'] . '</lastmod>' . chr(10);
			$xml .= '<changefreq>daily</changefreq>' . chr(10);
			$xml .= '<priority>0.8</priority>' . chr(10);
			$xml .= '</url>' . chr(10);
			return $xml;
		}
	}

//setmaps索引文件
	private function setMapIndex($filename)
	{
		$xml = '';
		$xml .= "<sitemap>" . chr(10);
		$xml .= "<loc>{$filename}</loc>" . chr(10);
		$xml .= "<lastmod>" . date("Y-m-d", time()) . "</lastmod>" . chr(10);
		$xml .= "</sitemap>" . chr(10);
		return $xml;
	}
}

?>