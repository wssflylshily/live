<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    //后台主页
    //
    //
    public function index(){

    	if(!session('adminid')){
            if(session('admintype') == 1){
                $this->redirect('Admin/glylb');
            }else{
                $this->redirect('Admin/zblb');
            }
        }
        if(session('admintype') == 1){
            $arr = M('admin')->select();
            $this->assign('res',$arr);
            $this->display('Admin/glylb');
        }else{

            $this->redirect('Admin/zblb');
        }
        
        
    }
    /**
     * 编辑信息
     */
    public function editInfo(){
    	$info = M('info')->where('id=1')->find();
    	if ($info){
    		M('info')->where("id=".$info['id'])->save($_POST);
    		$this->success('更新成功');
    	}else{
    		M('info')->add($_POST);
    		$this->success('保存成功');
    	}
    }
    
    //这是个测试方法 
    public function test(){
    	$ali = new \Admin\Tool\AliyunTool();
        $ali->querydian(); 
    }
    


    //发布任务
    public function publish(){
        $this->display();
    } 




}