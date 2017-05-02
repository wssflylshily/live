<?php
/**
* 日志记录模型 
* @author 250
* @mail	250@250.com
* @date 2016/07/25
*/
namespace Admin\Model;
use Think\Model;
class LogsModel extends Model {

	/**
	 * 管理员操作日志
	 * @param array $data 数组
	 * @return  bool   
	 */
	public function save_log($data){
		$insert_id = $this->add($data);
		return $insert_id;
	}	
}

?>