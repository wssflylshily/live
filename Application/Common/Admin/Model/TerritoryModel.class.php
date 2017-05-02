<?php
/**
* 设计领域模型 
* @author 250
* @mail	250@250.com
* @date 2016/07/23
*/
namespace Admin\Model;
use Think\Model;

class TerritoryModel extends Model {

	/**
	 * 添加设计领域
	 * @param string $name   设计领域名称
	 * @param int $sort  排序
	 * @return  bool  
	 */
	public function add_save($name,$sort){
		$data = array();
		$data['territory_name'] = $name;
		$data['sort'] = $sort;
		$data['is_del'] = 0;
		$data['createtime'] = time();
		$insert_id = $this->add($data);
		return $insert_id;
	}
	/**
	 * 获取所属的分类id 获取领域
	 * @param int $cid 所属分类id
	 * @return  array
	 */
	public function get_cid_territory($cid=0){
		$where = array();
		$where['c_id'] = $cid;
		$res = $this->where($where)->select();
		return $res;
	}
	/**
	 * 根据领域id获取领域
	 * @param int $id 领域主键id
	 * @return  array
	 */
	public function get_territory($id=0){
		$where = array();
		$where['id'] = $id;
		$res = $this->where($where)->find();
		return $res;
	}

}

?>