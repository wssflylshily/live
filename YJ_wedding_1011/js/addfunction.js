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

//导航定固定功能
function menuFixed(id) {
    var obj = document.getElementById(id);
    var _getHeight = obj.offsetTop;
    window.onscroll = function () {
        changePos(id, _getHeight);
    }
}
function changePos(id, height) {
    var obj = document.getElementById(id);
    var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    if (window.innerWidth > 767) {
        if (scrollTop < height) {
            obj.style.position = 'relative';
            document.getElementsByTagName("section")[0].style.marginTop = 0 + "px";
        } else {
            obj.style.position = 'fixed';
            //顶部导航固定后，顶部空白内容丢失，section自动顶上，会导致页面滑动跳闪和无法滑动问题。
            // 而小屏下导航栏的占位隐藏，也会在滑动时跳闪，用section内容区域的margin-top来填充导航栏的高度
            document.getElementsByTagName("section")[0].style.marginTop = 70 + "px";
        }
    } else {
        obj.style.position = 'fixed';
        document.getElementsByTagName("section")[0].style.marginTop = 55 + "px";
    }
}
window.onload = function () {
    menuFixed('navPart');
};

//调整键盘弹出后遮挡输入框
var wHeight = window.innerHeight;           //获取初始可视窗口高度
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
});
