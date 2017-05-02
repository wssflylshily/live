/**
 * Created by Administrator on 2016/9/9.
 */
//判断是否微信浏览器打开：调用此方法即可
function is_weixin(){
    var ua = navigator.userAgent.toLowerCase();
    if(ua.match(/MicroMessenger/i) == "micromessenger") {return true;}
    else {return false;}
}
 /*window.onload= function () {
    if(is_weixin()){
        alert(123);
        body.style.margin=0;
        body.style.padding=0;
    }else{alert(456);}
};*/

//播放器调试
//document.getElementById("player").style.height=document.getElementById("player").scrollWidth*0.076+"%";
$(function(){
    var player=document.getElementById("player");
    var playerCon=document.getElementById("J_prismPlayer");
    player.style.height=window.innerWidth*9/16+'px';
    //playerCon.style.height=100+'%';
    if(playerCon){playerCon.style.height=100+'%';}
    document.getElementsByClassName('W-mainBg')[0].style.marginBottom=player.style.height;
    if(window.innerWidth<420){
        $('.prism-player .prism-big-play-btn').css({"width":"40px", "height":"40px","left":"5%","bottom":"4.5rem","background-size": "300%"})
    }
    if(window.innerWidth>767){
        player.style.position='relative';
        document.getElementsByClassName('W-mainBg')[0].style.marginBottom=1+'%';
        $('.prism-player .prism-big-play-btn').css({"width":"60px", "height":"60px","left":"3%","bottom":"4rem","background-size": "290%"})
    }
    if(window.innerWidth>800){
        player.style.width=90+'%';
        player.style.left=0;
        player.style.right=0;
    }
    if(window.innerWidth>1010){
        player.style.position='relative';
        player.style.width=889+'px';
        player.style.height=540+'px';
        document.getElementsByClassName('W-mainBg')[0].style.marginBottom=0;
    }
})

//调整键盘弹出后遮挡输入框
/*var wHeight = window.innerHeight;           //获取初始可视窗口高度
$(window).resize(function() {               //监测窗口大小的变化事件
    var hh = window.innerHeight;            //当前可视窗口高度
    var viewTop = $(window).scrollTop();    //可视窗口高度顶部距离网页顶部的距离
    if (window.innerWidth < 767) {  //当小屏时
        if(wHeight > hh){           //可以作为虚拟键盘弹出事件
            //alert(viewTop);
            $("body,html").scrollTop(viewTop+55);    //调整可视页面的位置
            //alert($("body").scrollTop());
            //alert($('.my-modal-content').css('marginTop').replace('px',''));
            $('.P-addUserPhone').css('marginTop','11%');
            $('.P-exitBg').css('display','none');
            $('Lnow-wishInputBox').css({'display':'fixed','bottom':'0'})
        }else{                      //可以作为虚拟键盘关闭事件
            //$("body,html").animate({scrollTop:viewTop-200});
            $("body,html").scrollTop(viewTop-55);
            $('.P-addUserPhone').css('marginTop','20%');
            $('.P-exitBg').css('display','table');
        }
    }
    wHeight = hh;
});*/

/*祝福语过滤*/
$(function () {
    $('.Lnow-wishInputBox input[type="button"]').on('click', function () {
        var inputWord = $('.Lnow-wishInputBox input[type="text"]').val();
        var words = ['我日','日你','我曰','吊','屌','操','我草','草','槽','肏','艹','我CAO','我cao','我擦','我肏','我曹','我槽','卧','窝', '猪', '扯淡', '蛋', '杂种', '种', '杂', '死绝', '死', '死光', '傻', '杀', '你吗','你妈', '妈', '你马', '玛', '骂', '婊子', '小三', '贱货', '渣', '混', '干', '灭', '六四', '64', '三级', '色诱', '色欲', '淫', '射', '夜情', '贱人', '贱', '娘', '母', '狗', '屁股', '下体', 'a片', '内裤', '浑圆', '咪咪', '发情', '白嫩', '粉嫩', '兽', '风骚', '呻吟', '阉', '高潮', '裸露', '爆', '暴', '强奸', '奸', '叫床', '巨奶', '巨乳', '乳房', '乳', '奶', '肉', '插逼', '插', '逼', '笔', '屄', '阴', '成人', '穴', '肛', '门', '龟', '尿', '湿', '粪', '屎', '鸟', '黄片', '鸡', '鸡巴', '阴茎', '睾丸', '卵', '瘾', 'jj', '寂寞', '乱轮', '妓女', '妓', '处女', '棒', '幼交', '幼', '做爱', '性爱', '性', '关系', '按摩', '快感', '处男', '猛男', '少妇', '狼', '浪', '收购', '求购', '军', '抢', '枪', '公关', '低价', '调查', '橱柜', '出轨', '血', '民主', '专制', '打到', '炸', '打倒', '灭亡', '共产党', 'gcd', '中国', '毛泽东', 'mzd', '江泽民', 'jzm', '胡锦涛', '温家宝', 'hjt', '习近平', 'xjp', '毒', '党', '人权', '维吾', '吾尔', '热比娅', '伊力哈木', '疆独', '藏民', '藏m', '达赖', '赖达', 'dalai', '哒赖', '法论', '李洪志', '销售', '联系', 'qq', '出售', '买', '卖', '加盟', '免费', '钱', '资金', '黑暗', 'b', 'sb', 'sm', 'diao', 'qiangjian', 'yindao', 'jb', 'jiba', 'cha', 'gan', 'nima', 'ri', 'porn', 'www', 'com', 'nai', 'fuck', 'shit', 'asshole', 'bitch', 'whore', 'makelove', 'make love'];
        for (var i = 0; i < words.length; i++) {
            inputWord = inputWord.replace(new RegExp(words[i], 'ig'), new Array(words[i].length+1).join('*'));
        }
        /*if(!inputWord.indexOf("*")){
            //alert('您输入的信息有违法信息！！');
            inputWord='';
        }*/
        $('.Lnow-wishInputBox input[type="text"]').val(inputWord);
    });
});