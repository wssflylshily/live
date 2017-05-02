<?php 
namespace Home\Tool;


class AliyunTool {
	// 阿里直播 ak 和 secret

	protected $alik = 'LTAIICmbMbAUL66z';
	protected $alisecret = 'zweAia2tr6k02IZwvirWPA8jnh0m13';
  protected $DomainName = "gotozb.yicehua.cn";
	   
    // 直播录制索引 //  
    // public function reindex($streamname='live4'){
    //     $ext = $streamname.'.m3u8';
    //     $url = 'https://cdn.aliyuncs.com/?';
    //      $requestparam = [
    //           'Action'=>'CreateLiveStreamRecordIndexFiles',
    //           'DomainName'=>'zhibo.tjtomato.com',
    //           'AppName'=>$this->appname,
    //           'StreamName'=>$streamname,
    //           'OssEndpoint'=>'tjtomatoin.oss-cn-hangzhou.aliyuncs.com',
    //           'OssBucket'=>'tjtomatoin',
    //           'OssObject'=>$prefix,
    //           'StartTime'=>gmdate('Y-m-d\TH:i:s\Z',strtotime('2016/11/4 15:55:00')),
    //           'EndTime'=>gmdate('Y-m-d\TH:i:s\Z',strtotime('2016/11/4 15:56:00')),
    //     ];
    //     $param = $this->requestall($requestparam);
    //     $url = $url.$param;
    //     echo $url;
    //     // $res = file_get_contents($url);
    //     // echo $res;
    // } 

    // 直播 推流历史
    public function queryhis($endtime,$appname){
      $url = 'https://cdn.aliyuncs.com/?';
        $requestparam = [
              'Action'=>'DescribeLiveStreamsPublishList',
              'DomainName'=>$this->DomainName,
              'AppName'=>$appname,
              'StartTime'=>gmdate('Y-m-d\TH:i:s\Z',strtotime('2016/11/9 00:00:00')),
              'EndTime'=>gmdate('Y-m-d\TH:i:s\Z',$endtime),
        ];

        $param = $this->requestall($requestparam);
        $url = $url.$param;
        $res = file_get_contents($url);
        $res = json_decode($res,true);
        return $res; 
        // echo $res;
    }



    // 正在推流 查询
    public function queryactive($appname){
      $url = 'https://cdn.aliyuncs.com/?';
        $requestparam = [
              'Action'=>'DescribeLiveStreamsOnlineList',
              'DomainName'=>$this->DomainName,
              'AppName'=>$appname,
        ];

        $param = $this->requestall($requestparam);
        $url = $url.$param;
        $res = file_get_contents($url);
        $res = json_decode($res,true);
        return $res;
        // echo $res;
    }

    // 查询所有录制配置
    // public function queryzhibo(){
    //   $url = 'https://cdn.aliyuncs.com/?';
    //     $requestparam = [
    //           'Action'=>'DescribeLiveRecordConfig',
    //           'DomainName'=>$this->DomainName,
    //     ];

    //     $param = $this->requestall($requestparam);
    //     $url = $url.$param;
    //     $res = file_get_contents($url);
    //     $res = json_decode($res,true);
    //     return $res;
    //     // echo $res;
    // }  

    // 查询操作历史
    // public function queryzhibo(){
    //   $url = 'https://cdn.aliyuncs.com/?';
    //     $requestparam = [
    //           'Action'=>'DescribeLiveStreamsControlHistory',
    //           'DomainName'=>$this->DomainName,
    //           'AppName'=>$this->appname,
    //           'StartTime'=>gmdate('Y-m-d\TH:i:s\Z',strtotime('2016/11/3 14:38:00')),
    //           'EndTime'=>gmdate('Y-m-d\TH:i:s\Z',strtotime('2016/11/4 18:00:00')),
    //     ];

    //     $param = $this->requestall($requestparam);
    //     $url = $url.$param;
    //     $res = file_get_contents($url);
    //     $res = json_decode($res,true);
    //     return $res;
    //     // echo $res;
    // }  

    // 删除录制配置
    public function recorddel(){
      $url = 'https://cdn.aliyuncs.com/?';
        $requestparam = [
              'Action'=>'DeleteLiveAppRecordConfig',
              'DomainName'=>$this->DomainName,
              'AppName'=>$appname,
        ];

        $param = $this->requestall($requestparam);
        $url = $url.$param;
        $res = file_get_contents($url);
        $res = json_decode($res,true);
        return $res;
        // echo $res;
    }      

    
    // 直播录制配置  判断是否配置过
    public function queryrecord($appname){
       $url = 'https://cdn.aliyuncs.com/?';
        $requestparam = [
              'Action'=>'DescribeLiveAppRecordConfig',
              'DomainName'=>$this->DomainName,
              'AppName'=>$appname,
        ];
        $param = $this->requestall($requestparam);
        $url = $url.$param;
        $res = file_get_contents($url);
        $res = json_decode($res,true);
        return $res;
    }


    // 直播录制索引文件 查询某个索引文件是否存在  
    public function queryreindex($streamname,$appname){
        $url = 'https://cdn.aliyuncs.com/?';
         $requestparam = [
              'Action'=>'DescribeLiveStreamRecordIndexFiles',
              'DomainName'=>$this->DomainName,
              'AppName'=>$appname,
              'StreamName'=>$streamname,
              'StartTime'=>gmdate('Y-m-d\TH:i:s\Z',strtotime('2016/11/8 00:00:00')),
              'EndTime'=>gmdate('Y-m-d\TH:i:s\Z',time()),
        ];
        $param = $this->requestall($requestparam);
        $url = $url.$param;
        $res = file_get_contents($url);
        $res = json_decode($res,true);
        return $res;
    } 

    // 查询点播文件 地址
    public function querydian(){
      $url = 'http://mts.aliyuncs.com?';
         $requestparam = [
              'Action'=>'QueryMediaList',
              'MediaIds'=>'53957cc1c7794503be109becf3b64f70',

          ];
        $param = $this->requestall($requestparam);
        $url = $url.$param;
        $res = file_get_contents($url);
        $res = json_decode($res,true);
        return $res;
    }

      
   // 阿里云直播组合参数
   protected function requestall($requestparam){
        // 公共参数
        $pubpar = array(
              'Format'=>'JSON',
              'Version'=>'2014-11-11',
              'AccessKeyId'=>$this->alik,
              'Timestamp'=>gmdate('Y-m-d\TH:i:s\Z'),
              'SignatureMethod'=>'HMAC-SHA1',
              'SignatureVersion'=>'1.0',
      );

        // 随机签名串
        $SignatureNonce = substr(md5(mt_rand(1,9999999)),0,10);
        $pubpar['SignatureNonce'] = $SignatureNonce;

        // 组合参数
        $param = array_merge($requestparam,$pubpar);


        // 把签名字符串组合进参数里
        $param['Signature'] = $this->sign($param);

        // 转成get请求形式
        $getpar = http_build_query($param);
        return $getpar;
   }

  // 生成签名 
   public function sign($param,$method='GET'){
        ksort($param);
        $arr = [];
        foreach($param as $k=>$v){
          $key = $this->code($k);
          $val = $this->code($v);
          $arr[$key] = $val; 
        }
        $tmp = '';
        foreach($arr as $k=>$v){
          $tmp .= $k.'='.$v.'&';
        }
        ksort($arr);
        $sign = rtrim($tmp,'&'); 

        $StringToSign = strtoupper($method).'&'.$this->code('/').'&'.$this->code($sign);
        // return $StringToSign;KkkQOf0ymKf4yVZLggy6kYiwgFs=  ;
        $key = $this->alisecret.'&';
        $hmac = hash_hmac('sha1',$StringToSign,$key,true);
        $Signature = base64_encode($hmac);
        return $Signature;
   }

   protected function code($value=null){
        $val = urlencode($value);
        $val = str_replace('+','%20',$val);
        $val = str_replace('*','%2A',$val);
        $val = str_replace('%7E','~',$val);
        return $val;
   }



}
