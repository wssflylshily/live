<?php
/**
* 用户登录模型 
* @author 250
* @mail	250@250.com
* @date 2016/07/22
*/
namespace Admin\Model;
use Think\Model;

/**
* 分类模型
* @author 	250
* @mail	250@250.com
* @date 	2016/07/22
*/
class CategoryModel extends Model {

	/**
	 * 添加分类
	 * @param int $pid 父类id
	 * @param string $name   分类名称
	 * @param string $parent  父类名称
	 * @param int $sort  排序
	 * @return  bool  
	 */
	public function add_save($pid,$name,$parent,$sort){
		$data = array();
		$data['cate_name'] = $name;
		$data['p_id'] = $pid;
		$data['parent_name'] = $parent;
		$data['sort'] = $sort;
		$data['is_del'] = 0;
		$data['createtime'] = time();
		$insert_id = $this->add($data);
		return $insert_id;
	}
	/**
	 * 根据父类id获取子分类
	 * @param int $pid 父类id
	 * @return  array
	 */
	public function get_category($pid=0){
		$where = array();
		$where['p_id'] = $pid;
		$res = $this->where($where)->select();
		return $res;
	}
	/**
	 * 根据分类id获取分类
	 * @param int $id 分类主键id
	 * @return  array
	 */
	public function get_one($id=0){
		$where = array();
		$where['c_id'] = $id;
		$res = $this->where($where)->find();
		return $res;
	}


}

?>