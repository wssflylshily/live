<?php
/**
* 用户登录模型 
* @author 250
* @mail	250@250.com
*/
namespace Admin\Model;
use Think\Model;

/**
* 管理员模型
* @author 	250
* @mail	250@250.com
*/
class AdminModel extends Model {

	/**
	 * 管理员登录判断
	 * @param string $user 用户名
	 * @param string $pwd  密码
	 * @return  array   
	 */
	public function login($user,$pwd){
		$where = array();
		$where['name'] = $user;
		$where['password'] = $pwd;
		$where['_logic'] = 'and';
		//print_r($where);exit;
		$result = $this->where($where)->find();
		if(!empty($result)){
			$result['status'] = 1;
			return $result;
		}else{
			$result['status'] = 0;
			return $result;
		}

	}	
}

?>