<?php
/**
* 风格管理模型 
* @author 250
* @mail	250@250.com
* @date 2016/07/23
*/
namespace Admin\Model;
use Think\Model;

class StyleModel extends Model {

	/**
	 * 添加风格管理
	 * @param string $name   风格管理名称
	 * @param int $sort  排序
	 * @return  bool  
	 */
	public function add_save($name,$sort){
		$data = array();
		$data['style_name'] = $name;
		$data['sort'] = $sort;
		$data['is_del'] = 0;
		$data['createtime'] = time();
		$insert_id = $this->add($data);
		return $insert_id;
	}
	/**
	 * 根据所属的分类id获取风格 
	 * @param int $cid 设计父类id
	 * @return  array
	 */
	public function get_style_category($cid=0){
		$where = array();
		$where['c_id'] = $cid;
		$res = $this->where($where)->select();
		return $res;
	}
	/**
	 * 根据风格id获取风格
	 * @param int $id 风格主键id
	 * @return  array
	 */
	public function get_style($id=0){
		$where = array();
		$where['id'] = $id;
		$res = $this->where($where)->find();
		return $res;
	}	
}

?>