<?php
namespace Admin\Controller;
use Think\Controller;
class CommonController extends Controller{
	public function _initialize(){
		if(session('adminuser') == ''||session('adminid') == ''){
			//exit('3');
			$this->redirect('Admin/index');
		}else{
			$user = M('admin');
			$this->userdata = $user ->where("admin_id='".session('adminid')."'")->find();
		}
	
	}
	
}
?>