



document.querySelector('input[id="upload"]').addEventListener('change', function () {
    var that = this;

    lrz(that.files[0], {
        width: 200
    })
        .then(function (rst) {
            var img = new Image(),
                div = document.createElement('span');
            div.appendChild(img);
            img.onload = function () {
               // alert($(this));
                document.querySelector('#show_img').appendChild(div);
            };
            img.src = rst.base64;
            return rst;
        });
});
document.querySelector('input[id="uploa"]').addEventListener('change', function () {

    var that = this;
   /* for (var sop in that) {
        alert(sop);
    }*/
    lrz(that.files, {
        width: 200
    })
        .then(function (rst) {
            var img = new Image(),
                div = document.createElement('span');
            div.appendChild(img);
            img.onload = function () {
                //var img = new Image(),


                document.querySelector('#showImg').appendChild(div);
            };
            img.src = rst.base64;
            return rst;
        });
});

/**
 * 替换字符串 !{}
 * @param obj
 * @returns {String}
 * @example
 * '我是!{str}'.render({str: '测试'});
 */
String.prototype.render = function (obj) {
    var str = this, reg;

    Object.keys(obj).forEach(function (v) {
        reg = new RegExp('\\!\\{' + v + '\\}', 'g');
        str = str.replace(reg, obj[v]);
    });

    return str;
};

/**
 * 触发事件 - 只是为了兼容演示demo而已
 * @param element
 * @param event
 * @returns {boolean}
 */
function fireEvent (element, event) {
    var evt;

    if (document.createEventObject) {
        // IE浏览器支持fireEvent方法
        evt = document.createEventObject();
        return element.fireEvent('on' + event, evt)
    }
    else {
        // 其他标准浏览器使用dispatchEvent方法
        evt = document.createEvent('HTMLEvents');
        // initEvent接受3个参数：
        // 事件类型，是否冒泡，是否阻止浏览器的默认行为
        evt.initEvent(event, true, true);
        return !element.dispatchEvent(evt);
    }
}
