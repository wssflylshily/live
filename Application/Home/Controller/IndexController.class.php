<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    protected $city;

    // 遍历 直播城市
    public function __construct(){
        parent::__construct();
        $this->city = D('qy')->field(['qy_id','qy_name'])->select();
    }
    
    // 获取ip
    public function getrealip(){
        if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"])
        {
          $ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];
        }
        elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"])
        {
          $ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];
        }
        elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"])
        {
          $ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];
        }
        elseif (getenv("HTTP_X_FORWARDED_FOR"))
        {
          $ip = getenv("HTTP_X_FORWARDED_FOR");
        }
        elseif (getenv("HTTP_CLIENT_IP"))
        {
          $ip = getenv("HTTP_CLIENT_IP");
        }
        elseif (getenv("REMOTE_ADDR"))
        {
          $ip = getenv("REMOTE_ADDR");
        }
        else
        {
          $ip = false;
        }
        return $ip ;
        // echo $ip;
    }
    
    // 获取地理位置
    public function getsite(){
        $ip = $this->getrealip();
        $ipurl = C('ipurl');
        $ipurl = $ipurl.'&ip='.$ip;
        $res = file_get_contents($ipurl);
        $res = json_decode($res,true);
        // print_r($res);exit;
        $site = $res['content']['address_detail']['city'];
        $pos = mb_strrpos($site,'市');
        $site = mb_substr($site,0,$pos);
        // echo $site;
        return $site;
    }

    public function index(){
        $citys = $this->city;
        foreach($citys as $k=>$v){
            $city_names[] =$v['qy_name'];
        }
        // print_r($city_names);exit;
        // $lng = $_POST('lng'); 
        // $lat = $_POST('lat');
        // echo $lng;exit;
        // 获取区域值 ，默认北京 ，可以修改默认  
        $cid = $_GET['cid'];
        // $city = $_GET['city'];
        // echo $cid;
        $scid = session('cid');

        if(!$cid && !$scid){ 
            $site = session('cname'); 
            
            if($site){
                $cid = D('qy')->where(['qy_name'=>$site])->getField('qy_id');
                $this->assign('cname',$site);
            }else{
                $site = $this->getsite(); 
                if(in_array($site,$city_names)){
                    $cid = D('qy')->where(['qy_name'=>$site])->getField('qy_id');
                    $this->assign('cname',$site);
                    $mourl = U('Home/Index/index',['cid'=>3]);
                    $this->assign('mourl',$mourl);
                    session(array('expire'=>3600));
                    session('cname',$site);  
                    $this->assign('show',1); 
                }else{
                    $cid = 3;
                    $cname = M('qy')->where(['qy_id'=>$cid])->getField('qy_name');
                    $this->assign('cname',$cname);
                } 
            }
            
        }else if($cid){
            $cname = M('qy')->where(['qy_id'=>$cid])->getField('qy_name');
            $this->assign('cname',$cname);
        }else if($scid){
            $cid = $scid;
            $cname = M('qy')->where(['qy_id'=>$cid])->getField('qy_name');
            $this->assign('cname',$cname);
        }
        
        $this->assign('citys',$this->city);
        
        session('cid',$cid);
        /*-----------------------------视频-----------------------------------*/
        // 是否在直播
        // $ali = new \Home\Tool\AliyunTool();
        // $res = $ali->queryactive(C('appname'));
        // if(!empty($res['OnlineInfo']['LiveStreamOnlineInfo'])){
        //     foreach($res['OnlineInfo']['LiveStreamOnlineInfo'] as $k=>$v){
        //         // $arr['StreamName'] = $v['StreamName'];
        //         // $arr['PublishUrl'] = $v['PublishUrl'];
        //         // $arr['PublishTime'] = $v['PublishTime'];
        //         $active[] = $v['StreamName'];
        //     }
        // }
        
        $mRankings = M('video')->field('*')->where(['c_id'=>$cid])->order('sort desc')->limit(4)->select();  
        
        foreach ($mRankings as $key => $value) {
                //排行  mRankings{imgSrc:'img/hl_show_1_24.png',towsName:'萧先生&陆小姐',rate:1}
                //直播间   lives{imgSrc:'img/hl_1_15.png',title:'美好美好美好',date:'2015-06'}
                //精彩视频  videos{imgSrc:'img/hl_1_15.png',title:'美好美好美好',date:'2015-06'}
                // rate => 是否有名次，只有前3有名次
                if($key<3){
                    $mRankings[$key]['rate'] = $key+1;
                }else{
                     $mRankings[$key]['rate'] = 0;
                }
                $mRankings[$key]['imgSrc'] = $value['zb_img']; 
                $mRankings[$key]['towsName'] = $value['video_name'];
                $mRankings[$key]['v_id'] = $value['v_id'];

            }

            $arr = M('jx_video')->field('*')->where(['c_id'=>$cid])->order('start_time desc')->limit(4)->select();  
                foreach ($arr as $key => $value) {
                        $videos[$key]['jx_id'] = $value['jx_id']; 
                        $videos[$key]['imgSrc'] = $value['jx_img']; 
                        $videos[$key]['title'] = $value['jx_name'];
                        $videos[$key]['date'] = date('Y-m-d',$value['start_time']); 
                }


            $live = M('video')->where(['c_id'=>$cid])->order('v_id desc')->limit(4)->select();    
            foreach($live as $lk=>$lv){
                $lives[$lk]['v_id'] = $lv['v_id'];
                $lives[$lk]['imgSrc'] = $lv['zb_img'];
                $lives[$lk]['title'] = $lv['video_name'];
                $lives[$lk]['date'] = date('Y-m-d',$lv['start_time']);
            }
                
                //获取广告
                $guanggao_arr  = M('guanggao')->select();
                
                $this->assign('guanggao',$guanggao_arr);



      //print_r($mRankings);exit;



        //print_r($videos);exit;
        $rankings['data'] = $mRankings;
        $videos['data'] = $videos;
        $lives['data'] = $lives;
        $this->assign('rankings',json_encode($rankings));
        $this->assign('videos',json_encode($videos));
        $this->assign('lives',json_encode($lives));
        
        /*-----------------------------轮播图-----------------------------------*/
        $mCarouselImg = M('lunbo')->limit(4)->select();
        //print_r($mCarouselImg);exit;
        foreach($mCarouselImg as $key => $value){
            //{bigImgSrc,smallImgSrc:,txt:'缘来是你',date:'2015-06',addr:'9号公馆',focusNum:'0',toggle:true}
            if($key == 0){
                $mCarouselImg[$key]['toggle'] = true;
                $mCarouselImg[$key]['focusNum'] = 0;
            }else{
                $mCarouselImg[$key]['toggle'] = false;
                $mCarouselImg[$key]['focusNum'] = $key;
            }
            $mCarouselImg[$key]['bigImgSrc'] = $value['big_img'];
            $mCarouselImg[$key]['smallImgSrc'] = $value['small_img'];
            $mCarouselImg[$key]['txt'] = $value['imgname'];
            $mCarouselImg[$key]['date'] = $value['livetime'];
            $mCarouselImg[$key]['url'] = $value['url'];
        }
        
        $carouselImg['data'] = $mCarouselImg;
        $this->assign('carouselImg',json_encode($carouselImg));

        /*-----------------------------广告图-----------------------------------*/
       $img_responsive1 = M('guanggao')->select();

       foreach ($img_responsive1 as $key => $value) {
           $img_responsive[$value['fenlei']] = $value;
       }

        $this->assign('img_responsive',$img_responsive);

        //取值参考类似 广告如下
        /*
        '1'=>'主页 banner广告一 1100*140 ',
        '2'=>'主页 底部广告展示图 1100*140 ',
        '3'=>'主页 直播部分左一展示广告 260*500',
        '4'=>'主页 左右2侧广告图 210*510',
        '5'=>'直播间首页顶部主体广告展示图( PC大屏1900*499,移动小屏幕1080*460 需要传2张图)',
        '6'=>'直播间部分 中部广告展示图 1060*200',
        '7'=>'精选视频顶部主体广告展示图( PC大屏1900*499,移动小屏幕1080*460 需要传2张图)',
        '8'=>'精选视频（点播）播放部分 中部广告展示图：1060*200',
         */





        /*-----------------------------主持热榜-----------------------------------*/
        $compere = M('zcr')->order('zcr_star desc')->limit(6)->select();
        $compere = array_reverse($compere);
        // 明星主持 services{imgSrc:'img/hl_1_26.png',hostRate:5},
         foreach($compere as $key => $value){
            $compere[$key]['imgSrc'] = $value['zcr_img'];
            $compere[$key]['hostRate'] = $value['zcr_star'];
            $compere[$key]['zid'] = $value['zcr_id'];
            $compere[$key]['url'] = $value['zcr_url'];
         }

        $services['data'] =  array_reverse($compere);
        //print_r($services);
        $this->assign('services',json_encode($services));
        $this->display();
    }

    //个人中心
    public function index_check_login(){
        $name = $_SESSION['member']['mobile'];    
        if($name){
            $this->redirect('User/personal_Info');
        }else{
            $this->redirect('Login/login');
        }
    }

    /*------------------------------------------------------------------------------------------------
    |排行榜
    -------------------------------------------------------------------------------------------------*/
    public function rankings(){
        $this->assign('citys',$this->city);
        $cid = session('cid');
        $cname = M('qy')->where(['qy_id'=>$cid])->getField('qy_name');
        $this->assign('cname',$cname);
        $map['c_id'] = $cid;
        $num = M('video')->where($map)->count();
        if($num <= 4){
            $this->assign('num',0);
        }else{
            $this->assign('num',$num);
        }
        $this->assign('cid',$cid);
        $model = M('video')->field('zb_img,video_name,start_time,number')->where($map)->order('number desc')->limit(4)->select();      

        foreach ($model as $key => $value) {
                //视频排行
                //{imgSrc:'img/hl_show_1_24.png',towsName:'萧先生&陆小姐',date:'2016-06-01',rate:1,hotSum:4321},
                if($key>2){
                    $model[$key]['rate'] = $key+1;
                }else{
                     $model[$key]['rate'] = 0;
                }
                $model[$key]['imgSrc'] = $value['zb_img']; 
                $model[$key]['towsName'] = $value['video_name'];
                $model[$key]['date'] = date('Y-m-d',$value['start_time']);
                $model[$key]['hotSum'] = $value['number'];
        }

        //print_r($model);exit;
        $userModel['data'] = $model;
        $this->assign('userModel',json_encode($userModel));
        $this->display();
    }

    public function ajaxGetranks(){
        $page = (int)I('get.page')?(int)I('get.page'):2;
        // $cid = (int)I('get.cid')?(int)I('get.cid'):3;
        $map['c_id'] = session('cid');
        $num = M('qy')->where($map)->count();
        $start = ($page-1)*4;
        $sheng = $num-$start;
        if($sheng <= 4){
            $userModel['num'] = 0;
        }else{
            $userModel['num'] = $sheng;
        }

        $model = M('video')->field('zb_img,video_name,start_time,number')->where($map)->order('number desc')->limit($start,4)->select();      

        foreach ($model as $key => $value) {
                //视频排行
                //{imgSrc:'img/hl_show_1_24.png',towsName:'萧先生&陆小姐',date:'2016-06-01',rate:1,hotSum:4321},
                if($key>2){
                    $model[$key]['rate'] = $key+1;
                }else{
                     $model[$key]['rate'] = 0;
                }
                $model[$key]['imgSrc'] = $value['zb_img']; 
                $model[$key]['towsName'] = $value['video_name'];
                $model[$key]['date'] = date('Y-m-d',$value['start_time']);
                $model[$key]['hotSum'] = $value['number'];
        }
        //print_r($model);exit;
        $userModel['data'] = $model;
        echo json_encode($userModel);exit;
    }


    public function carouselImg(){
        $this->display();
    }

    /*------------------------------------------------------------------------------------------------
    |产生验证码图片
    -------------------------------------------------------------------------------------------------*/
    public function getVerify(){
    	ob_clean();
    	// 导入Image类库
    	$Verify = new \Think\Verify(array('useCurve'=>false,'fontSize' => 36,'length'=>4,'useNoise'=>false,'fontttf'=>'4.ttf'));
    	$Verify->entry();
    }

    /*------------------------------------------------------------------------------------------------
    |主持人信息
    -------------------------------------------------------------------------------------------------*/
    public function host($id){
        $this->assign('citys',$this->city);
        $this->assign('cname','中国');
        //stars:[{hostname:"陈晓",age:28,height:"182",major:"播音主持",times:1231,src:"/uploads/2016/10/18/58058653da43f.png",hostRate:5}],
        //info:[{txtCon1:
        $zUser = M('zcr')->where('zcr_id='.$id)->find();
        $hostinfo['info']['txtCon1'] = $zUser['txtcon1'];
        $hostinfo['info']['txtCon2'] = $zUser['txtcon2'];
        $hostinfo['info']['txtCon3'] = $zUser['txtcon3'];

        $hostinfo['stars']['hostname'] = $zUser['zcr_name'] ;
        $hostinfo['stars']['age'] = $zUser['zcr_age'];
        $hostinfo['stars']['height'] = $zUser['zcr_height'];
        $hostinfo['stars']['major'] = $zUser['zcr_zy'];
        $hostinfo['stars']['times'] = $zUser['zcr_num'];
        $hostinfo['stars']['imgSrc'] = $zUser['zcr_img'];
        $hostinfo['stars']['hostRate'] = $zUser['zcr_star'];
        
        //print_r($hostinfo);exit;
        $this->assign('hid',$id);
        $this->assign('hostinfo',json_encode($hostinfo));
        $this->display();
    }

    // 主持人评星
    public function getstar(){
        //print_r($_POST);exit();
        $hid = $_GET['hid'] + 0;
        $key = $_POST['hostrating'] + 0;
        if($key == 0){
            exit('');
        
	}
        $zcr = M('zcr');
        $where['zcr_id'] = $hid;
        if($zcr->where($where)->save(['zcr_star'=>$key])){
            exit('1');
        }else{
            exit('');
        }
    }

    /*------------------------------------------------------------------------------------------------
    |精选视频
    -------------------------------------------------------------------------------------------------*/
    public function wonderful(){
        $this->display();
    }
    

     //imgSrc:'img/history_personalLogo.jpg',nickname:'美屡',gender:'女',addr:'天津和平区xxxx',qqNum:'123123456',phone:'',valiPicture:'',valiSms:'',loginPwd:''}

      //获取个人中心资料
     public function ziliao(){
          $id = session('member.userid');
          $arr = M('user')->where('userid='.$id)->find();
          foreach ($arr as $key => $value) {                
                $new_arr['imgSrc']  = $value['userlog'];
                $new_arr['nickname']  = $value['nick'];
                $new_arr['gender']  = $value['gender'];
                $new_arr['addr']  = $value['path'];
                $new_arr['qqNum']  = $value['QQ'];
                $new_arr['phone']  = $value['mPhone'];
          }


          exit(json_encode($new_arr));


     }





     public function test(){
//header("Content-type:text/html;charset=utf-8");
            $uid = '57423';     //数字用户名
            $pwd = 'k3le03';       //密码
            $mobile  = '1332131319352';   //号码
            $content = '你好，你的短信验证码为：6666，请在1分钟内验证。【云曼】';
            //iconv('gbk','utf-8',$content);  
            //iconv('utf-8','gbk',$content); 
            //iconv('gbk','utf-8',$content);
// $k = md5('k3le03');
// echo $k;exit;
            //即时发送
            $res = $this->sendSMS($uid,$pwd,$mobile,$content);
            echo $res;




     }

    // http://http.yunsms.cn/tx/?uid=57423&pwd=49f06d1fdbe250482bef270f8adbf15d&mobile=13800138000&content=你好，验证码：1043【快信

//http://http.yunsms.cn/tx/?uid=57423&pwd=abf1e3389a1abc75149f41047911b4e1&mobile=135445454352&encode=utf8&content=你好，验证码：1043【云曼】


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


    //获取红包接口
    public function get_hongbao(){

        $arr = M('hongbao')->find();
        
        print_r(json_encode($arr));exit;

    }

}
