<?php
namespace Home\Controller;
use Think\Controller;
class LoginController extends Controller {
	/*------------------------------------------------------------------------------------
	|验证登陆
	|--------------------------------------------------------------------------------------*/
	 protected $city;

    // 遍历 直播城市
    public function __construct(){
        parent::__construct();
        $this->city = D('qy')->field(['qy_id','qy_name'])->select();
    }

	public function dologin(){
		// if(session('auth') == 'wuser'){
		// 	session('wuser',null);
		// 	session('wid',null);
		// 	session('auth',null);
		// }
	
		$name = I('name');
		$pwd = md5($_POST['password']);
		$m = M('user');
		$user = $m->where('mobile="'.$name.'"')->find();

		if (!empty($user)) {
			if ($pwd==$user['password']){
				session('member',$user);
				session('memberid',$user['userid']);
	 			// session('auth','reguser');
				//print($_SESSION['member']);exit;
				exit('1');
			}else{
				exit('0');
			}
		}else{

			exit('0');
			
		}
	}

	//快捷登录
	public function dologin1(){
		$name = I('name');
		$pwd = md5($_POST['password']);
		$m = M('user');
		$user = $m->where('mobile="'.$name.'"')->find();

		if (!empty($user)) {
			if ($pwd==$user['password']){
				session('member',$user);
				session('memberid',$user['id']);
				//print($_SESSION['member']);exit;
				exit('1');
			}else{
				exit('0');
			}
		}else{
			$data['mobile'] = $name;
			$data['password'] = $pwd;
			$data['pubtime'] = time();

			/*
			echo substr('abcdef', 1);      //输出 bcdef
echo substr('abcdef', 1, 2);   //输出 bc
echo substr('abcdef', -3, 2);  //输出 de
echo substr('abcdef', 1, -2);  //输出 bcd
			 */
			
			$tou  =  substr($name,0,3);
			$wei  = substr($name,-3,3);

			$data['nick']  =  $tou.'*****'.$wei;
			$data['userlog'] = "/Public/Home/img/default.jpg";
			$ok = M('user')->add($data);
			
			if($ok){

				$data['userid'] = $ok;
				session('member',$data);
				session('memberid',$ok);

				//print_r(session());exit;
				exit('1');
			}else{
				exit('0');
			}

		}
	}



	/*------------------------------------------------------------------------------------
	|退出登录
	|------------------------------------------------------------------------------------*/

	// public function loginout(){
	// 	session('member',$user);
	// 	session('memberid',$user['userid']);
	// 	$this->redirect('Index/index');
	// }



	/*------------------------------------------------------------------------------------
	|用户注册
	|-----------------------------------------------------------------------------------*/

	public function doregister(){

		$name  =  I('post.mobile');
		$pwd  =  md5(I('post.password'));

		if(!preg_match("/^1[3|4|5|7|8]\d{9}$/", $name)){
			$this->error("用户名不合法",'register',2);
			exit();
		}
		//echo $name;

		$user = M('user')->where(array('mobile'=>$name))->find();
		//print_r($user);exit;
		if ($user){
			$this->error("该用户已经注册");;
		}else{
			$userModel = D('User');
            if(!$userModel->create()){
                echo $userModel->getError();
            }

			$tou  =  substr($name,0,3);
			$wei  = substr($name,-3,3);

			$nick  =  $tou.'*****'.$wei;

            $pwd = $userModel->password = md5($userModel->password);//$data['userlog'] = "/Public/Home/img/default.jpg";
			$id = $userModel->add(array('mobile'=>$name,'password'=>$pwd,'pubtime'=>time(),'userlog'=>"/Public/Home/img/default.jpg",'nick'=>$nick));
			if ($id){
				// session('wuser',null);
				// session('wid',null);
				// session('auth',null);
				$reurl = U("Home/User/personal_Info");
				// $this->success("注册成功","/Home/User/personal_Info.html");
				$user = $userModel->where(array('mobile'=>$name))->find();
				//var_dump($user);exit;
				session('member',$user);
				session('memberid',$user['userid']);
				// session('auth','reguser');
				$reurl = U("Home/User/personal_Info");
				header("location:".$reurl);
			}else{
				$this->error("注册失败",'register');
			}
		}	
	}


	/*------------------------------------------------------------------------------------
	|获取手机验证码
	|------------------------------------------------------------------------------------*/

	function get_mobile_code(){
		//$data['sdst'] = $_POST['name']+0;
        $rand = mt_rand(100000,999999);
        $_SESSION['mobilecode'] = $rand;
        //$uri = "http://cf.51welink.com/submitdata/service.asmx/g_Submit";
        // $uri = "http://cf.lmobile.cn/submitdata/Service.asmx/g_Submit";
        // $data['sname'] = 'dlbbjkwj';
        // $data['spwd'] = 'GtV4vv9X';
        // $data['scorpid'] = '';
        // $data['sprdid'] = '1012818';
        // $data['sdst'] = $_POST['name']+0;
        // $data['smsg'] = '您的注册验证码是'.$rand.'，5分钟有效【番茄网络】';
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
            $content = "您的注册验证码是：".$rand."，请在5分钟内验证。【直播】";
            //iconv('gbk','utf-8',$content);  
            //iconv('utf-8','gbk',$content); 
            //iconv('gbk','utf-8',$content);
// $k = md5('k3le03');
// echo $k;exit;
            //即时发送
            $res = $this->sendSMS($uid,$pwd,$mobile,$content);
            //echo $res;
            if($res=="发送成功!"){
            	exit('1');
            }
        
    }

    /*------------------------------------------------------------------------------------
	|判断验证码是否正确
	|------------------------------------------------------------------------------------*/

	public function check_mobile_code(){
		$_SESSION['mobilecode']=$_SESSION['mobilecode']?$_SESSION['mobilecode']:'0';

		if($_POST['name']!=$_SESSION['mobilecode']){
			exit('0');
		}else{
			exit('1');
		}

	}

	/*------------------------------------------------------------------------------------
	|修改密码
	|------------------------------------------------------------------------------------*/
	public function dopwd(){

		$name  =  I('post.name','strip_tags');
		
		$_SESSION['mobilecode'] = $_SESSION['mobilecode']?$_SESSION['mobilecode']:1111;
		if($_POST['mobilecode']==$_SESSION['mobilecode']){


			$data['status'] = 1;
			$data['msg'] = "可以";

		}else{

			$data['status'] = 0;
			$data['msg'] = "手机验证码不正确";

		}

		echo json_encode($data);
	}


	public function login(){
		//print_r($_SERVER);exit;
		//echo getenv("HTTP_REFERER");exit;//和$_SERVER("HTTP_REFERER");一样的作用 
		//echo $_SERVER['HTTP_REFERER'];exit;
		$this->assign('citys',$this->city);
		$cid = $_GET['cid']?$_GET['cid']:3;
		$cname = D('qy')->where(['qy_id'=>$cid])->getField('qy_name');
		$this->assign('cname',$cname);

		// 手机端
		$pappid = C('pappid');
		$pappsecret = C('pappsecret');
		$redirect = urlencode(C('WEB').U('Home/Login/pwback'));
		
		// pc端
		$appid = C('appid');
		$predirect = urlencode(C('WEB').U('/Home/Login/wback'));
		
		// qq登录
		$qqid = C('qqid');

		$qqredirect = urlencode("http://".$_SERVER['HTTP_HOST']."/Home/Login/qqback");

		// 微博登录
		$sinaid = C('sinaid');
		$sinaredirect = C('WEB').U('/home/login/sinaback');
		// echo $sinaredirect;exit;
		if(isset($_GET['u']) && isset($_GET['fen'])){
				$id = $_GET['u']+0;
				$uri = U('Home/Live/liveRoom_broadcast',['id'=>$id,'fen'=>1]);
		}else if(isset($_GET['d']) && isset($_GET['fen'])){
				$id = $_GET['d']+0;
				$uri = U('Home/Live/wonderful_broadcast',['id'=>$id,'fen'=>1]);
		}else if($_GET['u'] && !isset($_GET['fen'])){
				$id = $_GET['u']+0;
				$uri = U('Home/Live/liveRoom_broadcast',['id'=>$id]);
		}else if($_GET['d'] && !isset($_GET['fen'])){
				$id = $_GET['d']+0;
				$uri = U('Home/Live/wonderful_broadcast',['id'=>$id]);
		}
	

		//$uri = base64_decode($_GET['u']);
		
		$enuri = urlencode($uri);
        $_SESSION['state'] = md5(uniqid(rand(), TRUE));
		$purl = "https://open.weixin.qq.com/connect/qrconnect?appid={$appid}&redirect_uri={$predirect}&response_type=code&scope=snsapi_login&state={$enuri}#wechat_redirect";

		$wurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$pappid}&redirect_uri={$redirect}&response_type=code&scope=snsapi_userinfo&state={$enuri}#wechat_redirect";

		$qqurl = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id={$qqid}&redirect_uri={$qqredirect}&state=".$_SESSION['state'];
		$sinaurl = "https://api.weibo.com/oauth2/authorize?client_id={$sinaid}&redirect_uri={$sinaredirect}&state={$enuri}";
		
		// echo $enuri;exit;
		// echo $url;exit;
		$this->assign('purl',$purl);
		$this->assign('url',$wurl);
		$this->assign('uris',$uri);
		$this->assign('qqurl',$qqurl);
		$this->assign('sinaurl',$sinaurl);
		//print_r($_SERVER);
		$this->display();
	}


public function qqlogin(){
        $this->appid = '101389568';
        $this->secret = '22e6b2228675e39de3f5d558743ef2d2';
        //$this->return_url = "http://".$_SERVER['HTTP_HOST']."/index.php/Home/ThirdLogin/callback/oauth/weixin";
        //qq登录回调地址(qq的消息返回到哪)
        $this->return_url = "http://".$_SERVER['HTTP_HOST']."/home/login/qqback";
        //-------生成唯一随机串防CSRF攻击 存到session中
        $_SESSION['state'] = md5(uniqid(rand(), TRUE));
        //拼接URL
        $dialog_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
            . $this->appid . "&redirect_uri=" . $this->return_url . "&state="
            . $_SESSION['state'];
            // return;
        echo("<script> top.location.href='" . $dialog_url . "'</script>");
        exit;
}
	public function login1(){
		//print_r($_SERVER);exit;
		//echo getenv("HTTP_REFERER");exit;//和$_SERVER("HTTP_REFERER");一样的作用 
		//echo $_SERVER['HTTP_REFERER'];exit;
		//print_r($_GET);exit;
		$this->assign('citys',$this->city);
		if(isset($_GET['u'])){
				$uri = $_GET['u']+0;
			}else{
				$uri = '';
			}
	
		//$uri = base64_decode($_GET['u']);
		
		$this->assign('uris',$uri);
		//print_r($_SERVER);
		$this->display();
	}

	// 注册页面
	public function register(){
		$this->assign('citys',$this->city);
		$cid = $_GET['cid']?$_GET['cid']:3;
		$cname = D('qy')->where(['cid'=>$cid])->getField('qy_name');
		$this->assign('cname',$cname);
		$this->display();
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

 /*微信登录回调页*/
 public function wback(){
	// if(session('member.userid')){
	// 	session('member',null);
	// 	session('memberid',null);
	// 	session('auth',null);
 	//     }

 	$code = $_GET['code'];
 	$state = $_GET['state'];
 	$appid = C('appid');
 	$secret = C('appsecret');
 	$url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$appid}&secret={$secret}&code={$code}&grant_type=authorization_code";
 	$to_res = file_get_contents($url);
 	$to_res = json_decode($to_res,true);
 	if(!$to_res['errmsg']){
 		$token = $to_res['access_token'];
		$openid = $to_res['openid'];
	 	$uurl = "https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$openid}";
	 	$info_res = file_get_contents($uurl);
		$info_res = json_decode($info_res,true);
	 	if(!$info_res['errmsg']){
	 		$wuser = M('user');
	 		$same = $wuser->where(['openid'=>$info_res['openid']])->find();
	 		if(!$same){
	 			$wuser->mobile = substr($info_res['openid'],3,11);
	 			$wuser->openid = $info_res['openid'];
		 		$wuser->nick = $info_res['nickname'];
		 		$wuser->userlog = $info_res['headimgurl'];
		 		$wuser->path = $info_res['province'];
		 		$wuser->pubtime = time();
		 		if($info_res['sex'] == 1){
		 			$info_res['sex'] = "男";
		 		}else if($info_res['sex'] == 2){
		 			$info_res['sex'] = "女";
		 		}else{
		 			$info_res['sex'] = '';
		 		}
		 		$wuser->gender = $info_res['sex'];
		 		$wuser->add();
	 		}else{
				$wuser->userlog = $info_res['headimgurl'];
	 			$wuser->where(['openid'=>$info_res['openid']])->save();
			}
				
			$wres = $wuser->where(['openid'=>$info_res['openid']])->find();
			session('member',$wres);
			session('memberid',$wres['userid']);
			// session('wuser',$wres);
	 		// session('wid',$wres['wid']);
	 		// session('auth','wuser');
	 		if(!empty($state)){
	 			$state = urldecode($state);
	 			$this->redirect($state);
	 		}else{
	 			$this->redirect('/');
	 		}
	 		
	 	}else{
	 		$this->error($info_res['errmsg']);
	 	}	
	}else{
		$this->error($to_res['errmsg']);
	} 
 	
 }

// 手机端 微信 登录
 public function pwback(){
	
	// if(session('member.userid')){
	// 	session('member',null);
	// 	session('memberid',null);
	// 	session('auth',null);
 	//  }
	
 	$code = $_GET['code'];
 	$state = $_GET['state'];
 	$pappid = C('pappid');
 	$psecret = C('pappsecret');
 	$uri = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$pappid}&secret={$psecret}&code={$code}&grant_type=authorization_code";
  	$to_res = file_get_contents($uri);
  //	print_r($to_res);exit;
 	$to_res = json_decode($to_res,true);
 	if(!$to_res['errmsg']){
 		$token = $to_res['access_token'];
		$openid = $to_res['openid'];
	 	$uurl = "https://api.weixin.qq.com/sns/userinfo?access_token={$token}&openid={$openid}&lang=zh_CN";
	 	$info_res = file_get_contents($uurl);
	 	$info_res = json_decode($info_res,true);
	 	if(!$info_res['errmsg']){
	 		$wuser = M('user');
	 		$same = $wuser->where(['openid'=>$info_res['openid']])->find();

	 		if(!$same){
	 			$wuser->mobile = substr($info_res['openid'],3,11);
	 			$wuser->openid = $info_res['openid'];
		 		$wuser->nick = $info_res['nickname'];
		 		$wuser->userlog = $info_res['headimgurl'];
		 		$wuser->path = $info_res['province'];
		 		$wuser->pubtime = time();
		 		if($info_res['sex'] == 1){
		 			$info_res['sex'] = "男";
		 		}else if($info_res['sex'] == 2){
		 			$info_res['sex'] = "女";
		 		}else{
		 			$info_res['sex'] = '';
		 		}
		 		$wuser->gender = $info_res['sex'];
		 		$wuser->add();
	 		}else{
				$wuser->userlog = $info_res['headimgurl'];
	 			$wuser->where(['openid'=>$info_res['openid']])->save();
			}
			$wres = $wuser->where(['openid'=>$info_res['openid']])->find();
			session('member',$wres);
			session('memberid',$wres['userid']);
	 		if(!empty($state)){
	 			$state = urldecode($state);
	 			$this->redirect($state);
	 		}else{
	 			$this->redirect('/');
	 		}
	 	}else{
	 		$this->error($info_res['errmsg']);
	 	}

 	}else{
 		$this->error($to_res['errmsg']);
 	}
 }

 	// qq登录 回调
 	public function qqback(){
	 	$code = $_GET['code'];
	 	$state = $_GET['state'];
//	 	$qqid = C('qqid');
//	 	$qqsecret = C('qqsecret');
	 	$qqid = '101389568';
	 	$qqsecret = '22e6b2228675e39de3f5d558743ef2d2';
	 	$web = C('WEB');
//	 	$qqredirect = $web.U('/Home/Login/qqback');
	 	$qqredirect = "http://".$_SERVER['HTTP_HOST']."/home/login/qqback";

	 	// if($test !== $state){
	 	// 	$this->redirect('/');exit;
	 	// }
//	 	echo $_GET['code'];exit;
	 	$uri = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&client_id={$qqid}&client_secret={$qqsecret}&code={$code}&redirect_uri={$qqredirect}";
	 	$to_res = file_get_contents($uri);
	 	/*$to_res = json_decode($to_res);
//	 	 $access_pos = strrpos($to_res,'&');
	 	dump($to_res);
//	 	parse_str($to_res);
//        echo $access_token;
        die();
//	 	print_r($to_res);exit;*/
        if (strpos($to_res, "callback") !== false)
        {
            $lpos = strpos($to_res, "(");
            $rpos = strrpos($to_res, ")");
            $to_res  = substr($to_res, $lpos + 1, $rpos - $lpos -1);
            $msg = json_decode($to_res);
            if (isset($msg->error))
            {
                echo "<h3>error:</h3>" . $msg->error;
                echo "<h3>msg  :</h3>" . $msg->error_description;
                exit;
            }
        }

        //Step3：使用Access Token来获取用户的OpenID
        $params = array();
        parse_str($to_res, $params);

        /*$graph_url = "https://graph.qq.com/oauth2.0/me?access_token="
            .$params['access_token'];

        $str  = $this->get_contents($graph_url);
        if (strpos($str, "callback") !== false)
        {
            $lpos = strpos($str, "(");
            $rpos = strrpos($str, ")");
            $str  = substr($str, $lpos + 1, $rpos - $lpos -1);
        }
        $user = json_decode($str);
        if (isset($user->error))
        {
            echo "<h3>error:</h3>" . $user->error;
            echo "<h3>msg  :</h3>" . $user->error_description;
            exit;
        }

        echo $user;die();
        //获取到openid
        $openid = $user->openid;*/

//        dump($to_res);
//        dump($params);die();
        $token = $params['access_token'];
//        dump($token);
        $openurl = "https://graph.qq.com/oauth2.0/me?access_token={$token}";
        $info_res = file_get_contents($openurl);
        if (strpos($info_res, "callback") !== false)
        {
            $lpos = strpos($info_res, "(");
            $rpos = strrpos($info_res, ")");
            $info_res  = substr($info_res, $lpos + 1, $rpos - $lpos -1);
        }
        $user = json_decode($info_res);
        if (isset($user->error))
        {
            echo "<h3>error:</h3>" . $user->error;
            echo "<h3>msg  :</h3>" . $user->error_description;
            exit;
        }

//        echo $user;die();
        //获取到openid
        $openid = $user->openid;
         /*$pos = strrpos($info_res,'openid');
         $openid = substr($info_res,$pos);*/
//         echo $openid;die();
        $openurl = "https://graph.qq.com/user/get_user_info?access_token={$token}&oauth_consumer_key={$qqid}&openid={$openid}";
        $userinfo = file_get_contents($openurl);
        $userinfo = json_decode($userinfo,true);
//	 		dump($userinfo);die();
        $wuser = M('user');

        $same = $wuser->where(['openid'=>$userinfo['openid']])->find();

        if(!$same){
            $wuser->mobile = substr(shuffle($openid),3,11);
            $wuser->openid = $openid;
            $wuser->nick = $userinfo['nickname'];
            $wuser->userlog = $userinfo['figureurl_qq_2'];
            $wuser->path = $userinfo['province'];
            $wuser->pubtime = time();
            $wuser->gender = $userinfo['gender'];
            $wuser->add();
        }else{
            $wuser->userlog = $info_res['headimgurl'];
            $wuser->where(['openid'=>$openid])->save();
        }

        $wres = $wuser->where(['openid'=>$openid])->find();
        session('member',$wres);
        session('memberid',$wres['userid']);
        $this->redirect('/');

	 		
 	}
 	// 新浪微博登录
 	public function sinaback(){

 		$code = $_GET['code'];
 		$state = urldecode($_GET['state']);
 		$appkey = C('sinaid');
 		$appsecret = C('sinasecret');

 		$access_url = "https://api.weibo.com/oauth2/access_token";
 		$param = array(
 				'client_id'=>$appkey,
 				'client_secret'=>$appsecret,
 				'grant_type'=>'authorization_code',
 				'code'=>$code,
 				'redirect_uri'=>C('WEB').U('/home/login/sinaback'),
 			);
 		$param = http_build_query($param);
 		// echo $param;exit;
 		$ch = curl_init($access_url);
 		curl_setopt($ch,CURLOPT_POST,true);
 		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
 		curl_setopt($ch,CURLOPT_POSTFIELDS,$param);
 		$to_res = curl_exec($ch);
 		curl_close($ch);
 		// echo $to_res;exit;
 		
 		$to_res = json_decode($to_res,true);

 		$token = $to_res['access_token'];
 		$uid = $to_res['uid'];
 		// echo $token;exit;
 		// echo $uid;exit;
 		$user_url = "https://api.weibo.com/2/users/show.json?access_token={$token}&uid={$uid}";
 	
 		$info_res = file_get_contents($user_url);
 		
 		$info_res = json_decode($info_res,true);
		$wuser = M('user');
	 		$same = $wuser->where(['openid'=>$info_res['id']])->find();

	 		if(!$same){
	 			$wuser->mobile = $info_res['id'];
	 			$wuser->openid = $info_res['id'];
		 		$wuser->nick = $info_res['screen_name'];
		 		$wuser->userlog = $info_res['profile_image_url'];
		 		$wuser->path = $info_res['location'];
		 		$wuser->pubtime = time();
		 		if($info_res['gender'] == 'm'){
		 			$info_res['gender'] = "男";
		 		}else if($info_res['gender'] == 'f'){
		 			$info_res['gender'] = "女";
		 		}else{
		 			$info_res['gener'] = '未知';
		 		}
		 		$wuser->gender = $info_res['gender'];
		 		$wuser->add();
	 		}


			$wres = $wuser->where(['openid'=>$info_res['id']])->find();

			session('member',$wres);
			session('memberid',$wres['userid']);
			if(!empty($state)){
	 			// $state = urldecode($state);
	 			$this->redirect($state);
	 		}else{
	 			$this->redirect('Home/User/personal_Info');
	 		}
 	}

}

