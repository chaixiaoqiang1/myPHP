var CreatedOKLodop7766=null;

//====判断是否需要安装CLodop云打印服务器:====
function needCLodop(){
    try{
	var ua=navigator.userAgent;
	if (ua.match(/Windows\sPhone/i) !=null) return true;
	if (ua.match(/iPhone|iPod/i) != null) return true;
	if (ua.match(/Android/i) != null) return true;
	if (ua.match(/Edge\D?\d+/i) != null) return true;
	if (ua.match(/QQBrowser/i) != null) return false;
	var verTrident=ua.match(/Trident\D?\d+/i);
	var verIE=ua.match(/MSIE\D?\d+/i);
	var verOPR=ua.match(/OPR\D?\d+/i);
	var verFF=ua.match(/Firefox\D?\d+/i);
	var x64=ua.match(/x64/i);
	if ((verTrident==null)&&(verIE==null)&&(x64!==null))
		return true; else
	if ( verFF !== null) {
		verFF = verFF[0].match(/\d+/);
		if ( verFF[0] >= 42 ) return true;
	} else
	if ( verOPR !== null) {
		verOPR = verOPR[0].match(/\d+/);
		if ( verOPR[0] >= 32 ) return true;
	} else
	if ((verTrident==null)&&(verIE==null)) {
		var verChrome=ua.match(/Chrome\D?\d+/i);
		if ( verChrome !== null ) {
			verChrome = verChrome[0].match(/\d+/);
			if (verChrome[0]>=42) return true;
		};
	};
        return false;
    } catch(err) {return true;};
};

//====页面引用CLodop云打印必须的JS文件：====
if (needCLodop()) {
	var head = document.head || document.getElementsByTagName("head")[0] || document.documentElement;
	var oscript = document.createElement("script");
	//让本机浏览器打印(更优先)：
	oscript = document.createElement("script");
	oscript.src ="http://localhost:8000/CLodopfuncs.js?priority=1";
	head.insertBefore( oscript,head.firstChild );
	//本机浏览器的后补端口8001：
	oscript = document.createElement("script");
	oscript.src ="http://localhost:8001/CLodopfuncs.js?priority=2";
	head.insertBefore( oscript,head.firstChild );
};

//====获取LODOP对象的主过程：====
function getLodop(oOBJECT,oEMBED){
    var strHtmInstall="打印控件未安装!点击这里执行安装,安装后请刷新页面或重新进入。";
    var strHtmUpdate="打印控件需要升级!点击这里执行升级,升级后请重新进入。";
    var strHtm64_Install="打印控件未安装!点击这里执行安装,安装后请刷新页面或重新进入。";
    var strHtm64_Update="打印控件需要升级!点击这里执行升级,升级后请重新进入。";
    var strHtmFireFox="（注意：如曾安装过Lodop旧版附件npActiveXPLugin,请在【工具】->【附加组件】->【扩展】中先卸它）</font>";
    var strHtmChrome="(如果此前正常，仅因浏览器升级或重安装而出问题，需重新执行以上安装）";
    var strCLodopInstall="CLodop云打印服务(localhost本地)未安装启动!点击这里执行安装,安装后请刷新页面。";
    var strCLodopUpdate="CLodop云打印服务需升级!点击这里执行升级,升级后请刷新页面。";
    var LODOP;
    try{
        var isIE = (navigator.userAgent.indexOf('MSIE')>=0) || (navigator.userAgent.indexOf('Trident')>=0);
        if (needCLodop()) {
            try{ LODOP=getCLodop();} catch(err) {};
	    if (!LODOP && document.readyState!=="complete") {
            var index=layer.confirm( "C-Lodop没准备好，请稍后再试！", {
                btn: ['确定'],
                btnAlign: 'c'//按钮
            }, function(){
                layer.close(index);
            });
            return;
	    };
            if (!LODOP) {
		 if (isIE)
                 var index=layer.confirm( strCLodopInstall, {
                     btn: ['下载','取消'],
                     btnAlign: 'c'//按钮
                 }, function(){
                     window.location.href="http://113.10.155.131/CLodopPrint_Setup_for_Win32NT.zip";
                     layer.close(index);
                 },function(){
                     layer.close(index);
                 });
         else
                var index=layer.confirm( strCLodopInstall, {
                    btn: ['下载','取消'],
                    btnAlign: 'c'//按钮
                }, function(){
                    window.location.href="http://113.10.155.131/CLodopPrint_Setup_for_Win32NT.zip";
                    layer.close(index);
                },function(){
                    layer.close(index);
                });

                 return;
            } else {

	         if (CLODOP.CVERSION<"2.0.4.6") {
			if (isIE)
                var index=layer.confirm( strCLodopUpdate, {
                    btn: ['下载','取消'],
                    btnAlign: 'c'//按钮
                }, function(){
                    window.location.href="http://113.10.155.131/CLodopPrint_Setup_for_Win32NT.zip";
                    layer.close(index);
                },function(){
                    layer.close(index);
                });
            else
                var index=layer.confirm( strCLodopUpdate, {
                    btn: ['下载','取消'],
                    btnAlign: 'c'//按钮
                }, function(){
                    window.location.href="http://113.10.155.131/CLodopPrint_Setup_for_Win32NT.zip";
                    layer.close(index);
                },function(){
                    layer.close(index);
                });
		 };
		 if (oEMBED && oEMBED.parentNode) oEMBED.parentNode.removeChild(oEMBED);
		 if (oOBJECT && oOBJECT.parentNode) oOBJECT.parentNode.removeChild(oOBJECT);
	    };
        } else {
            var is64IE  = isIE && (navigator.userAgent.indexOf('x64')>=0);
            //=====如果页面有Lodop就直接使用，没有则新建:==========
            if (oOBJECT!=undefined || oEMBED!=undefined) {
                if (isIE) LODOP=oOBJECT; else  LODOP=oEMBED;
            } else if (CreatedOKLodop7766==null){
                LODOP=document.createElement("object");
                LODOP.setAttribute("width",0);
                LODOP.setAttribute("height",0);
                LODOP.setAttribute("style","position:absolute;left:0px;top:-100px;width:0px;height:0px;");
                if (isIE) LODOP.setAttribute("classid","clsid:2105C259-1E0C-4534-8141-A753534CB4CA");
                else LODOP.setAttribute("type","application/x-print-lodop");
                document.documentElement.appendChild(LODOP);
                CreatedOKLodop7766=LODOP;
             } else LODOP=CreatedOKLodop7766;
            //=====Lodop插件未安装时提示下载地址:==========
            if ((LODOP==null)||(typeof(LODOP.VERSION)=="undefined")) {
                 if (navigator.userAgent.indexOf('Chrome')>=0)
                    var index=layer.confirm( strHtmChrome, {
                        btn: ['确定'],
                        btnAlign: 'c'//按钮
                    }, function(){
                        layer.close(index);
                    });
                 if (navigator.userAgent.indexOf('Firefox')>=0)
                     var index=layer.confirm( strHtmFireFox, {
                         btn: ['确定'],
                         btnAlign: 'c'//按钮
                     }, function(){
                         layer.close(index);
                     });
                 if (is64IE)
                     var index=layer.confirm( strHtm64_Install, {
                         btn: ['下载','取消'],
                         btnAlign: 'c'//按钮
                     }, function(){
                         window.location.href="http://113.10.155.131/install_lodop64.zip";
                         layer.close(index);
                     },function(){
                         layer.close(index);
                     });
                 else
                 if (isIE)
                    var index=layer.confirm( strHtmInstall, {
                        btn: ['下载','取消'],
                        btnAlign: 'c'//按钮
                    }, function(){
                        window.location.href="http://113.10.155.131/install_lodop32.zip";
                        layer.close(index);
                    },function(){
                        layer.close(index);
                    });
                 else
                     var index=layer.confirm( strHtmInstall, {
                         btn: ['下载','取消'],
                         btnAlign: 'c'//按钮
                     }, function(){
                         window.location.href="http://113.10.155.131/install_lodop32.zip";
                         layer.close(index);
                     },function(){
                         layer.close(index);
                     });
                 return LODOP;
            };
        };
        if (LODOP.VERSION<"6.2.0.3") {
            if (needCLodop())
                var index=layer.confirm( strCLodopUpdate, {
                    btn: ['下载','取消'],
                    btnAlign: 'c'//按钮
                }, function(){
                    window.location.href="http://113.10.155.131/CLodopPrint_Setup_for_Win32NT.zip";
                    layer.close(index);
                },function(){
                    layer.close(index);
                });
            else
            if (is64IE)
                var index=layer.confirm( strHtm64_Update, {
                    btn: ['下载','取消'],
                    btnAlign: 'c'//按钮
                }, function(){
                    window.location.href="http://113.10.155.131/install_lodop64.zip";
                    layer.close(index);
                },function(){
                    layer.close(index);
                });
            else
            if (isIE)
                var index=layer.confirm( strHtmUpdate, {
                    btn: ['下载','取消'],
                    btnAlign: 'c'//按钮
                }, function(){
                    window.location.href="http://113.10.155.131/install_lodop32.zip";
                    layer.close(index);
                },function(){
                    layer.close(index);
                });
            else
                var index=layer.confirm( strHtmUpdate, {
                    btn: ['下载','取消'],
                    btnAlign: 'c'//按钮
                }, function(){
                    window.location.href="http://113.10.155.131/install_lodop32.zip";
                    layer.close(index);
                },function(){
                    layer.close(index);
                });
            return LODOP;
        };

		//===如下空白位置适合调用统一功能(如注册语句、语言选择等):===
        LODOP.SET_LICENSES("申通快递有限公司","88484B219EC9898C62DF0ACACDA3204B","","");
        //===========================================================
        return LODOP;
    } catch(err) {alert("getLodop出错:"+err);};
};

