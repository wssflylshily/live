<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends Controller {

	protected $city;

    // 遍历 直播城市
    public function __construct(){
        parent::__construct();
        $this->city = D('qy')->field(['qy_id','qy_name'])->select();
    }

	/*------------------------------------------------------------------------------------
	|个人编辑资料
	|------------------------------------------------------------------------------------*/

	public function personal_Info(){
		if(!IS_POST){

				$name = $_SESSION['memberid'];
//			print_r($_SESSION);exit;
				$user = M('user')->where('userid='.$name)->find();
				if(!$user['QQ']){
					$user['QQ'] = '未填写';
				}
				$id = session('member.userid');
         		 $arr = M('user')->where('userid='.$id)->select();
				 foreach ($arr as $key => $value) {                
		                $new_arr['imgSrc']  = $value['userlog']?$value['userlog']:'/Public/Home/img/default.jpg';
		                $new_arr['nickname']  = $value['nick']?$value['nick']:'未填写';
		                $new_arr['gender']  = $value['gender']?$value['gender']:'未填写';
		                $new_arr['addr']  = $value['path']?$value['path']:'未填写';
		                $new_arr['qqNum']  = $value['QQ']?$value['QQ']:'未填写';
		                $new_arr['phone']  = $value['mPhone']?$value['mPhone']:'未填写';
		          }
			//print_r($user);exit;
			$this->assign('user',$user);
         	 $this->assign('citys',$this->city);
       		$this->assign('cname','中国');
         	$this->assign('new_arr',json_encode($new_arr));
			$this->display();
		}else{

			//上传头像
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
			    }else{  	   
			    	foreach($info as $file){

			    		$name = substr($file['savepath'],2).$file['savename'];
			    		$data['userlog'] = $name;
			    		$name = $_SESSION['member']['userid'];
			    		$ok = M('user')->where(['userid'=>$name])->save($data);

			    	}    
			    	if($ok){
						$this->redirect('Home/User/personal_Info');
			    	}else{
						$this->redirect('添加失败',U('Home/User/personal_Info'));
			    	}
			}
		}
		
	}

	//修改昵称
	public function update_user_Nick(){
		
		$name = $_SESSION['memberid'];
		$ok = M('user')->where('userid='.$name)->save(array('nick'=>$_POST['name']));
		
		
		if($ok){
			exit('1');
		}else{
			exit('0');
		}
	}
	//性别写入
	public function update_user_Gender(){
	
		$name = $_SESSION['memberid'];
		$ok = M('user')->where('userid='.$name)->save(array('gender'=>$_POST['name']));
		if($ok){
			exit('1');
		}else{
			exit('0');
		}
	}

	//地址写入
	public function update_user_Path(){
	
		$name = $_SESSION['memberid'];
		$ok = M('user')->where('userid='.$name)->save(array('path'=>$_POST['name']));
		if($ok){
			exit('1');
		}else{
			exit('0');
		}
	}


	//QQ写入
	public function update_user_qq(){
	
		
		$name = $_SESSION['memberid'];
		$ok = M('user')->where(['userid'=>$name])->save(array('QQ'=>$_POST['name']));
		//print_r($ok);exit;
		if($ok){
			exit('1');
		}else{
			exit('0');
		}
	}


	//绑定手机号
	public function user_modal_phone_code(){
		//print_r($_POST);exit;
		$rand = mt_rand(100000,999999);
        $_SESSION['mPhonecode'] = $rand;
        // echo $rand;
        // $uri = "http://cf.lmobile.cn/submitdata/Service.asmx/g_Submit";
        // $data['sname'] = 'dlbbjkwj';
        // $data['spwd'] = 'GtV4vv9X';
        // $data['scorpid'] = '';
        // $data['sprdid'] = '1012818';
        // $data['sdst'] = $_POST['name']+0;
        // $data['smsg'] = '您的绑定验证码是'.$rand.'，5分钟有效【番茄网络】';
        // foreach ($data as $key => $value) {
        //         $o.= "$key=" . urlencode( $value ). "&" ;
        // }
        // $post_data = substr($o,0,-1);    
        // $ch = curl_init ();
        // curl_setopt ( $ch, CURLOPT_URL, $uri );
        // curl_setopt ( $ch, CURLOPT_POST, 1 );
        // curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        // curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        // curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
        // $return = curl_exec ( $ch );
        // curl_close ( $ch );
            $uid = '57423';     //数字用户名
            $pwd = 'k3le03';       //密码
            $mobile  = $_POST['name']+0;   //号码
            $content = "您的绑定验证码是:".$rand."，5分钟有效。【直播】";
            //iconv('gbk','utf-8',$content);  
            //iconv('utf-8','gbk',$content); 
            //iconv('gbk','utf-8',$content);
// $k = md5('k3le03');
// echo $k;exit;
            //即时发送
            $res = $this->sendSMS($uid,$pwd,$mobile,$content);
	        exit('1');

 
	}

	//绑定手机验证
	public function user_modal_phone(){
		/*print_r($_SESSION);
		print_r($_POST['mPhonecode']);*/
		$name = $_SESSION['memberid'];
		//验证图片验证码
		$verify = new \Think\Verify();
        if(!$verify->check($_POST['mPhotocode'])){
            exit('-2');
        }
        if($_SESSION['mPhonecode'] != $_POST['mPhonecode']){
         	exit('-1');
        }
        if(MD5($_POST['mPassword']) != $_SESSION['member']['password']){
         	exit('0');
        }

        $ok = M('user')->where(['userid'=>$name])->save(array('mPhone'=>$_POST['mPhone']));
        

		if($ok!==false){
			exit('1');
		}else{
			exit('-3');
		}
		//print_r($_SESSION);exit;
		
	}




	//绑定查询
	public function get_modal_phone(){
		
		$name = $_SESSION['memberid'];
		$ok = M('user')->field('mPhone')->where('userid='.$name)->find();

		if($ok){
			exit('1');
		}else{
			exit('0');
		}
	}
	
	/*------------------------------------------------------------------------------------
	|个人历史记录
	|--------------------------------------------------------------------------------------*/
	
	public function personal_center_view(){

		 if(!session('member.userid')){
       			$this->redirect('Login/login');
       		}

       	$this->assign('citys',$this->city);
       	$this->assign('cname','中国');

      
       	$name = $_SESSION['memberid'];
		$user = M('user')->where('userid='.$name)->find();
		$hisnum = M('guankan')->where(['userid'=>session('member.userid')])->count();
		$history = M('guankan')->where(['userid'=>session('member.userid')])->order('gk_time desc')->limit(5)->select();
		$invnum = M('invite')->where(['userid'=>session('member.userid')])->count();
		$invite = M('invite')->where(['uid'=>session('member.userid')])->order('inv_num desc')->limit(5)->select();
     
       	
		// 历史记录
		 //  {imgSrc:'/Public/Home/img/liveroom_videos_01.jpg',title:'创意北京婚礼，小成本，达成面dasdasdasdas',date:'2016-05-30',time:'18:06'},
         // {imgSrc:'/Public/Home/img/liveroom_videos_01.jpg',title:'创意北京婚礼，小成本，达成面',date:'2016-05-30',time:'18:06'},
         // {imgSrc:'/Public/Home/img/liveroom_videos_01.jpg',title:'创意北京婚礼，小成本，达成面',date:'2016-05-30',time:'18:06'}
		
		if($hisnum <= 5){
			$this->assign('hisnum',0);
		}else{
			$this->assign('hisnum',$hisnum);
		}

       	foreach($history as $k=>$v){
       		$arr[$k]['imgSrc'] = $v['sp_img'];
       		$arr[$k]['title'] = $v['sp_title'];
       		$arr[$k]['date'] = date('Y-m-d',$v['gk_time']);
       		$arr[$k]['time'] = date('H:i',$v['gk_time']);
       	}
       	$his['data'] = $arr;
       	// 邀约历史
       	// {imgSrc:'/Public/Home/img/history_ListSub.jpg',title:'创意北京婚礼，小成本，达成面dasdasdasdas',uSum:123456},
       // {imgSrc:'/Public/Home/img/history_ListSub.jpg',title:'创意北京婚礼，小成本，达成面',uSum:553456},
       // {imgSrc:'/Public/Home/img/history_ListSub.jpg',title:'创意北京婚礼，小成本，达成面',uSum:553456}
       
		if($invnum <= 5){
			$this->assign('invnum',0);
		}else{
			$this->assign('invnum',$invnum);
		}
       	
       	foreach($invite as $k=>$v){
       		$invs[$k]['imgSrc'] = $v['v_img'];
       		$invs[$k]['title'] = $v['v_title'];
       		$invs[$k]['uSum'] = $v['inv_num'];
       	}
       	$inv['data'] = $invs;
       	$this->assign('invs',json_encode($inv));
       	$this->assign('his',json_encode($his));
		$this->assign('user',$user);
		$this->display();
	} 

	public function ajaxGethis(){
		$page = (int)I('get.page')?(int)I('get.page'):2;
		$hiss = M('guankan');
		
		$map['userid'] = session('member.userid');
		
		$num = $hiss->where($map)->count();
		$start = ($page-1)*5;
		$sheng = $num - $start;
		if($sheng <= 5){
			$his['num'] = 0;
		}else{
			$his['num'] = $sheng;
		}

		$history = $hiss->where($map)->order('gk_time desc')->limit($start,5)->select();
		foreach($history as $k=>$v){
       		$arr[$k]['imgSrc'] = $v['sp_img'];
       		$arr[$k]['title'] = $v['sp_title'];
       		$arr[$k]['date'] = date('Y-m-d',$v['gk_time']);
       		$arr[$k]['time'] = date('H:i',$v['gk_time']);
       	}
       	$his['data'] = $arr;
       	echo json_encode($his);exit;
	}

	public function ajaxGetinvs(){
		$page = (int)I('get.page')?(int)I('get.page'):2;
		$invs = M('invite');
	
		$map['uid'] = session('member.userid');
		
		
		$start = ($page-1)*5;
		$num = $invs->where($map)->count();
		$sheng = $num-$start;

		if($sheng <=5 ){
			$inv['num'] = 0;
		}else{
			$inv['num'] =$sheng;
		}

		$invite = $invs->where($map)->order('inv_num desc')->limit($start,5)->select();

       	foreach($invite as $k=>$v){
       		$invs[$k]['imgSrc'] = $v['v_img'];
       		$invs[$k]['title'] = $v['v_title'];
       		$invs[$k]['uSum'] = $v['inv_num'];
       	}
       	$inv['data'] = $invs;
       	echo json_encode($inv);exit;
	}

	/*------------------------------------------------------------------------------------
	|忘记密码
	|--------------------------------------------------------------------------------------*/

	//判断该用户是否注册,如是发送验证码
	public function get_username(){
		$userModel = D('user'); 
		
		$user = $userModel->where('mobile='.I('post.name'))->find();
		if(empty($user)){
			exit('0');
		}else{
			$rand = mt_rand(100000,999999);
	        
	        // $uri = "http://cf.lmobile.cn/submitdata/Service.asmx/g_Submit";
	        // $data['sname'] = 'dlbbjkwj';
	        // $data['spwd'] = 'GtV4vv9X';
	        // $data['scorpid'] = '';
	        // $data['sprdid'] = '1012818';
	        // $data['sdst'] = $_POST['name']+0;
	        // $data['smsg'] = '您的新密码'.$rand.'，请妥善保管并及时修改【番茄网络】';
	        // foreach ($data as $key => $value) {
	        //         $o.= "$key=" . urlencode( $value ). "&" ;
	        // }
	        // $post_data = substr($o,0,-1);    
	        // $ch = curl_init ();
	        // curl_setopt ( $ch, CURLOPT_URL, $uri );
	        // curl_setopt ( $ch, CURLOPT_POST, 1 );
	        // curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	        // curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	        // curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
	        // $return = curl_exec ( $ch );
	        // curl_close ( $ch );
            $uid = '57423';     //数字用户名
            $pwd = 'k3le03';       //密码
            $mobile  = $_POST['name']+0;   //号码
            $content = "您的短信验证码是：".$rand."，【直播】";
            //iconv('gbk','utf-8',$content);  
            //iconv('utf-8','gbk',$content); 
            //iconv('gbk','utf-8',$content);
// $k = md5('k3le03');
// echo $k;exit;
            //即时发送
            $res = $this->sendSMS($uid,$pwd,$mobile,$content);
	       // $ok = M('user')->where('name='.$_SESSION['member']['mobile'])->save(array('password'=>md5($rand)));
	        //if($ok){
	        	exit('1');
	        //}else{
	       // 	exit('-1');
	        //}
	        
		}
	}


	//提交验证
	public function forget_pwd(){
		if(!IS_POST){
			$this->assign('citys',$this->city);
			$this->assign('cname','北京');
			$this->display();
		}else{
			$_SESSION['mobile']=$_SESSION['mobile']?$_SESSION['mobile']:'0';
			
			//判断传过来的手机号是否和发短信的一至
			if($_POST['name']!=$_SESSION['mobile']){
				exit('-1');
			}

			//验证码
			$verify = new \Think\Verify();
            if(!$verify->check($_POST['yzm'])){
                exit('0');
            }

            //手机验证码
            $_SESSION['mobilecode']=$_SESSION['mobilecode']?$_SESSION['mobilecode']:'0';

			if($_POST['name']!=$_SESSION['mobilecode']){
				exit('-2');
			}else{
				$ok = $this->update_password();
		        if($ok){
		        	exit('1');
		        }else{
		        	exit('-3');
		        }
		        
			}
		}
	} 

	public function update_password(){
		$rand = mt_rand(100000,999999);
	    // $uri = "http://cf.lmobile.cn/submitdata/Service.asmx/g_Submit";
	    // $data['sname'] = 'dlbbjkwj';
	    // $data['spwd'] = 'GtV4vv9X';
	    // $data['scorpid'] = '';
	    // $data['sprdid'] = '1012818';
	    // $data['sdst'] = $_POST['name']+0;
	    // $data['smsg'] = '您的新密码是'.$pwd.'，请您及时修改【番茄网络】';
	    // foreach ($data as $key => $value) {
	    //         $o.= "$key=" . urlencode( $value ). "&" ;
	    // }

	    // $post_data = substr($o,0,-1);    
	    // $ch = curl_init ();
	    // curl_setopt ( $ch, CURLOPT_URL, $uri );
	    // curl_setopt ( $ch, CURLOPT_POST, 1 );
	    // curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	    // curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
	    // curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
	    // $return = curl_exec ( $ch );
	    // curl_close ( $ch );

            $uid = '57423';     //数字用户名
            $pwd = 'k3le03';       //密码
            $mobile  = $_POST['name']+0;   //号码
            $content = "您的新密码是：".$rand."，请您及时修改。【直播】";
            //iconv('gbk','utf-8',$content);  
            //iconv('utf-8','gbk',$content); 
            //iconv('gbk','utf-8',$content);
// $k = md5('k3le03');
// echo $k;exit;
            //即时发送
            $res = $this->sendSMS($uid,$pwd,$mobile,$content);
            //echo $res;
     

	    //写入数据库
	  	return M('user')->where('mobile='.$_POST['name'])->save(array('password'=>md5($rand)));
	}

	/*------------------------------------------------------------------------------------
	|个人修改密码
	|------------------------------------------------------------------------------------*/
	public function user_Update_password(){

		$name = $_SESSION['member']['mobile'];

		$user = M('user')->where('mobile='.$name)->find();
		$pwd = $user['password'];
		$oldPwd = MD5($_POST['oldPwd']);
		$newPwd = MD5($_POST['newPwd']);
		$rePwd = MD5($_POST['rePwd']);
		if(strlen($_POST['newPwd'])<6){
			exit('-3');
		}
		if( $pwd !== $oldPwd){
			exit('0');
		}else if($newPwd !== $rePwd){
			exit('-1');
		}else{
			$ok = M('user')->where('mobile='.$name)->save(array('password'=>$newPwd));
			if($ok){
				exit('1');
			}else{
				exit('-2');
			}
			
		}

	}
	/*------------------------------------------------------------------------------------
	|个人邀约中心
	|--------------------------------------------------------------------------------------*/

	public function inviteList(){
        $cid = $_GET['cid']?$_GET['cid']:3;
        $cname = D('qy')->where(['qy_id'=>$cid])->getField('qy_name');
        $this->assign('citys',$this->city);
        $this->assign('cname',$cname);		
	
		$inv = M('invite');
		$map['uid'] = session('member.userid');

		
		$invnum = $inv->where($map)->count();
		if($invnum <= 8){
			$this->assign('invnum',0);
		}else{
			$this->assign('invnum',$invnum);
		}  

		$invite = $inv->where($map)->order('inv_num desc')->limit(8)->select();

       	foreach($invite as $k=>$v){
       		$invs[$k]['inviteRanking'] = $k+1;
       		$invs[$k]['uName'] = $v['uname'];
       		$invs[$k]['imgSrc'] = $v['v_img'];
       		$invs[$k]['inviteDate'] = date('Y-m-d',$v['inv_time']);
       		$invs[$k]['inviteTime'] = date('i:s',$v['inv_time']);
       	}

       	$data['data'] = $invs;
       	$this->assign('invs',json_encode($data));
		$this->display();
	} 
	
	public function ajaxGetperinv(){
		$page = (int)I('get.page')?(int)I('get.page'):2;
		$inv = M('invite');
		if(session('auth') == 'reguser'){
			$map['uid'] = session('member.userid');
		}else{
			$map['wid'] = session('wid');
		}
		
		$invnum = $inv->where($map)->count();
		$start = ($page-1)*4;
		$sheng = $invnum-$start;
		if($sheng <= 8){
			$data['num'] = 0;
		}else{
			$data['num'] = $sheng;
		}

		$invite = $inv->where($map)->order('inv_num desc')->limit($start,8)->select();
       	foreach($invite as $k=>$v){
       		$invs[$k]['inviteRanking'] = $k+1;
       		$invs[$k]['uName'] = $v['uname'];
       		$invs[$k]['imgSrc'] = $v['v_img'];
       		$invs[$k]['inviteDate'] = date('Y-m-d',$v['inv_time']);
       		$invs[$k]['inviteTime'] = date('i:s',$v['inv_time']);
       	}

       	$data['data'] = $invs;
       	echo json_encode($data);exit;
	}
	/*------------------------------------------------------------------------------------
	|退出登录
	|--------------------------------------------------------------------------------------*/
	public function logout(){
		session('member',null);
		session('memberid',null);
		$this->redirect('Index/index');
	}

	/*------------------------------------------------------------------------------------
	|验证码
	|--------------------------------------------------------------------------------------*/
	public function photo_check(){
		ob_end_clean();
		$Verify = new \Think\Verify();
		$Verify->length   = 4;
        $Verify->entry();
	}

  
public function sendSMS($uid,$pwd,$mobile,$content,$time='',$mid='')
{
    $http = 'http://http.yunsms.cn/tx/';
    $data = array
        (
        'uid'=>$uid,                    //数字用户名
        'pwd'=>strtolower(md5($pwd)),   //MD5位32密码
        'mobile'=>$mobile,              //号码
        'content'=>$content,            //内容 如果对方是utf-8编码，则需转码iconv('gbk','utf-8',$content); 如果是gbk则无需转码
        //'content'=>iconv('utf-8','gbk',$content),
        //'content'=>iconv('gbk','utf-8',$content),         //内容 如果对方是utf-8编码，则需转码iconv('gbk','utf-8',$content); 如果是gbk则无需转码
        //'time'=>$time,      //定时发送
        //'mid'=>$mid                     //子扩展号
        'encode'=>'utf8',
        );
    $re= $this->postSMS($http,$data);          //POST方式提交
    
    if( trim($re) == '100' )
    {
        return "发送成功!";
    }
    else 
    {
        return "发送失败! 状态：".$re;
    }
}

public function postSMS($url,$data='')
{
    $post='';
    $row = parse_url($url);
    $host = $row['host'];
    $port = $row['port'] ? $row['port']:80;
    $file = $row['path'];
    while (list($k,$v) = each($data)) 
    {
        $post .= rawurlencode($k)."=".rawurlencode($v)."&"; //转URL标准码
    }
    //echo $post;exit;
    $post = substr( $post , 0 , -1 );
    $len = strlen($post);
    
    //$fp = @fsockopen( $host ,$port, $errno, $errstr, 10);
     
    $fp = stream_socket_client("tcp://".$host.":".$port, $errno, $errstr, 10);
    
    
    
    
    if (!$fp) {
        return "$errstr ($errno)\n";
    } else {
        $receive = '';
        $out = "POST $file HTTP/1.0\r\n";
        $out .= "Host: $host\r\n";
        $out .= "Content-type: application/x-www-form-urlencoded\r\n";
        $out .= "Connection: Close\r\n";
        $out .= "Content-Length: $len\r\n\r\n";
        $out .= $post;      
        fwrite($fp, $out);
        while (!feof($fp)) {
            $receive .= fgets($fp, 128);
        }
        fclose($fp);
        $receive = explode("\r\n\r\n",$receive);
        unset($receive[0]);
        return implode("",$receive);
    }
}

}
