<?php
namespace Admin\Controller;
use Admin\Model;
use Think\Controller;
use Admin\Tool\AliyunTool;

/**
* 管理员控制器
* @author 	250
* @mail	250@250.com
*/
class AdminController extends Controller {
	

	public function index(){

		if(session('adminid')){
			if(session('admintype') == 1){
				$this->redirect('Admin/glylb');
			}else{
				$this->redirect('Admin/zblb');
			}
		}
		$this->display();
	}
	
	/**
	 * 管理员登录
	 */
	public function login(){
		
		if(IS_POST){
			$name  =  I('post.user','strip_tags');
			$passwd  = md5(I('post.pwd','strip_tags'));
			$model  = D('Admin');
			$rs = $model->login($name,$passwd);
			if($rs['status']){
				session('adminid',$rs['admin_id']);
				session('adminuser',$name);
				session('admintype',$rs['type']);
				if($rs['type'] == 1){
					$this->success('登录成功',U('Index/index'),0);
				}else{
					$this->success('登录成功',U('Admin/zblb'),0);
				}
				
			}else{
				$this->error('登录失败,用户名或密码不正确',U('Admin/index'),3);
			}
		}else{
			if(session('adminid')){
				if(session('admintype') == 1){
				$this->redirect('Admin/glylb');
			}else{
				$this->redirect('Admin/zblb');
			}

			}else{
				$this->redirect('Admin/index');
			}
			
		}
	}

	//添加管理员
	public function admiadd(){

   		if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		if(session('admintype') == 1){
			$this->display('add_user');
		}else{
			$this->redirect('admin/zblb');
		}

	}

	//添加管理员
	public function save_user(){

       if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		//print_r($_POST);
		$name  =  I('post.user','strip_tags');
		$passwd  = I('post.pwd','strip_tags');
		$rpasswd = I('post.qpwd','strip_tags');

		if($passwd!=$rpasswd){
			$this->error('密码和确认密码不一致',U('Admin/glylb'));
		}
		$where['name'] = $name;
		$arr = M('admin')->where($where)->find();

		if(!empty($arr)){
			$this->error('用户名已经存在',U('Admin/glylb'));
		}


		$data['name'] = $name;
		$data['password'] = md5($passwd);
		$data['time'] = time();
		$data['login_time'] = time();
		$data['type'] = I('post.type');
		$data['ip'] = get_client_ip();
		$id = M('admin')->add($data);

		if($id){
			session('adminid',$id);
			session('adminuser',$name);
			$this->success('新增成功',U('Admin/glylb'));
		}else{
			$this->error('新增失败',U('Admin/glylb'));
		}

	}

	//管理员列表
	public function glylb(){
        if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		if(session('admintype') == 1){
			$arr = M('admin')->select();
			$this->assign('res',$arr);
			//print_r($arr);exit;
			$this->display();
		}else{
			$this->redirect('admin/zblb');
		}
		
	}

	//修改管理员
	public function edit_gly(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		if(IS_POST){

			//print_r($_POST);exit;
			if(!intval($_POST['dd'])){
				$this->error('数据异常',U('Admin/glylb'));
			}

			if(strlen($_POST['pwd'])<6){
				$this->error('密码不能小于6位',U('Admin/glylb'));
			}

			if($_POST['pwd']!=$_POST['qpwd']){
				$this->error('数据异常',U('Admin/glylb'));
			}
	
			//Array ( [user] => admin [pwd] => 1111111 [qpwd] => 1111111 [dd] => 1 )
			$name  =  I('post.user','strip_tags');
			$passwd  = I('post.pwd','strip_tags');
			$rpasswd = I('post.qpwd','strip_tags');
			$arr = M('admin')->where("admin_id=".intval($_POST['dd']))->find();
			if(!empty($arr)){
		
					$data['password'] = md5($passwd);
					$data['name'] = $name;
					$ok = M('admin')->where("admin_id=".intval($_POST['dd']))->save($data);
					if($ok!==false){
						
						//exit('3');
						$this->success('修改成功',U('Admin/glylb'));
					}else{
						//exit('4');
						$this->error('修改失败',U('Admin/glylb'));
					}
			}

		}else{
			if(session('admintype') == 1){
			$where['admin_id'] = $_GET['id']+0;
			$arr = M('admin')->where($where)->find();
			$this->assign('res',$arr);
			$this->display();
		}else{
			$this->redirect('admin/zblb');
		}
	}
	}

	//会员列表
	public function hylb(){
		exit('3');
	}

	    //后台随便写个注册
	    public function reg(){


        		if(!session('adminid')){
			$this->redirect('Admin/index');
		}

	            $this->display();

	    }

	/**
	 * 退出登录
	 */
	public function loginout(){
		header("Content-type:text/html;charset=utf-8");
		session('adminuser',null);
		session('adminid',null);
		$this->success('退出成功',U('Admin/index'));
	}

	//管理员删除
	public function gly_del(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
			header("Content-type:text/html;charset=utf-8");
			$where['admin_id'] = $_GET['id']+0;
			$ok = M('admin')->where($where)->delete();
			if($ok){  
				$this->success('删除成功',U('Admin/glylb'));
			}else{
				$this->error('删除失败',U('Admin/glylb'));
			}
		}else{
			$this->redirect('admin/zblb');
		}
		
	}

	//区域列表
	public function qylb(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
			$arr = M('qy')->select();
			$this->assign('arr',$arr);
			header("Content-type:text/html;charset=utf-8");
			$this->display();
		}else{
			$this->redirect('admin/zblb');
		}
		

	}

	//添加区域
	public function add_quyu(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		if(session('admintype') == 1){
			header("Content-type:text/html;charset=utf-8");	
			$this->display();
		}else{
			$this->redirect('admin/zblb');
		}
		// $city = M('city')->where("pid=1")->select();
		// $this->assign('city',$city);

	}

	//保存区域
	public function  save_quyu(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		//Array ( [name] => 2 [sort] => 0 [parent] => )
		//print_r($_POST);
		header("Content-type:text/html;charset=utf-8");
		if(intval($_POST['sort'])>255){
			$_POST['sort'] = 255;
		}
		if(intval($_POST['sort'])<0){
			$_POST['sort'] = 0;
		}

		$data['qy_sort'] = $_POST['sort'];
		$data['qy_name']  =  I('post.name','strip_tags');
		
		$arr = M('qy')->where("qy_name="."'".$data['qy_name']."'")->find();

		if(!empty($arr)){
			$this->error('该区域已存在',U('Admin/qylb'));

		}

		$ok =  M('qy')->add($data);
		if($ok){                                                      
			$this->success('添加成功',U('Admin/qylb'));
		}else{
			$this->error('添加失败',U('Admin/qylb'));
		}

	}


	//区域删除
	public function qy_del(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
			header("Content-type:text/html;charset=utf-8");
			$where['qy_id'] = $_GET['id']+0;
			$ok = M('qy')->where($where)->delete();
			if($ok){                                                        
				$this->success('删除成功',U('Admin/qylb'));
			}else{
				$this->error('删除失败',U('Admin/qylb'));
			}
		}else{
			$this->redirect('admin/zblb');
		}
		
	}

	//修改区域
	public function edit_qy(){
		header("Content-type:text/html;charset=utf-8");

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(IS_POST){

			if(!intval($_POST['dd'])){
				$this->error('数据异常',U('Admin/index'));
			}

			$data['qy_name']  = I('post.name','strip_tags');
			$data['qy_sort'] = I('post.sort','strip_tags');
			$ok = M('qy')->where('qy_id='.intval($_POST['dd']))->save($data);
			if($ok===false){
				$this->error('修改失败',U('Admin/qylb'));
			}else{
				$this->success('修改成功',U('Admin/qylb'));
			}

		}else{
			if(session('admintype') == 1){
				$id  = intval($_GET['id']);
				$arr = M('qy')->where('qy_id='.$id)->find();
				if(empty($arr)){
					$this->error('数据异常',U('Admin/qylb'));
				}
				$this->assign('arr',$arr);
				$this->display();
			}else{
				$this->redirect('admin/zblb');
		}
		}
	}

	//直播列表
	public function zblb(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		header("Content-type:text/html;charset=utf-8");
		if(session('admintype') == 0){
			$where['a_id'] = session('adminid');
		}else{
			$where = 1;
		}
		if(!$_GET['p']){
			$_GET['p'] = 1;
		}
		$arr = M('video')->where($where)->page($_GET['p'].',15')->select();
		$this->assign('arr',$arr);

		$count  = M('video')->field($field)->where($where)->count();// 查询满足要求的总记录数
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
	}

	// 添加直播应用
	public function add_app(){
		if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
				$this->display();
			}else{
				$this->redirect('admin/zblb');
		}
	}

	// 保存直播应用
	public function save_app(){
		$apps = M('app');
		$data['appname'] = I('post.appname');
		$num = $apps->where(['appname'=>$data['appname']])->count();
		if($num != 0){
			$this->error('应用名不能重复,请重新填写!!');
		}
		$data['pubtime'] = time();
		$ok = $apps->add($data);
		if($ok){
			$this->redirect('Admin/applb');
		}else{
			$this->error('数据异常');
		}
	}

	// 直播应用列表
	public function applb(){
		if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
				$apps = M('app');
				$_GET['p'] = $_GET['p']?$_GET['p']:1;
				$data = $apps->page($_GET['p'].',10')->select();
				foreach($data as $k=>$v){
					$data[$k]['pubtime'] = date('Y-m-d H:i:s',$v['pubtime']);
				}
				$count  = M('app')->count();// 查询满足要求的总记录数
				$Page  = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
				//$Page -> setConfig('header','共%TOTAL_ROW%条');
				$Page -> setConfig('first','首页');
				$Page -> setConfig('last','共%TOTAL_PAGE%页');
				$Page -> setConfig('prev','上一页');
				$Page -> setConfig('next','下一页');
				$Page -> setConfig('link','indexpagenumb');//pagenumb 会替换成页码
				$Page -> setConfig('theme','%HEADER% %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
				$show  = $Page->show();// 分页显示输出
				$this->assign('page',$show);// 赋值分页输出
				$this->assign('data',$data);
				$this->display();
			}else{
				$this->redirect('admin/zblb');
		}
		
	}

	// 直播应用编辑
	public function edit_app(){
		if(!IS_POST){
			$aid = I('get.id');
			$num = M('app')->where(['appid'=>$aid])->getField('number');
			$appname = M('app')->where(['appid'=>$aid])->getField('appname');
			$this->assign('appid',$aid);
			$this->assign('appname',$appname);
			$this->display();
			exit;
		}else{
			$aid = I('post.appid');
			$data['appname'] = I('post.appname');
			$data['pubtime'] =time();
			$ok = M('app')->where(['appid'=>$aid])->save($data);
			if($ok){
				$this->redirect('admin/applb');
			}else{
				$this->error('修改失败!!!');
			}
		}
	}
	
	// 直播应用删除
	public function app_del(){
		$aid = I('get.id');
		$num = M('app')->where(['appid'=>$aid])->getField('number');
		$ok = M('app')->where(['appid'=>$aid])->delete();
		if($ok){
			$this->redirect('admin/applb');
		}else{
			$this->error('删除失败!!');
		}
	}

	// 直播应用禁用
	public function appact(){
		$id = I('get.id');
		$actid = I('get.act');
		if($actid == 0){
			$ok = M('app')->where(['appid'=>$id])->save(['active'=>1]);
		}else{
			$ok = M('app')->where(['appid'=>$id])->save(['active'=>0]);
		}
		if($ok){
			$this->redirect('admin/applb');
		}else{
			$this->error('操作失败,请重新尝试!!');
		}
	}

	//添加直播
	public function add_zb(){

		//echo date('Y-m-d H:i:s','1457102002');exit;
        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		echo 'hello';
		echo session('adminid');exit;
		$arr = M('qy')->select();
		$this->assign('arr',$arr);
		$member = M('admin')->select();
		$this->assign('member',$member);
		$apps = M('app')->where(['active'=>1])->select();
		$this->assign('apps',$apps);

		//添加主持人
		$zcr = M('zcr')->select();
		$this->assign('zcr',$zcr);
		$this->display();
	}


	//修改直播
	public function edit_zb(){


        if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		header("Content-type:text/html;charset=utf-8");
		$id = intval($_GET['id']);

		$arr = M('video')->where("v_id=".intval($_GET['id']))->find();
		$video_file = $arr['video_file'];


		$arr['start_time'] = date('Y/m/d/H/i/s',$arr['start_time']);
		$this->assign('id',$id);

		$arr1 = M('qy')->select();
		$this->assign('arr1',$arr1);

		$arr2 = M('admin')->select();
		$this->assign('arr2',$arr2);

		$zcr = M('zcr')->select();
		$this->assign('zcr',$zcr);

		$this->assign('arr',$arr);
		$this->display();

	}

 	



	//婚礼仪式餐参考
	public function cankao(){
        		if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		header("Content-type:text/html;charset=utf-8");
		$this->display();

	}

	//添加直播
	public function save_zb(){
        if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		header("Content-type:text/html;charset=utf-8");

		$fuck = getimagesize($_FILES['zb_img']['tmp_name']);
		if(($fuck[0]!='260')||($fuck[1]!='180')){
			$this->error('直播展示图片 必须宽260高180像素');
		}
		
		if($_POST['sort']>99){
			$_POST['sort'] = 99;
		}elseif($_POST['sort']<0){
			$_POST['sort'] =0;
		}

		foreach ($_POST as $key => $value) {
			$data[$key] = $value;
		}
		$video_file = $data['video_file'];
		$sql = "select * from hl_video where video_file='".$video_file."'";
		//echo $sql;
		$vnum = M('')->query($sql);
		//print_r($vnum);exit;
		if($vnum){
			$this->error('直播文件名不能重复!!!请重新输入!!');
		}
		//2016/03/04/22/33/22
		$time_arr = explode('/',$data['start_time']); 
		$time = $time_arr[0].'-'.$time_arr[1].'-'.$time_arr[2].' '.$time_arr[3].':'.$time_arr[4].':'.$time_arr[5];
		$start_time =  strtotime($time);

		$data['start_time'] = $start_time;
		$data['a_id'] = session('adminid');
		$data['url'] = C('DomainName').$data['video_type'].'/'.$data['video_file'].'.m3u8';

		//图片上传
		
		$year = date('Y',time());
		$month = date('m',time());
		$day = date('d',time());
		$config = array(
				'maxSize'    =>    1*1024*1024,
				'rootPath'   =>     './Public/',
				'savePath'   =>    '../uploads/',
				'exts'       =>    array('jpg', 'png', 'jpeg'),
				'autoSub'    =>    true,
				'subName'    =>    array('date','Y/m/d'),
		);
		//print_r($_FILES);
		$upload = new \Think\Upload($config);	// 实例化上传类
		$info = $upload->upload();
		// print_r($info);exit;
		if(!$info) {  	
			echo $upload->getError();
			exit;	
			//$this->error($upload->getError());
		    }else{  	 
		    		//print_r($file);exit;
		    		$name=substr($info['zb_img']['savepath'],2).$info['zb_img']['savename'];
		    		$data['zb_img'] = $name;
		    	}    	
		$ok = M('video')->add($data);
		if(!$ok){
			$this->error('添加失败',U('Admin/add_zb'));
		}else{
			$this->success('添加成功',U('Admin/zblb'));
		}
	}

	//删除直播
	public function zb_del(){
        		if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		header("Content-type:text/html;charset=utf-8");
		$id = $_GET['id']+0;

		$ok = M('video')->where('v_id='.$id)->delete();
		if(!$ok){
			$this->error('删除失败',U('Admin/zblb'));
		}else{
			$this->success('删除成功',U('Admin/zblb'));
		}

	}

	//保存修改直播
	public function bczb(){


		header("Content-type:text/html;charset=utf-8");
        		if(!session('adminid')){
			$this->redirect('Admin/index');
		}



		if($_POST['sort']>99){
			$_POST['sort'] = 99;
		}elseif($_POST['sort']<0){
			$_POST['sort'] =0;
		}

		foreach ($_POST as $key => $value) {
			if($key == 'v_id'){
				continue;
			}
			$data[$key] = $value;
		}
		

		$video_file = $data['video_file'];
		//
		$where['v_id'] = array('neq',I('post.v_id'));
		$where['video_file'] = array('eq',$video_file);
		//echo $sql;
		$vnum = M('video')->where($where)->count();

		//print_r($vnum);exit;
		if($vnum){
			$this->error('直播文件名不能重复!!!请重新输入!!');
			exit;
		}

		//2016/03/04/22/33/22
		$time_arr = explode('/',$data['start_time']); 
		$time = $time_arr[0].'-'.$time_arr[1].'-'.$time_arr[2].' '.$time_arr[3].':'.$time_arr[4].':'.$time_arr[5];
		$start_time =  strtotime($time);

		$data['start_time'] = $start_time;
		$data['a_id'] = session('adminid');

		$data['url'] = C('DomainName').$data['video_type'].'/'.$data['video_file'].'.m3u8';

		//图片上传
		
		$year = date('Y',time());
		$month = date('m',time());
		$day = date('d',time());
		$config = array(
				'maxSize'    =>    1*1024*1024,
				'rootPath'   =>     './Public/',
				'savePath'   =>    '../uploads/',
				'exts'       =>    array('jpg', 'png', 'jpeg'),
				'autoSub'    =>    true,
				'subName'    =>    array('date','Y/m/d'),
		);
		if($_FILES['zb_img']['name']){
			$fuck = getimagesize($_FILES['zb_img']['tmp_name']);
			
			if(($fuck[0]!='260')||($fuck[1]!='180')){
			$this->error('直播展示图片 必须宽260高180像素');exit;
			}

			$upload = new \Think\Upload($config);	// 实例化上传类
			$info = $upload->upload();

		// print_r($info);exit;
		if(!$info) {  	
			echo $upload->getError();
			exit;	
			//$this->error($upload->getError());
		    }else{  	 
		    		//print_r($file);exit;
		    		$name=substr($info['zb_img']['savepath'],2).$info['zb_img']['savename'];
		    		$data['zb_img'] = $name;
		    	}    
		
		}
		//print_r($_FILES);
	
		$ok = M('video')->where(['v_id'=>I('post.v_id')])->save($data);

		if(!$ok){
			$this->error('修改失败',U('Admin/edit_zb',['id'=>I('post.v_id')]));
		}else{
			$this->success('修改成功',U('Admin/zblb'));
			
		}
	}

	//添加主持人
	public function  add_zcr(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
				header("Content-type:text/html;charset=utf-8");
				$this->display();	
			}else{
				$this->redirect('admin/zblb');
		}
	}

	//保存主持人
	public function save_zcr(){

        		if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		header("Content-type:text/html;charset=utf-8");
		$data['zcr_name'] = I('post.name','strip_tags');
		$data['zcr_age'] =   I('post.age','strip_tags');
		$data['zcr_height'] =   I('post.height','strip_tags');
		$data['zcr_num'] =   I('post.num','strip_tags');
		$data['zcr_zy'] =   I('post.zy','strip_tags');
		$data['info'] =   I('post.info','strip_tags');
		//print_r($data);exit;

		$year = date('Y',time());
		$month = date('m',time());
		$day = date('d',time());
		$config = array(
				'maxSize'    =>    1*1024*1024,
				'rootPath'   =>     './Public/',
				'savePath'   =>    '../uploads/',
				'exts'       =>    array('jpg', 'png', 'jpeg'),
				'autoSub'    =>    true,
				'subName'    =>    array('date','Y/m/d'),
		);
		$upload = new \Think\Upload($config);	// 实例化上传类
		$info = $upload->upload();
		if(!$info) {	

			echo $upload->getError();
			exit;	
			//$this->error($upload->getError());
		    }else{  	   

	

		    	foreach($info as $file){
		    		//print_r($file);exit;
		    		$name=substr($file['savepath'],2).$file['savename'];
		    		$data['zcr_img'] = $name;
		    	}    

		    	$ok = M('zcr')->add($data);
		    	if($ok){
				$this->success('添加成功',U('Admin/zcrlb'));
		    	}else{
				$this->error('添加失败');
		    	}


		}

	}

	//主持人列表
	public function zcrlb(){


        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
				header("Content-type:text/html;charset=utf-8");
				$model  = M('zcr');
				if(!isset($_GET['p'])){
					$_GET['p'] = 1;
				}
				$field = "*";
				$list = $model->field($field)->page($_GET['p'].',15')->select();
				$this->assign('res',$list);// 赋值数据集
				$count  = $model->field($field)->count();// 查询满足要求的总记录数
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
				$this->redirect('admin/zblb');
		}
		
	}

	//主持人删除
	public function zcr_del(){
        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
				//print_r($_GET);exit;
				$id = $_GET['id']+0;
				$ok = M('zcr')->where('zcr_id='.$id)->delete();

				if(!$ok){
					$this->error('删除失败',U('Admin/zcrlb'));
				}else{
					$this->success('删除成功',U('Admin/zcrlb'));
				}
	
			}else{
				$this->redirect('admin/zblb');
		}
		


	}

	//主持人修改页面
	public function edit_zcr(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
				header("Content-type:text/html;charset=utf-8");
				$id = $_GET['id']+0;
				$arr = M('zcr')->where('zcr_id='.$id)->find();
				$this->assign('arr',$arr);
				$this->display();
			}else{
				$this->redirect('admin/zblb');
		}
		
	}

	//主持人修改保存
	public function bczcr(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		header("Content-type:text/html;charset=utf-8");
		$data['zcr_name'] = I('post.name','strip_tags');
		$data['info'] =   I('post.info','strip_tags');
		$data['zcr_age'] =   I('post.age','strip_tags');
		$data['zcr_height'] =   I('post.height','strip_tags');
		$data['zcr_num'] =   I('post.num','strip_tags');
		$data['zcr_zy'] =   I('post.zy','strip_tags');
		$id = $_POST['dd']+0;
		$year = date('Y',time());
		$month = date('m',time());
		$day = date('d',time());
		$config = array(
				'maxSize'    =>    1*1024*1024,
				'rootPath'   =>     './Public/',
				'savePath'   =>    '../uploads/',
				'exts'       =>    array('jpg', 'png', 'jpeg'),
				'autoSub'    =>    true,
				'subName'    =>    array('date','Y/m/d'),
		);


		if($_FILES['zcr_img']['name']){

			$upload = new \Think\Upload($config);	// 实例化上传类
			$info = $upload->upload();
			if(!$info) {	

				echo $upload->getError();
				exit;	
				//$this->error($upload->getError());
			    }else{  	   

			    	foreach($info as $file){
			    		//print_r($file);exit;
			    		$name=substr($file['savepath'],2).$file['savename'];
			    		$data['zcr_img'] = $name;
			    	}    
				}

		}

			$ok = M('zcr')->where('zcr_id='.$id)->save($data);
			if($ok){
					$this->success('修改成功',U('Admin/zcrlb'));
			  }else{
					$this->error('修改失败',U('Admin/add_zcr'));
			 }
		
	}

	//添加精选视频
	public function add_jxsp(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(IS_POST){

			if($_POST['sort']>99){
				$_POST['sort'] = 99;
			}elseif($_POST['sort']<0){
				$_POST['sort'] =0;
			}
			foreach ($_POST as $key => $value) {
				
				$data[$key] = $value;
			}
			$vname = M('video')->where(['v_id'=>$data['v_id']])->field(['video_type','video_file','c_id'])->find();
			$data['video_file'] = $vname['video_file'];
			$data['video_type'] = $vname['video_type'];
			$data['c_id'] = $vname['c_id'];
			$data['order'] = 0;
			$data['type'] = 0;
			$time_arr = explode('/',$data['start_time']); 
			$time = $time_arr[0].'-'.$time_arr[1].'-'.$time_arr[2].' '.$time_arr[3].':'.$time_arr[4].':'.$time_arr[5];
			$start_time =  strtotime($time);
			$data['start_time'] = $start_time;

			$year = date('Y',time());
			$month = date('m',time());
			$day = date('d',time());
			$config = array(
					'maxSize'    =>    1*1024*1024,
					'rootPath'   =>     './Public/',
					'savePath'   =>    '../uploads/',
					'exts'       =>    array('jpg', 'png', 'jpeg'),
					'autoSub'    =>    true,
					'subName'    =>    array('date','Y/m/d'),
			);
			$upload = new \Think\Upload($config);	// 实例化上传类
			$info = $upload->upload();
			if(!$info) {	
				echo $upload->getError();
				exit;	
				//$this->error($upload->getError());
			    }else{  
			    
			    //print_r($file);exit;
			    	$name=substr($info['jx_img']['savepath'],2).$info['jx_img']['savename'];
			    	$data['jx_img'] = $name;  
			    	$ok = M('jx_video')->add($data);
			    	if($ok){
						$this->success('添加成功',U('Admin/jxsplb'));
			    	}else{
						$this->error('添加失败',U('Admin/add_jxsp'));
			    }
			}
		}else{
			if(session('admintype') == 1){
				$where = 1;
			}else{
				$where['a_id'] = session('adminid');
			}
			$video = M('video')->where($where)->select();
			$member = M('admin')->select();
			$this->assign('member',$member);
			$this->assign('videos',$video);
			$this->display();

		}




	}

	//精选视频列表
	public function jxsplb(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 0){
			$where['a_id'] = session('adminid');
		}
		header("Content-type:text/html;charset=utf-8");
		$model  = M('jx_video');
		if(!isset($_GET['p'])){
			$_GET['p'] = 1;
		}
		$where['type'] = 1;

		$field = "*";
		$list = $model->field($field)->where($where)->page($_GET['p'].',15')->select();
		$this->assign('res',$list);// 赋值数据集
		$count  = $model->field($field)->where($where)->count();// 查询满足要求的总记录数
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
	}

	// 花絮视频列表
	public function hxsplb(){
		if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 0){
			$where['a_id'] = session('adminid');
		}
		header("Content-type:text/html;charset=utf-8");
		$model  = M('jx_video');
		if(!isset($_GET['p'])){
			$_GET['p'] = 1;
		}
		$where['type'] = 0;
		$field = "*";
		$list = $model->field($field)->where($where)->page($_GET['p'].',15')->select();
		$this->assign('res',$list);// 赋值数据集
		$count  = $model->field($field)->where($where)->count();// 查询满足要求的总记录数
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
	}
	//精选视频删除
	public function jxsp_del(){
       if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		header("Content-type:text/html;charset=utf-8");
		$where['jx_id'] = $_GET['id']+0;
		$ok = M('jx_video')->where($where)->delete();
		if($ok){                                                                                                                                                                     
			$this->success('删除成功',U('Admin/jxsplb'));
		}else{
			$this->error('删除失败',U('Admin/jxsplb'));
		}
	}

	//精选视频修改保存
	public function bcjxsp(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		$where['jx_id'] = intval($_POST['dd']);


			if($_POST['sort']>99){
				$_POST['sort'] = 99;
			}elseif($_POST['sort']<0){
				$_POST['sort'] =0;
			}

			foreach ($_POST as $key => $value) {
				if($key=="dd"){
					continue;
				}
				$data[$key] = $value;
			}
			$vname = M('video')->where(['v_id'=>$data['v_id']])->field(['video_type','video_file','c_id'])->find();
			$data['video_file'] = $vname['video_file'];
			$data['video_type'] = $vname['video_type'];
			$data['c_id'] = $vname['c_id'];

			$time_arr = explode('/',$data['start_time']); 
			$time = $time_arr[0].'-'.$time_arr[1].'-'.$time_arr[2].' '.$time_arr[3].':'.$time_arr[4].':'.$time_arr[5];
			$start_time = strtotime($time);

			$data['start_time'] = $start_time;
			$year = date('Y',time());
			$month = date('m',time());
			$day = date('d',time());
			$config = array(
					'maxSize'    =>    1*1024*1024,
					'rootPath'   =>     './Public/',
					'savePath'   =>    '../uploads/',
					'exts'       =>    array('jpg', 'png', 'jpeg'),
					'autoSub'    =>    true,
					'subName'    =>    array('date','Y/m/d'),
			);

			if($_FILES['jx_img']['name']){
				$upload = new \Think\Upload($config);	// 实例化上传类
				$info = $upload->upload();
				if(!$info) {	
					echo $upload->getError();
					exit;	
					//$this->error($upload->getError());
				    }else{  	   
				    		$name=substr($info['jx_img']['savepath'],2).$info['jx_img']['savename'];
				    		$data['jx_img'] = $name;
				    	
				    	$ok = M('jx_video')->where($where)->save($data);

				    	if($ok!==false){
						$this->success('修改成功',U('Admin/jxsplb'));
				    	}else{
						$this->error('修改失败',U('Admin/jxsplb'));
				    	}

				}

			}else{

					$ok = M('jx_video')->where($where)->save($data);
				    	if($ok!==false){
						$this->success('修改成功',U('Admin/jxsplb'));
				    	}else{
				}
			}




	}

	//精选视频编辑
	public function edit_jxsp(){
		
        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		$id = intval($_GET['id']);

		$arr = M('jx_video')->where('jx_id='.$id)->find();

		$arr['start_time'] = date('Y/m/d/H/i/s',$arr['start_time']);
		if(session('admintype') == 0){
			$where['a_id'] = session('adminid');
		}
		$video = M('video')->where($where)->select();
		$member = M('admin')->select();
		$this->assign('member',$member);
		$this->assign('videos',$video);

		$this->assign('arr',$arr);
		$this->display();
	}

	//轮播图列表
	public function lbtlb(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
				$arr = M('lunbo')->select();
				$this->assign('arr',$arr);
				$this->display();
			}else{
				$this->redirect('admin/zblb');
		}
	}


	//添加轮播图
	public function add_lbt(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(IS_POST){
			foreach ($_POST as $key => $value) {
				if($key=="dd"){
					continue;
				}
				$data[$key] = $value;
			}

			$year = date('Y',time());
			$month = date('m',time());
			$day = date('d',time());
			$config = array(
					'maxSize'    =>    1*1024*1024,
					'rootPath'   =>     './Public/',
					'savePath'   =>    '../uploads/',
					'exts'       =>    array('jpg', 'png', 'jpeg'),
					'autoSub'    =>    true,
					'subName'    =>    array('date','Y/m/d'),
			);
			
			$upload = new \Think\Upload($config);	// 实例化上传类
			$info = $upload->upload();
			if(!$info) {	
				echo $upload->getError();
				exit;	
				//$this->error($upload->getError());
			}else{  	   

			    	foreach($info as $key=>$file){
			    		//print_r($file);exit;
			    		$name=substr($file['savepath'],2).$file['savename'];
			    		$data[$key] = $name;
			    		//print($data);exit;

			    	}    
					//print_r($data);exit;
			    	$ok = M('lunbo')->add($data);
			    	if($ok){
					$this->success('添加成功',U('Admin/lbtlb'));
			    	}else{
					$this->error('添加失败',U('Admin/lbtlb'));
			    	}

			}

		
		}else{

			$this->display();
		}



	}

	public function edit_lbt(){
		if(session('adminid') && session('admintype') == 1){
			if(!IS_POST){
				$id = I('get.id');
				$lbt = M('lunbo')->where(['id'=>$id])->find();
				$this->assign('lbt',$lbt);
				$this->display();
			}else{
				$id = I('post.id');
				foreach($_POST as $k=>$v){
					if($k == 'id'){
						continue;
					}
					$data[$k] = $v;
					
				}
				$year = date('Y',time());
				$month = date('m',time());
				$day = date('d',time());
				$config = array(
					'maxSize'    =>    1*1024*1024,
					'rootPath'   =>     './Public/',
					'savePath'   =>    '../uploads/',
					'exts'       =>    array('jpg', 'png', 'jpeg'),
					'autoSub'    =>    true,
					'subName'    =>    array('date','Y/m/d'),
				);
				if($_FILES['big_img']['name'] || $_FILES['small_img']['name']){
					$upload = new \Think\Upload($config);	// 实例化上传类
					$info = $upload->upload();
					if(!$info) {	
					echo $upload->getError();
					exit;	
						//$this->error($upload->getError());
					}else{  	   

					    	foreach($info as $key=>$file){
					    		//print_r($file);exit;
					    		$name=substr($file['savepath'],2).$file['savename'];
					    		$data[$key] = $name;
					    		//print($data);exit;

					    	}    
							
						}

					}
					$ok = M('lunbo')->where(['id'=>$id])->save($data);
			    	if($ok){
						$this->success('修改成功',U('Admin/lbtlb'));
			    	}else{
						$this->error('修改失败',U('Admin/lbtlb'));
			    	}
			}

			
			
		}else{
			$this->redirect('admin/zblb');
		}

	}


	//查看轮播图
	public function show_lbt(){

        		if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
				$id = intval($_GET['id']);
				$arr = M('lunbo')->where('id='.$id)->find();
				$this->assign('arr',$arr);
				$this->display();
			}else{
				$this->redirect('admin/zblb');
		}
		





	}


	//删除轮播图
	public function lbt_del(){

        		if(!session('adminid')){
			$this->redirect('Admin/index');
		}


		$id = intval($_GET['id']);

		$ok = M('lunbo')->where("id=".$id)->delete();
	    	if($ok){
			$this->success('删除成功',U('Admin/lbtlb'));
	    	}else{
			$this->error('删除成功',U('Admin/lbtlb'));
	    	}

	}

	//广告列表
	public function gglb(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
				$gg = C('gg');
				$this->assign('gg',$gg);

				$arr = M('guanggao')->select();
				$this->assign('arr',$arr);
				$this->display();
			}else{
				$this->redirect('admin/zblb');
		}
		
	}





	//添加广告图
	public function add_gg(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 1){
				$gg = C('gg');
				$this->assign('gg',$gg);

				if(IS_POST){
					if($_POST['fenlei']==3){
						$fuck = getimagesize($_FILES['big_img']['tmp_name']);
						if(($fuck[0]!='260')||($fuck[1]!='500')){
							$this->error('首页直播部分左一展示广告图 必须宽260高500像素');
						}
					}

					foreach ($_POST as $key => $value) {
						if($key=="dd"){
							continue;
						}
						$data[$key] = $value;
					}

					$year = date('Y',time());
					$month = date('m',time());
					$day = date('d',time());
					$config = array(
							'maxSize'    =>    1*1024*1024,
							'rootPath'   =>     './Public/',
							'savePath'   =>    '../uploads/',
							'exts'       =>    array('jpg', 'png', 'jpeg'),
							'autoSub'    =>    true,
							'subName'    =>    array('date','Y/m/d'),
					);
			
					$upload = new \Think\Upload($config);	// 实例化上传类
					$info = $upload->upload();
					if(!$info) {	
						echo $upload->getError();
						exit;	
						//$this->error($upload->getError());
					}else{  	   
		//print_r($info);
					    	foreach($info as $key=>$file){
					    		//print_r($file);exit;
					    		$name=substr($file['savepath'],2).$file['savename'];
					    		$data[$key] = $name;
					    	}    
		//print_r($data);exit;
					    	$ok = M('guanggao')->add($data);
					    	if($ok){
							$this->success('添加成功',U('Admin/gglb'));
					    	}else{
							$this->error('添加失败',U('Admin/gg'));
					    	}
					}

				}else{

					$this->display();
				}
			}else{
				$this->redirect('admin/zblb');
		}

	}

	public function edit_gg(){
		if(session('adminid') && session('admintype') == 1){
			if(!IS_POST){
				$gg = C('gg');
				$id = I('get.id');
				$guang = M('guanggao')->where(['id'=>$id])->find();
				$this->assign('gg',$gg);
				$this->assign('guang',$guang);
				$this->display();
				}else{
				
					foreach ($_POST as $key => $value) {
						if($key=="dd"){
							continue;
						}
						$data[$key] = $value;
					}

					$year = date('Y',time());
					$month = date('m',time());
					$day = date('d',time());
					$config = array(
							'maxSize'    =>    1*1024*1024,
							'rootPath'   =>     './Public/',
							'savePath'   =>    '../uploads/',
							'exts'       =>    array('jpg', 'png', 'jpeg'),
							'autoSub'    =>    true,
							'subName'    =>    array('date','Y/m/d'),
					);
					if($_FILES['big_img']['name'] || $_FILES['small_img']['name']){
						if($_POST['fenlei']==3){
							$fuck = getimagesize($_FILES['big_img']['tmp_name']);
							if(($fuck[0]!='260')||($fuck[1]!='500')){
								$this->error('首页直播部分左一展示广告图 必须宽260高500像素');
							}
						}
						$upload = new \Think\Upload($config);	// 实例化上传类
						$info = $upload->upload();
						if(!$info) {	
							echo $upload->getError();
							exit;	
							//$this->error($upload->getError());
						}else{  	
						    	foreach($info as $key=>$file){
						    		//print_r($file);exit;
						    		$name=substr($file['savepath'],2).$file['savename'];
						    		$data[$key] = $name;
						    	}  
						}
					}

					$ok = M('guanggao')->where(['id'=>$_POST['dd']])->save($data);
					    if($ok){
							$this->success('修改成功',U('Admin/gglb'));
					    }else{
							$this->error('修改失败');
					   }
			}			
		}else{	
			$this->redirect('admin/zblb');
		}
	}


	//查看广告图
	public function show_gg(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		$gg = C('gg');

		$id = intval($_GET['id']);
		$arr = M('guanggao')->where('id='.$id)->find();

		$name = $gg[$arr['fenlei']];

		$this->assign('name',$name);
		$this->assign('arr',$arr);
		$this->display();

	}




	//删除广告图
	public function gg_del(){

        		if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		$id = intval($_GET['id']);

		$ok = M('guanggao')->where("id=".$id)->delete();


	    	if($ok){
			$this->success('删除成功',U('Admin/gglb'));
	    	}else{
			$this->error('删除成功',U('Admin/gglb'));
	    	}
		
	}


	//祝福列表 
	public function zflb(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		if(session('admintype') == 0){
			$where['a_id'] = session('adminid');
		}
		$_GET['p'] = $_GET['p']?$_GET['p']:1;

		$arr = M('video')->where($where)->page($_GET['p'].',10')->select();

		$this->assign('arr',$arr);
		$count  = M('video')->where($where)->count();// 查询满足要求的总记录数
		$Page  = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
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

	}

	//查看祝福列表
	public function show_zflb(){

		if(!session('adminid')){

			$this->redirect('Admin/index');
		}

		//print_r($_GET);exit;
		$id= intval($_GET['id']);
		$zhufu = M('zhufu')->where('v_id='.$id)->select();

		header("Content-type:text/html;charset=utf-8");
		$model  = M('zhufu');
		if(!isset($_GET['p'])){
			$_GET['p'] = 1;
		}
		$model = M('zhufu');
		$field = "*";
		$list = $model->field($field)->where('v_id='.$id)->page($_GET['p'].',10')->select();
		$users = array();
		$this->assign('res',$list);// 赋值数据集
		$count  = $model->field($field)->where('v_id='.$id)->count();// 查询满足要求的总记录数
		$Page  = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数
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

	}

	//删除祝福
	public function lbzf_del(){



        		if(!session('adminid')){
			$this->redirect('Admin/index');
		}

		//print_r($_GET);exit();
		$ok = M('zhufu')->where('zhu_id='.intval($_GET['id']))->delete();

		if($ok){
			$this->success('删除成功',U('Admin/zflb'));
		}else{
			$this->success('删除失败',U('Admin/zflb'));
		}

	}



 public function ueditup(){
 	//echo file_get_contents(COMMON_PATH."Conf/ueditconfig.json");exit;
        header("Content-Type: text/html; charset=utf-8");
        $editconfig = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(COMMON_PATH."Conf/ueditconfig.json")), true);
         //print_r($editconfig);exit();

            //dump($editconfig);
        $action = I('get.action');
        //echo $action;
        switch ($action) {
            case 'config':
                $result =  json_encode($editconfig);
                break;
            /* 上传图片 */
            case 'uploadimage':
                $result = $this->editup('img');
                break;
            /* 上传涂鸦 */
            case 'uploadscrawl':
                $result = $this->editup('img');
                break;
            case 'uploadvideo':
                $result = $this->editup('video');
                break;
            case 'uploadfile':
                $result = $this->editup('file');
                //$result = include("action_upload.php");
                break;
            /* 列出图片 */
            case 'listimage':
                $result = $this->editlist('listimg');
                break;
            /* 列出文件 */
            case 'listfile':
                $result = $this->editlist('listfile');
                break;
            /* 抓取远程文件 */
            case 'catchimage':
                //$result = include("action_crawler.php");
                break;
            default:
                $result = json_encode(array(
                    'state'=> '请求地址出错'
                ));
                break;
        }
        /* 输出结果 */
        if (isset($_GET["callback"])) {
            if (preg_match("/^[\w_]+$/", $_GET["callback"])) {
                echo htmlspecialchars($_GET["callback"]) . '(' . $result . ')';
            } else {
                echo json_encode(array(
                    'state'=> 'callback参数不合法'
                ));
            }
        } else {
            echo $result;
        }
        
    }
    public function editup($uptype){
        // if($this->islogin==false){
        //     $_re_data['state'] = '请登陆';
        //     return json_encode($_re_data);
        // }
        $editconfig = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(COMMON_PATH."Conf/ueditconfig.json")), true);
        switch ($uptype) {
            case 'img':
                $upload = new \Think\Upload();// 实例化上传类
                $upload->rootPath  =     '.';
                $upload->maxSize   =     $editconfig['imageMaxSize'];
                $upload->exts      =     explode('.', trim(join('',$editconfig['imageAllowFiles']),'.'));
                $upload->savePath  =     $editconfig['imagePathFormat'];
                $upload->saveName  =     time().rand(100000,999999);
                $info   =   $upload->uploadOne($_FILES[$editconfig['imageFieldName']]);
                break;
            case 'file':
                $upload = new \Think\Upload();// 实例化上传类
                $upload->rootPath  =     '.';
                $upload->maxSize   =     $editconfig['fileMaxSize'];
                $upload->exts      =     explode('.', trim(join('',$editconfig['fileAllowFiles']),'.'));
                $upload->savePath  =     $editconfig['filePathFormat'];
                $upload->saveName  =     time().rand(100000,999999);
                $info   =   $upload->uploadOne($_FILES[$editconfig['fileFieldName']]);
                break;
            case 'video':
                $upload = new \Think\Upload();// 实例化上传类
                $upload->rootPath  =     '.';
                $upload->maxSize   =     $editconfig['videoMaxSize'];
                $upload->exts      =     explode('.', trim(join('',$editconfig['videoAllowFiles']),'.'));
                $upload->savePath  =     $editconfig['videoPathFormat'];
                $upload->saveName  =     time().rand(100000,999999);
                $info   =   $upload->uploadOne($_FILES[$editconfig['videoFieldName']]);
                break;
            default:
                return false;
                break;
        }
        if(!$info) {// 上传错误提示错误信息
            $_re_data['state'] = $upload->getError();
            $_re_data['url'] = '';
            $_re_data['title'] = '';
            $_re_data['original'] = '';
            $_re_data['type'] = '';
            $_re_data['size'] = '';
        }else{// 上传成功 获取上传文件信息
            $_re_data['state'] = 'SUCCESS';
            $_re_data['url'] = $info['savepath'].$info['savename'];
            $_re_data['title'] = $info['savename'];
            $_re_data['original'] = $info['name'];
            $_re_data['type'] = '.'.$info['ext'];
            $_re_data['size'] = $info['size'];
        }
        return json_encode($_re_data);
    }
    public function editlist($listtype){
        $editconfig = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents(COMMON_PATH."Conf/ueditconfig.json")), true);
        switch ($listtype) {
            case 'listimg':
                $allowFiles = $editconfig['imageManagerAllowFiles'];
                $listSize = $editconfig['imageManagerListSize'];
                $path = $editconfig['imageManagerListPath'];
                break;
            
            case 'listfile':
                $allowFiles = $editconfig['fileManagerAllowFiles'];
                $listSize = $editconfig['fileManagerListSize'];
                $path = $editconfig['fileManagerListPath'];
                break;
            
            default:
                return false;
                break;
        }
        /* 获取参数 */
        $size = isset($_GET['size']) ? htmlspecialchars($_GET['size']) : $listSize;
        $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 0;
        $end = $start + $size;
        /* 获取文件列表 */
        $path = $_SERVER['DOCUMENT_ROOT'] . (substr($path, 0, 1) == "/" ? "":"/") . $path;
        $files = $this->getfiles($path, $allowFiles);
        if (!count($files)) {
            return json_encode(array(
                "state" => "no match file",
                "list" => array(),
                "start" => $start,
                "total" => count($files)
            ));
        }
        /* 获取指定范围的列表 */
        $len = count($files);
        for ($i = min($end, $len) - 1, $list = array(); $i < $len && $i >= 0 && $i >= $start; $i--){
            $list[] = $files[$i];
        }
        //倒序
        //for ($i = $end, $list = array(); $i < $len && $i < $end; $i++){
        //    $list[] = $files[$i];
        //}
        /* 返回数据 */
        $result = json_encode(array(
            "state" => "SUCCESS",
            "list" => $list,
            "start" => $start,
            "total" => count($files)
        ));
        return $result;
    }
    /**
     * 遍历获取目录下的指定类型的文件
     * @param $path
     * @param array $files
     * @return array
     */
    public function getfiles($path, $allowFiles, &$files = array())
    {
        if (!is_dir($path)) return null;
        if(substr($path, strlen($path) - 1) != '/') $path .= '/';
        $handle = opendir($path);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                $path2 = $path . $file;
                if (is_dir($path2)) {
                    $this->getfiles($path2, $allowFiles, $files);
                } else {
                    if(in_array('.'.pathinfo($file, PATHINFO_EXTENSION), $allowFiles)){
                        $files[] = array(
                            'url'=> substr($path2, strlen($_SERVER['DOCUMENT_ROOT'])),
                            'mtime'=> filemtime($path2)
                        );
                    }
                }
            }
        }
        return $files;
    }

    //添加红包
    public function add_hongbao(){
    	if(!session('adminid')){
    		$this->redirect('Admin/index');
    	}

    	if(IS_POST){

		$year = date('Y',time());
		$month = date('m',time());
		$day = date('d',time());
		$config = array(
				'maxSize'    =>    1*1024*1024,
				'rootPath'   =>     './Public/',
				'savePath'   =>    '../uploads/',
				'exts'       =>    array('jpg', 'png', 'jpeg'),
				'autoSub'    =>    true,
				'subName'    =>    array('date','Y/m/d'),
		);

		$upload = new \Think\Upload($config);	// 实例化上传类
		$info = $upload->upload();
		if(!$info) {	

			echo $upload->getError();
			exit;	
			//$this->error($upload->getError());
		    }else{  	   

		    	foreach($info as $key=>$file){
		    		//print_r($file);exit;
		    		$name=substr($file['savepath'],2).$file['savename'];
		    		$data[$key] = $name;
		    	}    
		    	foreach($_POST as $k=>$v){
		    		$data[$k] = $v;
		    	}
		    	$video = M('video')->where(['v_id'=>$data['v_id']])->find();
		    	$data['video_name'] = $video['video_file'];
		    	$ok = M('hongbao')->add($data);
		    	if($ok){
				$this->success('添加成功',U('Admin/hblb'));
		    	}else{
				$this->error('添加失败',U('Admin/add_hongbao'));
		    	}
		}
	

    	}else{
    		$aid = session('adminid');
    		if(session('admintype') == 1){
    			$where = 1;
    		}else{
    			$where['a_id'] = $aid;
    		}
    		$video = M('video')->where($where)->field(['v_id','video_name','video_file'])->select();
    		$this->assign('video',$video);
    		 $this->display();
    	}
    }

    public function edit_hongbao(){
    	if(session('adminid')){
    		if(!IS_POST){
    			if(session('admintype') == 0){
    				$where['a_id'] = session('adminid');
	    		}
	    		$video = M('video')->where($where)->field(['v_id','video_file'])->select();
	    		$this->assign('video',$video);
	    		$id = I('get.id');
	    		$hong = M('hongbao')->where(['hb_id'=>$id])->find();
	    		$this->assign('hong',$hong);
	    		$this->display();
    		}else{
    			foreach($_POST as $k=>$v){
    				if($k == 'id'){
    					continue;
    				}
    				$data[$k] = $v;
    			}
    			$year = date('Y',time());
				$month = date('m',time());
				$day = date('d',time());
				$config = array(
					'maxSize'    =>    1*1024*1024,
					'rootPath'   =>     './Public/',
					'savePath'   =>    '../uploads/',
					'exts'       =>    array('jpg', 'png', 'jpeg'),
					'autoSub'    =>    true,
					'subName'    =>    array('date','Y/m/d'),
				);

				if($_FILES['xl_img']['name'] || $_FILES['xn_img']['name']){
					$upload = new \Think\Upload($config);	// 实例化上传类
					$info = $upload->upload();
					if(!$info) {	

						echo $upload->getError();
						exit;	
						//$this->error($upload->getError());
					    }else{  
					    	foreach($info as $key=>$file){
					    		//print_r($file);exit;
					    		$name=substr($file['savepath'],2).$file['savename'];
					    		$data[$key] = $name;
					    	}  
					    		
    				}
    			}
    				
    			$video = M('video')->where(['v_id'=>$data['v_id']])->find();
				$data['video_name'] = $video['video_file'];
				$ok = M('hongbao')->where(['hb_id'=>$_POST['id']])->save($data);
		    	if($ok){
					$this->success('修改成功',U('Admin/hblb'));
		    	}else{
					$this->error('修改失败');
		    	}
    		}
    	}else{
    		$this->redirect('admin/index');
    	}
    }
    //红包列表
    public function hblb(){

    	$aid = session('adminid');
    	if(!$aid){
    		$this->redirect('Admin/index');
    	}
    	if(session('admintype') == 0){
    			$where['a_id'] = $aid;
    		}

    	$video = M('video')->field(['video_name','v_id'])->where($where)->select();
    	$res = array();

    	foreach($video as $k=>$v){
    		$res[] = $v['v_id'];
    	}

    	if(!empty($res)){

    		$map['v_id'] = array('in',$res);
	    	$arr = M('hongbao')->where($map)->select();
	    	$this->assign('arr',$arr);
	    	$this->display();
    	}else{
    		$this->display();
    	}
    }

    //红包展示图
    public function show_hb(){

        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		$id = intval($_GET['id']);
		$arr = M('hongbao')->where('hb_id='.$id)->find();
		$this->assign('arr',$arr);
		$this->display();
    }

    //删除红包
    public function hb_del(){
        if(!session('adminid')){
			$this->redirect('Admin/index');
		}
		$id = intval($_GET['id']);
		$ok = M('hongbao')->where('hb_id='.$id)->delete();

	    	if($ok){
			$this->success('删除成功',U('Admin/hblb'));
	    	}else{
			$this->error('删除失败',U('Admin/hblb'));
	    	}


    }




}


?>
