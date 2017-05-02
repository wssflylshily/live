<?php namespace Home\Controller;
use Think\Controller;
use EasyWeChat\Foundation\Application;
use Home\Tool\AliyunTool;

class LiveController extends Controller {
    protected $city;

    // 遍历 直播城市
    public function __construct(){
        parent::__construct();
        $this->city = D('qy')->field(['qy_id','qy_name'])->select();
	
    }

    /**
     * 直播间
     */

    public function liveRoom(){
        $cid = $_GET['cid'];

        if(!$cid){
          $cid = session('cid');
        }

        $this->assign('citys',$this->city);
        $cname = D('qy')->where(['qy_id'=>$cid])->getField('qy_name');
        $this->assign('cname',$cname);  
        session('cid',$cid);
    	$m = M('video');
      $sum = $m->count();
    	$field = "v_id,zb_img as imgSrc,video_name as title,start_time as counttime";
    	$stardate = strtotime(I('stardate'));		//查询条件开始时间
    	if ($stardate){
    		$map['start_time'] = array('gt',$stardate);
    	}
    	$enddate = strtotime(I('enddate'));			//查询条件截止时间
    	if ($enddate){
    		$map['start_time'] = array('lt',$enddate);
    	}

      if($stardate&&$enddate){
        $map['start_time'] = array('between',array($stardate,$enddate));
      }
        
      $map['c_id'] = array('eq',$cid);
       $img_responsive1 = M('guanggao')->select();
      //print_r($map);exit;
       foreach ($img_responsive1 as $key => $value) {
           $img_responsive[$value['fenlei']] = $value;
       }
      $this->assign('img_responsive',$img_responsive);

    	$num = $m->where($map)->count();
      if($num <= 4){
        $this->assign('num',0);
      }else{
        $this->assign('num',$num);
      }

      $lists = $m->field($field)->where($map)->order('v_id DESC')->limit(4)->select();
      //echo $m->getLastsql();exit;
    	foreach ($lists as $k=>$v){
    		$lists[$k]['date'] = date("Y-m",$v['counttime']);
    		$lists[$k]['countTime'] = date('Y/m/d H:i:s',$v['counttime']);
    		$lists[$k]['url'] = U('Live/liveRoom_broadcast',array('id'=>$v['v_id']));
    	}
    	$list['data'] = $lists;
    	$this->assign('userModel',json_encode($list));
    	
    	$this->assign('stardate',$stardate);
    	$this->assign('enddate',$enddate);
    	$this->assign('cid',$cid);      // 获取城市限制条件
    	$this->display('liveroom');
    }
    
    /**
     * ajax获取直播间列表
     */
    public function ajaxGetLists(){

    	$p = (int)$_GET['page']?(int)$_GET['page']:2;
      $cid = (int)$_GET['cid'];
    	$stardate = $_GET['stardate'];
    	if ($stardate){
    		$map['start_time'] = array('gt',$stardate);
    	}
    	$enddate = $_GET['enddate'];			//查询条件截止时间
    	if ($enddate){
    		$map['start_time'] = array('lt',$enddate);
    	}
        $map['c_id'] = $cid;

    	$m = M('video');
       // 符合条件的数据总数
      $num = $m->where($map)->count();      
      $start = ($p-1)*4;
      // 剩下多少条
      $sheng = $num-$start;

        if($sheng <= 4){
          $list['num'] = 0;
        }else{
          $list['num'] = $sheng;
        }
      //

    	$field = "v_id,zb_img as imgsrc,video_name as title,start_time as counttime";
    	$lists = $m->field($field)->where($map)->order('v_id DESC')->limit($start,4)->select();
    	// print_r($lists);exit;
    	foreach ($lists as $k=>$v){
    		$lists[$k]['date'] = date("Y-m",$v['counttime']);
    		$lists[$k]['countTime'] = date('Y/m/d H:i:s',$v['counttime']);
    		$lists[$k]['url'] = U('Live/liveRoom_broadcast',array('id'=>$v['v_id']));
        $lists[$k]['imgSrc'] = $v['imgsrc'];
    	}
    	$list['data'] = $lists;
    	
    	echo json_encode($list);
    }
    
    /**
     * 精选视频
     */
    public function wonderful(){
      $this->assign('citys',$this->city);
      $cid = $_GET['cid'];

      if(!$cid){
        $cid = session('cid');
      }
      
      $cname = D('qy')->where(['qy_id'=>$cid])->getField('qy_name');
      $this->assign('cname',$cname);
      session('cid',$cid);
      
      // 添加 精彩视频
       $this->jxhis();
       //
    	$m = M('jx_video');
    	$field = "jx_id,jx_img as imgsrc,jx_name as title,start_time as date";
    	$map['jx_zs'] = 1;
    	$stardate = strtotime(I('stardate'));		//查询条件开始时间
    	if ($stardate){
    		$map['start_time'] = array('gt',$stardate);
    	}
    	$enddate = strtotime(I('enddate'));			//查询条件截止时间
    	if ($enddate){
    		$map['start_time'] = array('lt',$enddate);
    	}
       if($stardate&&$enddate){
        $map['start_time'] = array('between',array($stardate,$enddate));
      }

      $map['c_id'] = $cid;
      $map['url'] = array('neq','0');
      $map['delete_at'] = 0;
      $num = $m->where($map)->count();
      if($num <= 4){
        $this->assign('num',0);
      }else{
        $this->assign('num',$num);
      }

    	$lists = $m->field($field)->where($map)->order('jx_id DESC')->limit(4)->select();
    	
      // print_r($lists);exit;

    	foreach ($lists as $k=>$v){
    		$lists[$k]['url'] = U('Live/wonderful_broadcast',array('id'=>$v['jx_id']));
    		$lists[$k]['date'] = date('Y-m-d',$v['date']);
    	}

    	//     	print_r($lists);die;
    	$list['data'] = $lists;
    	$this->assign('userModel',json_encode($list));

    	$this->assign('stardate',$stardate);
    	$this->assign('enddate',$enddate);
      $this->assign('cid',$cid);
    	
       //广告
       $img_responsive1 = M('guanggao')->select();


       foreach ($img_responsive1 as $key => $value) {
           $img_responsive[$value['fenlei']] = $value;
       }
       $this->assign('img_responsive',$img_responsive);


    	$this->display('wonderful');
    }
    
    /**
     * ajax获取精选视频列表
     */
    public function ajaxGetWonder(){
      // 获取阿里直播后的精彩视频
       $this->jxhis();
       //

      $cid = (int)$_GET['cid']?(int)$_GET['cid']:3;
    	$p = (int)$_GET['page']?(int)$_GET['page']:2;
    	$start = ($p-1)*4;
    	$m = M('jx_video');
    	$field = "jx_id,jx_img as imgsrc,jx_name as title,start_time as date";
    	$map['jx_zs'] = 1;
    	$stardate = $_GET['stardate'];		//查询条件开始时间
    	if ($stardate){
    		$map['start_time'] = array('gt',$stardate);
    	}
    	$enddate = $_GET['enddate'];			//查询条件截止时间
    	if ($enddate){
    		$map['start_time'] = array('lt',$enddate);
    	}

      if($stardate&&$enddate){
        $map['start_time'] = array('between',array($stardate,$enddate));
      }
      $map['c_id'] = $cid;
      $map['url'] = array('neq','0');
      $map['delete_at'] = 0;
      $num = $m->where($map)->count();
      $sheng = $num - $start;

      if($sheng <= 4){
        $list['num'] = 0;
      }else{
        $list['num'] = $sheng;
      }

    	$lists = $m->field($field)->where($map)->order('jx_id DESC')->limit($start,4)->select();
    	
    	foreach ($lists as $k=>$v){
    		$lists[$k]['url'] = U('Live/wonderful_broadcast',array('id'=>$v['jx_id']));
    		$lists[$k]['date'] = date('Y-m-d',$v['date']);
    	}
    	$list['data'] = $lists;
    	echo json_encode($list);
    }

    // 微信验证
    public function wtest(){
        $options = [
                'debug'  => false,
                'app_id' => C('pappid'),
                'secret' => C('pappsecret'),
                'token'  => 'weixin',
                'aes_key'=>'AiFZz0E2h8HiMOUUF4it7X4f49211YZmiIuF0IrSr7z',
                'log' => [
                    'level' => 'debug',
                    'file'  => APP_PATH.'Runtime/Logs/easywechat.log', // XXX: 绝对路径！！！！
                ],
       ]; 
       $app = new Application($options);
       $js = $app->js;
       $arr = array('onMenuShareTimeline','onMenuShareAppMessage','onMenuShareQQ','onMenuShareWeibo');

      $config = $js->config($arr,false,false,false);
      unset($config['beta']);
      unset($config['url']);
      $config = json_encode($config);
      return $config;
    }

   public function liveRoom_broadcast(){

      // 微信js_sdk 
   $js = $this->wtest();
    $this->assign('jsconfig',$js);
    $this->assign('citys',$this->city);
      // 判断是不是分享链接 
      $uid = intval(I('get.uid'));
      $vid = I('get.id') + 0;
      // $auth = I('get.auth');
      $wfen = I('get.wfen');
      // print_r(session('member'));exit;
         if(!empty($uid)){
           $this->invite($uid,$vid);
            if($wfen){
                $pappid = C('pappid');
                $pappsecret = C('pappsecret');
                $state = urlencode(U('Home/Live/Liveroom_broadcast',['id'=>$vid,'fen'=>1]));
                $redirect = urlencode(C('WEB').U('Home/Login/pwback'));
                 $wfen_redirect = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$pappid}&redirect_uri={$redirect}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
               // echo $wfen_redirect;exit;
                header("Location:".$wfen_redirect);exit;
              }  
      	   
        } 
       
    	//print_r($_SESSION);
      // 登录与否   
       if(!session('member.userid')){
            $uri = $_GET['id'];
           	$this->redirect('Login/login',array('u'=>$uri));
       }
	/* if(!session('member.userid')){
          if(!empty($uid)){
            $uri = $_GET['id'];
            $this->redirect('Login/login',array('u'=>$uri,'fen'=>1));
          }else{
            $uri = $_GET['id'];
            $this->redirect('Login/login',array('u'=>$uri));
          }*/
       $time = time();
       
       $this->assign('time',$time);

        $id = intval($_GET['id']);

        if(!$id){
        	 exit('数据异常');
        }

        if($_GET['fen']){
          $this->assign('fen',1);
        }

        // 分享地址
        
          $fen_url = C('WEB').U('Home/Live/liveRoom_broadcast',['id'=>$id,'uid'=>session('member.userid'),'fen'=>1]);
          $wfen_url = C('WEB').U('Home/Live/liveRoom_broadcast',['id'=>$id,'uid'=>session('member.userid'),'wfen'=>1,'fen'=>1]);
      
        //   $fen_url = 'http://zhenghun.yicehua.cn'.U('Home/Live/liveRoom_broadcast',['id'=>$id,'uid'=>session('wid'),'auth'=>'wuser']);
        //   $wfen_url = 'http://zhenghun.yicehua.cn'.U('Home/Live/liveRoom_broadcast',['id'=>$id,'uid'=>session('member.userid'),'auth'=>'wuser','wfen'=>1]);
        // }

        // 微信分享地址
       

        // echo $fen_url;exit;
        $this->assign('fen_url',$fen_url);
        $this->assign('wfen_url',$wfen_url);
        // 微信分享地址
        // 展示祝福
       $zhufu_where['v_id'] = $id;
       $zhufu_where['userid'] =  session('member.userid');
  
        
       
       $zhufu_arr = M('zhufu')->where($zhufu_where)->select();	
       $zf_num = count($zhufu_arr);
       $this->assign('zf_num',1);
       
       //广告
       $img_responsive1 = M('guanggao')->select();


       foreach ($img_responsive1 as $key => $value) {
           $img_responsive[$value['fenlei']] = $value;
       }
       $this->assign('img_responsive',$img_responsive);

       //直播间中间轮播图
       $img_responsive2 = M('guanggao')->where('fenlei=6')->order('id desc')->limit(4)->select();
       $this->assign('img_responsive2',$img_responsive2);

       /****** 查询推流历史 ,查询精选视频表,精选视频表没有,推流历史有的添加进精选视频表****/
       // 可能传递应用名称 appname,没有限制条件(有就加上几个)
	
      // $data['number'] = $arr['number']+1;
	
        M('video')->where('v_id='.$id)->setInc('number');
        $arr = M('video')->where('v_id='.$id)->find();
        $watermark = $arr['watermark']?C('WEB').$arr['watermark']:'';
        $this->assign('watermark',$watermark);
        // print_r($arr);exit;
        // 查询索引文件接口
        $this->jxhis($arr['video_type'],$arr['video_file']);
        $condi['video_type'] = $arr['video_type'];
        $condi['video_file'] = $arr['video_file'];
        $condi['delete_at'] = 0;
        $historys = M('jx_video')->where($condi)->order('start_time desc')->select();

        foreach($historys as $k=>$v){
          $history_video[$k]['imgSrc'] = $v['jx_img'];
          $history_video[$k]['title'] = $v['jx_name'];
          $history_video[$k]['date'] = date('Y-m-d',$v['start_time']);
          $history_video[$k]['url'] = U('Home/Live/wonderful_broadcast',['id'=>$v['jx_id']]);
        }

        $hiss['data'] = $history_video;
        $this->assign('hiss',json_encode($hiss));
        $this->assign('cname',$arr['video_city']);
        $this->assign('arr',$arr);
        $this->assign('bodyBg',$arr['content_color']);
        $this->assign('tabBtnBg',$arr['button_color']);
        $this->assign('abFontColor',$arr['font_color']);
        // print_r($arr);exit;
        // 本站注册用户添加观看历史
        if(session('member.userid')){
            $history = M('guankan');
            $his['sp_id'] = $id;
            $his['sp_title'] = $arr['video_name'];
            $his['userid'] = session('member.userid');
            $his['gk_time'] = $time;
            $his['sp_img'] = $arr['zb_img'];
            $history->add($his);
        } 
	
        // 邀约查询
         $invs = M('invite');
        $where['vid'] = intval(I('get.id'));
        $invs = $invs->where($where)->order('inv_num desc')->select();
        // print_r($invs);exit;
        foreach($invs as $k=>$v){
            $in[$k]['uName'] = $v['uname'];
            $in[$k]['imgSrc'] = $v['uimg'];
            $in[$k]['uSum'] = $v['inv_num'];
            $in[$k]['ranking'] = $k + 1;
      }
      
      $ins['data'] = $in;
      $this->assign('invs',json_encode($ins));

      
      // 主持人信息
      $hosts = explode(',',$arr['z_id']);
      $host_where['zcr_id'] = array('in',$hosts);
      $field = array(
        'hl_zcr.*',
        'profession'=>'zhiye'
      );
      $zhuchi = M('zcr')->join('hl_host on hl_host.id = hl_zcr.hid')->field($field)->where($host_where)->select();

      // {hostname:"陈晓",imgSrc:"__PUBLIC__/home/img/hl_1_26.png",age:28,height:"180",major:"播音主持",times:1003,hostRate:5}
       if($zhuchi){
          foreach($zhuchi as $zk=>$zv){
                   $z_data[$zk]['hostname'] = $zv['zcr_name'];
                  $z_data[$zk]['imgSrc'] = $zv['zcr_img'];
                  $z_data[$zk]['hostRate'] = $zv['zcr_star'];
                  $z_data[$zk]['url'] = $zv['zcr_url'];
                  $z_data[$zk]['zhiye'] = $zv['zhiye'];
                  // print_r($z_data);exit;
                  
            }
            $host_data['data'] = $z_data;
            $this->assign('zhuchi',json_encode($host_data,JSON_UNESCAPED_UNICODE));
           $this->assign('res','1');

      }else{
        $host_data['data']['res'] = 0;
        $this->assign('zhuchi',json_encode($host_data,JSON_UNESCAPED_UNICODE));
        $this->assign('res','0');
      }
     
        //处理婚礼仪式
	  $arrs['coupleName'] = $arr['video_name'];
	  $arrs['date'] = date('Y-m-d',$arr['start_time']);
	  $i = 1;
	  for($i=1;$i<=11;$i++){
	             $key  = 'conTxt'.$i;

	             if($i==1){
	             	$m = 0;
	             	$mm = 'title';
	             }else{
	             	$m = $i-1;
	             	$mm = 'title'.$m;
	             }
	    	  $arrs[$key] = $arr[$mm];
	  }
	  
	  // print_r($arrs);exit;
	  $new_arrs['data'] = $arrs;

	  //print_r(json_encode($new_arrs));exit;


  	  $this->assign('arrs',json_encode($new_arrs));

 	  //获取婚礼所有祝福
       /*{wishuname:"贵妃BB",imgSrc:'img/liveroom_userLogo.png',txtCon:'十年修得同船渡，百年修得共枕眠。于茫茫人海中找到他/她，分明是千年前的一段缘；无数个偶然堆积而成的必然，怎能不是三生石上精心镌刻的结果呢？用真心呵护这份缘吧，真爱的你们。',date:'2015-06',time:'18:06'},*/

        $zhufu = M('zhufu')->where(['v_id'=>$id])->select();
        foreach($zhufu as $zkey=>$zvalue){
          $mark_where[] = $zvalue['pid'];
        }
        if($mark_where){
          $marks = M('user')->where(['userid'=>array('in',$mark_where)])->field(['userid','nick'])->select();

          foreach($marks as $mk=>$mv){
            $marks_data[$mv['userid']] = $mv['nick'];
          }
        }
        

        foreach ($zhufu as $key => $value) {
            $fan_arr['data'][$key]['wishuname'] = $value['nick'];
            $fan_arr['data'][$key]['imgSrc'] = $value['img'];
            $fan_arr['data'][$key]['txtCon'] = $value['text'];
            //date":"2015-06","time":"18:06"
            $fan_arr['data'][$key]['date'] = date('Y-m-d',$value['time']);
            $fan_arr['data'][$key]['time'] = date('H:i:s',$value['time']);
            $fan_arr['data'][$key]['uid'] = $value['userid'];
            if($marks_data[$value['pid']]){
                 $fan_arr['data'][$key]['markName'] = $marks_data[$value['pid']];
            }else{
              $fan_arr['data'][$key]['markName'] = '';
            }
        }

        // print_r($zhufu);exit;
        $this->assign('wish',json_encode($fan_arr));
        $this->assign('time',time());
        $this->display('liveRoom_broadcast');
    }

    //判断安全码
    public function get_code(){

  
    	
    	$vid = intval($_POST['vid']);
    	$code  = $_POST['code'];
    	$arr = M('video')->where('v_id='.$vid)->find();

    	//print_r($arr);

    	if($arr['safe_code']==$code){

    		$data['safeValiState'] = 1;

    	}else{

    		$data['safeValiState'] = 0;

    	}

    	print_r(json_encode($data));exit;


    	//exit;
    }



    //判断是否显示安全码
    public function get_code1(){


	$vid = intval($_GET['id']);
	$arr = M('video')->where('v_id='.$vid)->find();

	// print_r($arr);exit;

	if($arr['safe_code']){

		$data['safeCode'] = 1;

	}else{

		$data['safeCode'] = 0;

	}

	print_r(json_encode($data));exit;


    	//exit;
    }



    //判断是否显示点播安全码
    public function get_code2(){

    	// print_r($_GET);exit;

	$jxid = intval($_GET['id']);
	$arr = M('jx_video')->where('jx_id='.$jxid)->find();

	//print_r($arr);exit;

	if($arr['safe_code']){

		$data['safeCode'] = 1;

	}else{

		$data['safeCode'] = 0;

	}

	print_r(json_encode($data));exit;


    	//exit;
    }



    //判断点播安全码
    public function get_code3(){

	$jxid = intval($_POST['jxid']);
	$code  = $_POST['code'];
	$arr = M('jx_video')->where('jx_id='.$jxid)->find();

	//print_r($arr);

	if($arr['safe_code']==$code){

		$data['safeValiState'] = 1;

	}else{

		$data['safeValiState'] = 0;

	}

	print_r(json_encode($data));exit;

  }




   //点赞
   public function zan(){

   	$id = $_GET['id']+0;
    
   	$where['v_id'] = $id;
		M("video")->where('v_id='.$id)->setInc('zan_num');
   		exit("0");
   }



   //送祝福
   public function save_zf(){

   	//print_r(session());
   
      $id = session('member.userid');
      $arr = M('user')->where('userid='.$id)->find();
      $where['userid'] = $id;

    // }else{
    //   $id = session('wid');
    //   $arr = M('wuser')->where('wid='.$id)->find();
    //   $where['wid'] = $id;
    // }
    
   	$vid = $_POST['vid'];
   	$where['v_id'] = $vid;

   	$arrs = M('zhufu')->where($where)->select();

    
        $data['userid'] = session('member.userid');
        $data['img'] = $arr['userlog']?$arr['userlog']:'/Public/Home/img/default.jpg';
       $data['nick'] = $arr['nick'];
      
	   	$data['v_id'] = $_POST['vid']+0;
	   	$data['text'] = $_POST['wish'];
      $data['pid'] = $_POST['pid']?$_POST['pid']:0;
	   	$data['time'] = time();
	   	M('zhufu')->add($data);
      
   	$now_num = count($arrs)+1;
   	$fan_arr['wishuname'] = $data['nick']?$data['nick']:'匿名用户';
   	$fan_arr['imgSrc'] = $data['img']?$data['img']:"/Public/Home/img/default.jpg";
   	$fan_arr['txtCon'] = $data['text'];
   	//date":"2015-06","time":"18:06"
   	$fan_arr['date'] = date('Y-m-d',$data['time']);
   	$fan_arr['time'] = date('H:i:s',$data['time']);
   	$fan_arr['now_num']  = 1;
    $fan_arr['uid'] = $data['userid'];
    if($data['pid']){
      $fan_arr['markName'] =  M('user')->where(['userid'=>$data['pid']])->getField('nick');
    }else{
      $fan_arr['markName'] = '';
    }
    
   	//$new_fan['data'] = $fan_arr;

   	echo json_encode($fan_arr);
   }

   //获取祝福
   public function get_sj(){

   	$where['time'] = array('gt',$_POST['time']+0);
   	$where['v_id'] = $_POST['vid']+0;
   	$where['_logic'] = 'AND';

  
      $where['userid'] = array('neq',session('member.userid'));
    
   	 // print_r($where);

   	$arr['data'] = M('zhufu')->where($where)->select();

    if(empty($arr['data'])){
       $arr['time'] = time();
       $arr['status'] = 'true';
    }else{
          foreach ($arr['data'] as $key => $value) {
            $arr['data'][$key]['imgSrc'] = $value['img']?$value['img']:"/Public/Home/img/default.jpg";
            $arr['data'][$key]['wishuname'] = $value['nick'];
            $arr['data'][$key]['txtCon'] = $value['text'];
            $arr['data'][$key]['date'] = date('Y-m-d',$value['time']);
            $arr['data'][$key]['time'] = date('H:i:s',$value['time']);
            $arr['time'] = time();
            $arr['status'] = 'false';
        }
   }
    echo (json_encode($arr));exit();
}
   //获取目的地
   public function  get_mdd(){
    //exit('3');
   	$id =  $_POST['vid']+0;
   	$arr = M('video')->where('v_id='.$id)->find();

   	$data['liveTargetPlace'] = $arr['video_address'];
   	$data['city'] = $arr['video_city'];
   	echo json_encode($data);exit;
   }


   public function lsdb(){

   	  if(!session('member.userid')){
       	   redirect('Home/Login/login');
       }


       $time = time();

       $this->assign('time',$time);

        $id = intval($_GET['id']);
        if(!$id){
        	 exit('数据异常');
        }

       $zhufu_where['v_id'] = $id;
       $zhufu_where['userid'] =  session('member.userid');

       $zhufu_arr = M('zhufu')->where($zhufu_where)->select();	

       $zf_num = count($zhufu_arr);

       $this->assign('zf_num',$zf_num);

        $arr = M('video')->where('v_id='.$id)->find();

        $data['number'] = $arr['number']+1;
        M('video')->where('v_id='.$id)->save($data);
        $this->assign('arr',$arr);


        /*
        "WeddingProcedure":[
    {"coupleName":"凌先生和云小姐","date":"2016-06-06",
      "conTxt1":"婚礼仪式",
      "conTxt2":"1、新人入场（新人手挽手踏上红地毯。步速慢，接受来宾的祝福。新人事先安排好在红地毯两侧放礼宾花的来宾），乐队演奏，音乐响起，宾客鼓掌欢呼。",
      "conTxt3":"2、新人共同入场。新娘挽着父亲的臂膀入场，新郎在红地毯的1/3 处等待，再由父亲把女儿交给新郎，新人共同入场。（西式教堂式）",
      "conTxt4":"3、新娘拿手捧花入场，新郎在红地毯1/3处等待，新郎单腿跪下向新娘求婚，新娘把手捧花中最鲜艳的一朵玫瑰插入新郎衣服胸口的口袋，新人共同入场。（最浪漫的）",
      "conTxt5":"4、司仪简单介绍一下新人（新人目前的工作职务。司仪提问，新人现在的心情）",
      "conTxt6":"5、证婚人证婚",
      "conTxt7":"6、感恩仪式（父母仪式）",
      "conTxt8":"7、父母代表致辞",
      "conTxt9":"8、交换爱情信物（伴娘送上，新郎先为新娘戴戒指，新娘再为新郎戴戒指。戒指是慢慢的移动戴入，摄影师要拍一个特写的。戴上戒指后，向来宾展示一下。新郎亲吻新娘）",
      "conTxt10":"9、倒香槟美酒。（两个人一起倒香槟酒，倒的时候慢一点，幅度小一些，免得酒杯破碎。）",
      "conTxt11":"10、喝交杯酒 （两人手挽手喝交杯酒，饮尽酒杯中的香槟。慢，一个过程。）"
    }
  ],
         */
        
        //处理婚礼仪式
	  $arrs['coupleName'] = $arr['video_name'];
	  $arrs['date'] = date('Y-m-d',$arr['start_time']);
	  $i = 1;
	  for($i=1;$i<=11;$i++){
	             $key  = 'conTxt'.$i;

	             if($i==1){
	             	$m = 0;
	             	$mm = 'title';
	             }else{
	             	$m = $i-1;
	             	$mm = 'title'.$m;
	             }
	    	  $arrs[$key] = $arr[$mm];
	  }
	  
	  // print_r($arrs);exit;
	  $new_arrs['data'] = $arrs;

	  //print_r(json_encode($new_arrs));exit;


  	  $this->assign('arrs',json_encode($new_arrs));

 	//处理婚礼仪式结束
  	 /*
  	 处理精彩视频
  	  */


  	 /*
  	 处理精彩视频结束
  	  */


        $this->display();

   }



   //点播详情页
   public function wonderful_broadcast(){

      
      $uid = intval(I('get.uid'));
      $vid = I('get.id') + 0;
      // $auth = I('get.auth');
      $wfen = I('get.wfen');
      if($wfen){
          $pappid = C('pappid');
          $pappsecret = C('pappsecret');
          $state = urlencode(U('Home/Live/wonderful_broadcast',['id'=>$vid,'fen'=>1]));
          $redirect = urlencode(C('WEB').U('Home/Login/pwback'));
           $wfen_redirect = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$pappid}&redirect_uri={$redirect}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
         // echo $wfen_redirect;exit;
          header("Location:".$wfen_redirect);exit;
      }

    $js = $this->wtest();
    $this->assign('jsconfig',$js);

    $this->assign('citys',$this->city);
    $cid = $_GET['cid']?$_GET['cid']:'3';
    $cname = D('qy')->where(['qy_id'=>$cid])->getField('qy_name');
    $this->assign('cname',$cname);
    $time = time();

       $this->assign('time',$time); 

       // 登录与否   
       if(!session('member.userid')){
            $uri = $_GET['id'];
            $this->redirect('Login/login',array('d'=>$uri));
       }

        $id = intval($_GET['id']);
        if(!$id){
        	 exit('数据异常');
        }

       if(isset($_GET['fen'])){
          $this->assign('fen',1);
       }  
      
      $wfen_url = C('WEB').U('Home/Live/wonderful_broadcast',['id'=>$id,'uid'=>session('member.userid'),'wfen'=>1,'fen'=>1]);
       

        $this->assign('wfen_url',$wfen_url);
        // 精选视频

        $arr = M('jx_video')->where('jx_id='.$id)->find(); 

        //print_r($arr);exit;
        $this->assign('arr',$arr);
	      $jxinfo = M('video')->where(['v_id'=>$arr['v_id']])->field(['info','watermark'])->find();
        $this->assign('jxinfo',$jxinfo['info']);
        $watermark = $jxinfo['watermark']?C('WEB').$jxinfo['watermark']:'';
        $this->assign('watermark',$watermark);
        // 点赞和观看数
        $ren_number = M('video')->where(['v_id'=>$arr['v_id']])->field(['number','zan_num'])->find();
        $this->assign("number",$ren_number);

        /*********获取精彩视频 可能传递appname 值********/
  
      //	print_r($this->jxhis);exit;
        $historys = M('jx_video')->where(['jx_id'=>array('neq',$id),'video_type'=>$arr['video_type'],'video_file'=>$arr['video_file'],'delete_at'=>0])->order('start_time desc')->select();
        $n=0;
        foreach($historys as $k=>$v){
          $n++;
          $history[$k]['imgSrc'] = $v['jx_img'];
          $history[$k]['title'] = $v['jx_name'];
          $history[$k]['date'] = date('Y-m-d',$v['start_time']);
          $history[$k]['url'] = U('Home/Live/wonderful_broadcast',['id'=>$v['jx_id']]);
        }

        $hiss['data'] = $history;
        $this->assign('hiss',json_encode($hiss));
        /************end**************/

        //获取婚礼所有祝福
        $zhufu = M('zhufu')->where(['v_id'=>$arr['v_id']])->select();
        foreach($zhufu as $zkey=>$zvalue){
          $mark_where[] = $zvalue['pid'];
        }

        if($mark_where){
          $marks = M('user')->where(['userid'=>array('in',$mark_where)])->field(['userid','nick'])->select();

          foreach($marks as $mk=>$mv){
            $marks_data[$mv['userid']] = $mv['nick'];
          }
        }

        foreach ($zhufu as $key => $value) {
        		$fan_arr['data'][$key]['wishuname'] = $value['nick'];
      	   	$fan_arr['data'][$key]['imgSrc'] = $value['img'];
      	   	$fan_arr['data'][$key]['txtCon'] = $value['text'];
      	   	//date":"2015-06","time":"18:06"
      	   	$fan_arr['data'][$key]['date'] = date('Y-m-d',$value['time']);
      	   	$fan_arr['data'][$key]['time'] = date('H:i:s',$value['time']);
            
            if($marks_data[$value['pid']]){
               $fan_arr['data'][$key]['markName'] = $marks_data[$value['pid']];
            }else{
              $fan_arr['data'][$key]['markName'] = '';
            }
           
        }

        
        $this->assign('fan_arr',json_encode($fan_arr));
      
       // 邀约查询
         $invs = M('invite');
        $where['vid'] = $arr['v_id'];
        $invs = $invs->where($where)->order('inv_num desc')->select();
        foreach($invs as $k=>$v){
            $in[$k]['uName'] = $v['uname'];
            $in[$k]['imgSrc'] = $v['uimg'];
            $in[$k]['uSum'] = $v['inv_num'];
            $in[$k]['ranking'] = $k + 1;
      }
      $ins['data'] = $in;
      $this->assign('invs',json_encode($ins));     
       //广告
       $img_responsive1 = M('guanggao')->select();


       foreach ($img_responsive1 as $key => $value) {
           $img_responsive[$value['fenlei']] = $value;
       }
       $this->assign('img_responsive',$img_responsive);

       //精彩间中间轮播图
       $img_responsive2 = M('guanggao')->where('fenlei=8')->order('id desc')->limit(4)->select();
       $this->assign('img_responsive2',$img_responsive2);

        //处理婚礼仪式
	  $arrs['coupleName'] = $arr['video_name'];
	  $arrs['date'] = date('Y-m-d',$arr['start_time']);
	  $i = 1;
	  for($i=1;$i<=11;$i++){
	             $key  = 'conTxt'.$i;

	             if($i==1){
	             	$m = 0;
	             	$mm = 'title';
	             }else{
	             	$m = $i-1;
	             	$mm = 'title'.$m;
	             }
	    	  $arrs[$key] = $arr1[$mm];
	  }
	  
	  //print_r($arrs);exit;
	  $new_arrs['data'] = $arrs;

	  $this->assign('yishi',json_encode($new_arrs));
	  //print_r(json_encode($new_arrs));exit;


  	  //$this->assign('arrs',json_encode($new_arrs));

 	//处理婚礼仪式结束
  	 /*
  	 处理精彩视频
  	  */

  	 for ($t=1; $t < 4; $t++) { 

  	 	$title = 'sp'.$t;
  	 	$ms = 'sp'.$t.'_ms';
  	 	$tk = 'sp'.$t.'_uu';
  	 	$tk1 =  'sp'.$t.'_vu';
  	 	$tk2 =  'sp'.$t.'_pu';

  	 	$img = "sp".$t.'_img';

      if($arr1[$tk]){
          $jc_arr['otherVideos'][$t-1][$tk] = $arr1[$tk]?$arr1[$tk]:'';
          $jc_arr['otherVideos'][$t-1][$tk1] = $arr1[$tk1]?$arr1[$tk1]:'';
          $jc_arr['otherVideos'][$t-1][$tk2] = $arr1[$tk2]?$arr1[$tk2]:'';
          $jc_arr['otherVideos'][$t-1]['imgSrc'] = $arr1[$img]?$arr1[$img]:'';
          $jc_arr['otherVideos'][$t-1]['title'] = $arr1[$title]?$arr1[$title]:'';
          $jc_arr['otherVideos'][$t-1]['conTxt'] = $arr1[$ms]?$arr1[$ms]:'';
      } 


  // 	 	$jc_arr['otherVideos'][$t-1][$tk] = $arr1[$tk];
  // 	 	$jc_arr['otherVideos'][$t-1][$tk1] = $arr1[$tk1];
  // 	 	$jc_arr['otherVideos'][$t-1][$tk2] = $arr1[$tk2];
		// $jc_arr['otherVideos'][$t-1]['imgSrc'] = $arr1[$img];
		// $jc_arr['otherVideos'][$t-1]['title'] = $arr1[$title];
		// $jc_arr['otherVideos'][$t-1]['conTxt'] = $arr1[$ms];
  	 }


  	 $this->assign('jc_arr',json_encode($jc_arr));
  	 $this->display('wonderful_broadcast');
   }

   // 邀约处理函数  
   protected function invite($uid,$vid){
       $invs = M('invite');
       $videos = M('video');
       $users = M('user');
       // $wuser = M('wuser');
       
       $where['vid'] = $vid;
      
      $where['uid'] = $uid;
       
       
       $res = $invs->where($where)->find();
       // print_r($res);
       if($res){
           $invs->where($where)->setInc('inv_num');
           $invs->where($where)->save(['inv_time'=>time()]);
       }else{
            
            $map['uid'] = $uid;
            $regu = $users->field(['nick','userlog'])->where(['userid'=>$uid])->find();
            $map['uname'] = $regu['nick'];
            $map['uimg'] = $regu['userlog'];
            $map['vid'] = $vid;
            $fv = $videos->where(['v_id'=>$vid])->field(['video_name','zb_img'])->find();
            $map['v_title'] = $fv['video_name'];
            $map['v_img'] = $fv['zb_img'];
            $map['inv_num'] = 1;
            $map['inv_time'] = time();
       	   $invs->add($map);

	     }       

   }

   // 精彩视频处理
   public function jxhis($appname,$streamname){
      $endtime = time();

       $ali = new AliyunTool();
       $jxvideo = M('jx_video');
       $videos = M('video');
       $video = $videos->where(['video_type'=>$appname,'video_file'=>$streamname])->find();
       // 
       $his_files = M('jx_video')->where(['video_type'=>$appname,'video_file'=>$streamname,'type'=>1])->select();
       if(!empty($his_files)){
          foreach($his_files as $k=>$v){
            $times[] = $v['start_time'];
          }
       }

       // 查询索引文件
       $res = $ali->queryreindex($streamname,$appname);
       $res = $res['RecordIndexInfoList']['RecordIndexInfo'];

       foreach($res as $k=>$v){
          $res[$k]['order'] = $k+1;
       }

       if(!empty($res)){
          foreach($res as $k=>$v){
            $starttime[] = strtotime($v['StartTime']);
         }
       }

      $unres = $res;
       // 找到数据库中已有的数据
       if(!empty($times) && !empty($starttime)){
          $same = array_intersect($starttime, $times);
          if(!empty($same)){
            foreach($same as $sk=>$sv){
               foreach($unres as $rk=>$rv){
                  if(strtotime($rv['StartTime']) == $sv){
                    unset($unres[$rk]);
                  }
               }
            }
          }
       }
       $data = array();
       if(!empty($unres)){
           foreach($unres as $k=>$v){
            $n++;
            $arr['jx_name'] = $video['video_name'].'-第'.$v['order'].'部分';
            $arr['jx_zs'] = $video['zb_zs'];
            $arr['safe_code'] = $video['safe_code'];
            $arr['jx_img'] = $video['zb_img'];
            $arr['video_file'] = $streamname;
            $arr['video_type'] = $appname;
            $arr['v_id'] = $video['v_id'];
            $arr['c_id'] = $video['c_id'];
            $arr['start_time'] = strtotime($v['StartTime']);
            $arr['end_time'] = strtotime($v['EndTime']);
            $arr['url'] = $v['RecordUrl'];
            $arr['a_id'] = $video['a_id'];
            $data[] = $arr;
           }
       }

       if(!empty($data)){
          $jxvideo->addAll($data);  
       }
       
       return $res;
   }

   public function ajaxJxhis(){
        $vid = I('get.id');
        $endtime = time();
        
       $ali = new AliyunTool();
       $jxvideo = M('jx_video');
       $videos = M('video');
       $video = $videos->where(['v_id'=>$vid])->find();
        $streamname = $video['video_file'];
        $appname = $video['video_type'];

       // 
       $his_files = M('jx_video')->where(['video_type'=>$appname,'video_file'=>$streamname,'type'=>1])->select();
       if(!empty($his_files)){
          foreach($his_files as $k=>$v){
            $times[] = $v['start_time'];
          }
       }
       
       // 查询索引文件
       $res = $ali->queryreindex($streamname,$appname);
       $res = $res['RecordIndexInfoList']['RecordIndexInfo'];

       foreach($res as $k=>$v){
          $res[$k]['order'] = $k+1;
       }

       if(!empty($res)){
          foreach($res as $k=>$v){
            $starttime[] = strtotime($v['StartTime']);
         }
       }else{
          $hiss['status'] = 0;
          echo json_encode($hiss);exit;
       }

       // 找到数据库中已有的数据
       if(!empty($times) && !empty($starttime)){
          $same = array_intersect($starttime, $times);
          if(!empty($same)){
            foreach($same as $sk=>$sv){
               foreach($res as $rk=>$rv){
                  if(strtotime($rv['StartTime']) == $sv){
                    unset($res[$rk]);
                  }
               }
            }
          }
       }
       $data = array();
      
       if(!empty($res)){
           foreach($res as $k=>$v){
            $arr['jx_name'] = $video['video_name'].'-第'.$v['order'].'部分';
            $arr['jx_zs'] = $video['zb_zs'];
            $arr['safe_code'] = $video['safe_code'];
            $arr['jx_img'] = $video['zb_img'];
            $arr['video_file'] = $streamname;
            $arr['video_type'] = $appname;
            $arr['v_id'] = $video['v_id'];
            $arr['c_id'] = $video['c_id'];
            $arr['start_time'] = strtotime($v['StartTime']);
            $arr['end_time'] = strtotime($v['EndTime']);
            $arr['url'] = $v['RecordUrl'];
            $arr['a_id'] = $video['a_id'];
            $data[] = $arr;
           }
       }else{
          $hiss['status'] = 0;
          echo json_encode($hiss);exit;
       }

       if(!empty($data)){
          $ok = $jxvideo->addAll($data);
          if($ok){
                  $condi['video_file'] = $streamname;
                  $condi['video_type'] = $appname;
                  // $condi['order'] = array('gt',$sum);
                  $condi['delete_at'] = 0;

                  $historys= $jxvideo->where($condi)->order('start_time desc')->select();

                foreach($historys as $k=>$v){
                  $history[$k]['imgSrc'] = $v['jx_img'];
                  $history[$k]['title'] = $v['jx_name'];
                  $history[$k]['date'] = date('Y-m-d',$v['start_time']);
                  $history[$k]['url'] = U('Home/Live/wonderful_broadcast',['id'=>$v['jx_id']]);
                }
              $hiss['status'] = 1;
              $hiss['data'] = $history;
          }else{
              $hiss['status'] = 0;
          }
       }else{
          $hiss['status'] = 0;
       }    
          echo json_encode($hiss);
   }

   //测试阿里接口
   public function test(){
      $ali = new \Home\Tool\AliyunTool();
      $endtime = time();
      $res = $ali->queryreindex('yinongchang');
      echo '<pre>';
      print_r($res);
   }

}
