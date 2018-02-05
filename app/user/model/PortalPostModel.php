<?php

namespace app\user\model;

use think\Db;
use think\Model;

class PortalPostModel extends Model
{
	//开始时间戳
	protected $autoWriteTimestamp = true;


	/**
	 * post_content 自动转化
	 * @param $value
	 * @return string
	 */
	public function getPostContentAttr($value)
	{
		return cmf_replace_content_file_url(htmlspecialchars_decode($value));
	}

	/**
	 * post_content 自动转化
	 * @param $value
	 * @return string
	 */
	public function setPostContentAttr($value)
	{
		return htmlspecialchars(cmf_replace_content_file_url(htmlspecialchars_decode($value), true));
	}

	/**
	 * published_time 自动完成
	 * @param $value
	 * @return false|int
	 */
	public function setPublishedTimeAttr($value)
	{
		return strtotime($value);
	}


	/**
	 * 根据用户id获取用户发布的文章
	 *
	 * @param $userId
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getUserArticle($userId, $limit = 150)
	{

		$postData = $this
			->where('user_id', $userId)
			->where('delete_time', 0)
			->field('id,post_title,post_status,comment_count,post_excerpt')
			->limit($limit)
			->select();

		return $postData;
	}

	/**
	 *   用户发布文章  返回文章id
	 */
	public function postArticle($postData)
	{

		$this->data($postData)->save();

		return $this->id;
	}


	/**
	 * 获取所有幻灯片组
	 * */

	public function findSlide()
	{

		$slides = Db::name("slide")->select();

		return $slides;

	}

	/**
	 * 为文章添加幻灯片
	 * */

	public function setSlide($data)
	{

		$result = Db::name("slide_item")->insert($data);

		return $result;

	}

	public function updateSlide($aid, $data)
	{

		$slides = Db::name("slide_item")->select();

		foreach ($slides as $k => $v) {

			if (!empty($v["more"])) {

				$v["more"] = json_decode($v["more"], true);

				if ($v["more"]["aid"] == $aid) {

					$slide = $v;

				}

			}

		}

		if (isset($slide)) {

			Db::name("slide_item")->where("id", $slide["id"])->update($data);

		}

	}


	/**
	 * 删除幻灯片
	 */
	public function delSlide($aid)
	{

		$slides = Db::name("slide_item")->select();

		foreach ($slides as $k => $v) {

			if (!empty($v["more"])) {

				$v["more"] = json_decode($v["more"], true);

				if ($v["more"]["aid"] == $aid) {

					$slide = $v;

				}

			}

		}

		if (isset($slide)) {

			Db::name("slide_item")->where("id", $slide["id"])->update(["status" => 0]);

		}

	}


}