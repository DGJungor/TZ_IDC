<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\model;

use think\Db;
use think\Model;

class PortalCategoryModel extends Model
{

	/**
	 * 根据栏目名字获取栏目信息
	 *
	 * @author 张俊
	 * @param string $categoryName
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 *
	 */
	public function getCategoryByName($categoryName = '')
	{

		$categoryData = $this
			->where('name', $categoryName)
			->find();

		return $categoryData;
	}


	/**
	 * 根据父id获取栏目信息
	 *
	 * @author 张俊
	 * @param $parentId
	 * @return false|\PDOStatement|string|\think\Collection
	 * @throws \think\db\exception\DataNotFoundException
	 * @throws \think\db\exception\ModelNotFoundException
	 * @throws \think\exception\DbException
	 */
	public function getCategoryByParentId($parentId)
	{
		$categoryData = $this
			->where('parent_id', $parentId)
			->where('status', 1)
			->select();

		return $categoryData;
	}

}