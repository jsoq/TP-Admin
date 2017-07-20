<?php
// +----------------------------------------------------------------------
// | TP-Admin [ 多功能后台管理系统 ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2016 http://www.hhailuo.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 逍遥·李志亮 <xiaoyao.working@gmail.com>
// +----------------------------------------------------------------------

namespace Model;
use Think\Model;
use Think\Page as Page;

class BaseModel extends Model {
	protected $tableName = '';

	protected static $_instances = [];

	public static function getInstance() {
		$class = get_called_class();
		if (!isset(self::$_instances[$class])) {
			self::$_instances[$class] = new $class();
		}
		return self::$_instances[$class];
	}

	/**
	 * 获取内容
	 * @param array  $where       查询条件
	 * @param string $order       排序条件
	 * @param int    $limit
	 * @param mix    $field
	 * @param array  $page_params 分页格式 具体格式参照Page类
	 */
	public function getList($where = [], $order = '', $limit = 20, $field = true, $page_params = []) {
		$where           = empty($where) ? [] : $where;
		$where['siteid'] = get_siteid();
		$order           = empty($order) ? 'id desc' : $order;
		$page            = isset($_GET['p']) ? intval($_GET['p']) : 1;
		$data            = $this->field($field)->where($where)->order($order)->page($page . ', ' . $limit)->select();
		$count           = $this->where($where)->count();
		$page_obj        = new Page($count, $limit);
		if (!empty($page_params) && is_array($page_params)) {
			foreach ($page_params as $key => $param) {
				$page_obj->setConfig($key, $param);
			}
		}
		$pages = $page_obj->show();
		return ["data" => $data, "page" => $pages];
	}

	/**
	 * 获取字段信息 (不支持动态设置表)
	 * @return array
	 */
	public function getFields() {
		return $this->fields;
	}

	protected function _before_insert(&$data, $options) {
		$fields = $this->fields;
		if (in_array('inputtime', $fields) && (!isset($data['inputtime']) || empty($data['inputtime']))) {
			$data['inputtime'] = strpos($fields['_type']['inputtime'], 'int') === false ? date("Y-m-d H:i:s") : time();
		}
		if (in_array('updatetime', $fields) && (!isset($data['updatetime']) || empty($data['updatetime']))) {
			$data['updatetime'] = strpos($fields['_type']['updatetime'], 'int') === false ? date("Y-m-d H:i:s") : time();
		}
	}

	protected function _before_update(&$data, $options) {
		$fields = $this->fields;
		if (in_array('updatetime', $fields) && (!isset($data['updatetime']) || empty($data['updatetime']))) {
			$data['updatetime'] = strpos($fields['_type']['updatetime'], 'int') === false ? date("Y-m-d H:i:s") : time();
		}
	}
}