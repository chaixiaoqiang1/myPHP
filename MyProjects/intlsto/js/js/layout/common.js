/**
 * Created by Srako on 2017/06/13.
 */

$(function () {
    var csrftoken = $.cookie('XSRF-TOKEN');
    $.ajaxSetup({
        headers: {
            'X-XSRF-TOKEN': csrftoken
        }
    });
    //判断当前页是否在iframe之中,不在，则打开iframe加载当前页面
    if ((!self.frameElement||self.frameElement.tagName !== "IFRAME")&&$('body').children('.reg').length===0) {
        jsPost(web_root_url, {url :location.href});
    }

    //点击子页面关闭右上下拉框
    $(document).on('click',function (event) {
        $('.user_set .user_box',parent.document).find('.user_nav').slideUp();
        $('.user_set .ring ,.ring_updown',parent.document).find('.ring_updown').slideUp();
        event.stopPropagation();
    });

    //绑定layer.msg的红色输入框点击消除红色
    $('body').click(function (e) {
        if (e.target.className.indexOf('bindBlack')>=0){
            $(e.target).removeClass('error bindBlack');
        }
    });


    /**
     * 转化为当地时间
     */
    $('.localtime').each(function () {
        var datetime=$(this).text();
        $(this).text(BJToLocal(datetime));
    });

    //金额输入框，失焦格式化金额
    $(".money>input[type='text']").blur(function () {
        if($(this).val()!==''&&!isNaN($(this).val())){
            $(this).val(toDecimal($(this).val()));
        }else {
            $(this).val('');
        }
    });

});

/**
 * 重新定义错误提示，绑定输入框对象
 * @param str  提示信息
 * @param obj  输入框对象
 */
function layerMsg(str,obj) {
    obj.focus().addClass('error bindBlack');
    layer.msg(str);
}

/**
 * js模拟post数据
 * @param URL
 * @param PARAMS
 * @returns {Element}
 */
function jsPost(URL, PARAMS) {
    var temp = document.createElement("form");
    temp.action = URL;
    temp.method = "post";
    temp.style.display = "none";
    for (var x in PARAMS) {
        var opt = document.createElement("textarea");
        opt.name = x;
        opt.value = PARAMS[x];
        // alert(opt.name)
        temp.appendChild(opt);
    }
    document.body.appendChild(temp);
    temp.submit();
    return temp;
}

/**
 * 搜索刷新表单
 * @param table
 */
function reloadTable(table) {
    $('#'+table).bootstrapTable('refreshOptions',{pageNumber:1});
}

/**
 * 重置刷新表单
 * @param obj
 * @param table
 */
function resetTable(obj,table) {
    $(obj).parents('form').find('input,select').val('');
    $('#'+table).bootstrapTable('refreshOptions',{pageNumber:1});
}

/**
 * 加载状态
 */
function loading() {
    layer.load(1, {
        shade: [0.1,'#fff'] //0.1透明度的白色背景
    });
}

/**
 * 北京日期转换为当地日期
 * @param dateTime
 * @returns {*}
 * @constructor
 */
function BJToLocal(dateTime){
    var d = new Date();
    var timeOff=d.getTimezoneOffset();
    if(timeOff===-480||dateTime==null){return dateTime;}
    var localTime=Date.parse(new Date(dateTime))/1000-28800-timeOff*60;
    d.setTime(localTime * 1000);
    return d.format('yyyy-MM-dd hh:mm:ss');
}


/**
 * 格式化时间戳为datetime
 * @param format
 * @returns {*}
 */
Date.prototype.format = function(format) {
    var date = {
        "M+": this.getMonth() + 1,
        "d+": this.getDate(),
        "h+": this.getHours(),
        "m+": this.getMinutes(),
        "s+": this.getSeconds(),
        "q+": Math.floor((this.getMonth() + 3) / 3),
        "S+": this.getMilliseconds()
    };
    if (/(y+)/i.test(format)) {
        format = format.replace(RegExp.$1, (this.getFullYear() + '').substr(4 - RegExp.$1.length));
    }
    for (var k in date) {
        if (new RegExp("(" + k + ")").test(format)) {
            format = format.replace(RegExp.$1, RegExp.$1.length == 1
                ? date[k] : ("00" + date[k]).substr(("" + date[k]).length));
        }
    }
    return format;
};

/**
 * 时间转换时间戳
 * @param date
 * @returns {string}
 */
var formatDate = function (date) {
    var y = date.getFullYear(),
        m = date.getMonth() + 1,
        d = date.getDate();
    m = m < 10 ? '0' + m : m;
    d = d < 10 ? ('0' + d) : d;
    return Date.parse(new Date(y+'-'+m+'-'+d+' 00:00:00'))/1000;
};


/**
 * 时间转换带时分秒
 * @param date
 * @returns {string}
 */
var formatDateTime = function (date) {
    var y = date.getFullYear(),
     m = date.getMonth() + 1,
     d = date.getDate(),
     h = date.getHours(),
     minute = date.getMinutes(),
     second=date.getSeconds();
    m = m < 10 ? ('0' + m) : m;

    d = d < 10 ? ('0' + d) : d;
    h=h < 10 ? ('0' + h) : h;
    minute = minute < 10 ? ('0' + minute) : minute;
    second=second < 10 ? ('0' + second) : second;
    return y + '-' + m + '-' + d+' '+h+':'+minute+':'+second;
};


/**
 * 获取小数后两位
 * @param x
 * @returns {*}
 */
function toDecimal(x) {
    var fp = parseFloat(x);
    if (isNaN(fp)) {
        return false;
    }
    var f = Math.round(x*100)/100;
    var s = f.toString();
    var rs = s.indexOf('.');
    if (rs < 0) {
        rs = s.length;
        s += '.';
    }
    while (s.length <= rs + 2) {
        s += '0';
    }
    return s;
}

/**
 * 统一处理错误，未登录状态。
 * @param data
 */
function sysException(data) {
    layer.closeAll("loading");
    if(data!=null&&data.status===404&&data.location!==''){
        top.location.href=data.location+'?url='+encodeURIComponent(location.href);
    }
}
/**
 *
 * @param imgFile：文件按钮对象
 * @param get_data: 转换成功后执行的方法
 * @returns {boolean}
 */
function runImg(imgFile,get_data) {
    var pattern = /(\.*.jpg$)|(\.*.png$)|(\.*.jpeg$)|(\.*.gif$)|(\.*.bmp$)/;
    if(!pattern.test(imgFile.value)) {
        layer.alert("请上传jpg/jpeg/png/gif格式的照片！");
        imgFile.focus();
    }else{
        //判断浏览器类型
        if(document.all){
            //兼容IE
            var realPath, xmlHttp, xml_dom, tmpNode, imgBase64Data;
            realPath = imgFile.value;//获取文件的真实本地路径.
            xmlHttp = new ActiveXObject("MSXML2.XMLHTTP");
            xmlHttp.open("POST",realPath, false);
            xmlHttp.send("");
            xml_dom = new ActiveXObject("MSXML2.DOMDocument");
            tmpNode = xml_dom.createElement("tmpNode");
            tmpNode.dataType = "bin.base64";
            tmpNode.nodeTypedValue = xmlHttp.responseBody;
            imgBase64Data = "data:image/jpg;base64," + tmpNode.text.replace(/\n/g,"");
            get_data(imgBase64Data);
        }else{
            //兼容主流浏览器
            var fileReader;
            fileReader = new FileReader();
            fileReader.readAsDataURL(imgFile.files[0]);
            fileReader.onload = function () {
                get_data(this.result); //base64数据
            }
        }
    }
}

/**
 * 验证密码强度1,2,3,4。form mail.163.com
 * @param e
 * @returns {number}
 */
function checkPasswordStrength(e) {
    var d = 0,
        f, c = 0;
    for (i = 0; i < e.length; i++) {
        f = charMode(e.charAt(i));
        if (0 == f) {
            return - 1
        }
        if (0 == (d & f)) {
            d |= f; ++c
        }
    }
    return c
}
/**
 * 字符串转换为code
 * @param f
 * @returns {number}
 */
function charMode(f) {
    var b = " 　`｀~～!！@·#＃$￥%％^…&＆()（）-－_—=＝+＋[]［］|·:：;；\"“\\、'‘,，<>〈〉?？/／*＊.。{}｛｝",
        e = f.charCodeAt(0);
    if (e >= 48 && e <= 57) {
        return 1
    } else {
        if (e >= 65 && e <= 90) {
            return 2
        } else {
            if (e >= 97 && e <= 122) {
                return 4
            } else {
                if ( - 1 < b.indexOf(f)) {
                    return 8
                }
            }
        }
    }
    return 0
}