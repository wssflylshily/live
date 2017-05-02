<?php
namespace Admin\Controller;
use Admin\Model;
use Think\Controller;

/**
* 会员管理
* @author 	250
* @mail	250@250.com
*/
class UserController extends Controller {

	public function userlist(){
		if(session('adminid') && session('admintype') == 1){
				$p = $_GET['P']?$_GET['p']:1;
				$uData = M('user')->page($p.',15')->select();
				$this->assign('uData',$uData);
				$count  = M('user')->count();// 查询满足要求的总记录数
				$Page  = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
				//$Page -> setConfig('header','共%TOTAL_ROW%条');
				$Page -> setConfig('first','首页');
				$Page -> setConfig('last','共%TOTAL_PAGE%页');
				$Page -> setConfig('prev','上一页');
				$Page -> setConfig('next','下一页');
				$Page -> setConfig('link','indexpagenumb');//pagenumb 会替换成页码
				$Page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
				$show  = $Page->show();// 分页显示输出
				$this->assign('page',$show);// 赋值分页输出
				$this->display();
			}else{
				$this->redirect('Admin/index');
			}
		
	}
	
	public function wuserlist(){
		if(session('adminid') && session('admintype') == 1){
			    $p = $_GET['P']?$_GET['p']:1;
				$uData = M('wuser')->page($p.',15')->select();
				foreach($uData as $k=>$v){
					if($v['gender'] == 1){
						$uData[$k]['gender'] = '男';
					}else{
						$uData[$k]['gender'] = '女';
					}
					$uData[$k]['openid'] = substr($v['openid'],0,11);
				}
				$count  = M('wuser')->count();// 查询满足要求的总记录数
				$Page  = new \Think\Page($count,15);// 实例化分页类 传入总记录数和每页显示的记录数
				//$Page -> setConfig('header','共%TOTAL_ROW%条');
				$Page -> setConfig('first','首页');
				$Page -> setConfig('last','共%TOTAL_PAGE%页');
				$Page -> setConfig('prev','上一页');
				$Page -> setConfig('next','下一页');
				$Page -> setConfig('link','indexpagenumb');//pagenumb 会替换成页码
				$Page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
				$show  = $Page->show();// 分页显示输出
				$this->assign('page',$show);// 赋值分页输出
				$this->assign('uData',$uData);
				$this->display();
			}else{
				$this->redirect('Admin/index');
			}
	}
	
	public function useradd(){
		if(session('adminid')){
			if(!IS_POST){
				$this->display();
			}else{
				$lUser = M('user')->where('mobile='.$_POST['mobile'])->find();
				if($lUser){
					$this->error('该用户已存在');
				}else{
					$_POST['pubtime'] = time();
					$ok = M('user')->add($_POST);
					if(!$ok){
						$this->error('注册失败,请重试!');
					}else{
						$this->redirect('User/userlist');
					}
				}
			}
		}else{
			$this->redirect('Admin/index');
		}
		
		
	}

	public function useredit($userid){
		if(!session('adminid') && session('admintype') != 1){
				$this->redirect('Admin/index');
			}

		if(!IS_POST){
			$eUser = M('user')->where('userid='.$userid)->find();
			$this->assign('eUser',$eUser);
			$this->display();
		}else{
			foreach($_POST as $k=>$v){
				if($k == 'userid'){
					continue;
				}
				$data[$k] = $v;
			}
			$ok = M('user')->where('userid='.$_POST['userid'])->save($data);
			if($ok){
				$this->redirect('User/userlist');
			}else{
				$this->error('修改失败,重试!');
			}
		}
	}

	public function wuseredit(){
		if(!session('adminid') && session('admintype') != 1){
				$this->redirect('Admin/index');
			}
			
		if(!IS_POST){
			$id = I('get.id');
			$eUser = M('wuser')->where('wid='.$id)->find();
			$this->assign('eUser',$eUser);
			$this->display();
		}else{
			foreach($_POST as $k=>$v){
				if($k == 'userid'){
					continue;
				}
				$data[$k] = $v;
			}
			$ok = M('wuser')->where('wid='.$_POST['wid'])->save($data);
			if($ok){
				$this->redirect('User/wuserlist');
			}else{
				$this->error('修改失败,重试!');
			}
		}

	} 
	public function  userdel($userid){
		$dUser = M('user')->where('userid='.$userid)->find();
		if(!$dUser){
			$this->error('该用户不存在!');
		}
		$ok = M('user')->where('userid='.$userid)->delete();
		if($ok){
			$this->success('删除成功',U('User/userlist'));
		}else{
			$this->error('删除失败',U('User/userlist'));
		}
	}

	public function wuserdel(){
		$id = I('get.id');
		$dUser = M('wuser')->where('wid='.$id)->find();
		if(!$dUser){
			$this->error('该用户不存在!');
		}
		$ok = M('wuser')->where('wid='.$id)->delete();
		if($ok){
			$this->success('删除成功',U('User/wuserlist'));
		}else{
			$this->error('删除失败',U('User/wuserlist'));
		}
	}

}