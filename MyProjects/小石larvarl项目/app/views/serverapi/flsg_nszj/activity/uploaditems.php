<script src="/js/ajaxfileupload.js"></script>
<script>
		$(function () {
            $("#upload").click(function () {
                ajaxFileUpload();
            })
        })
        function ajaxFileUpload() {
            $.ajaxFileUpload
            (
                {
                    url: '/game-server-api/upload/items', //用于文件上传的服务器端请求地址
                    secureuri: false, //是否需要安全协议，一般设置为false
                    fileElementId: 'itemsfile', //文件上传域的ID
                    dataType: 'json', //返回值类型 一般设置为json
                    success: function (data, status)  //服务器成功响应处理函数
                    {
                            if (data.error != '') {
                                alert(data.error);
                            } else {
                                alert(data.msg);
                            }
                    },
                    error: function (data, status, e)//服务器响应失败处理函数
                    {
                    	        	console.log('error');
                        alert(e);
                    }
                }
            )
            return false;
        }
</script>
<div class="col-xs-12">
	<form method="post" role="form" onsubmit="return false;" enctype="multipart/form-data">
		<p>选择item文件:<br/>
		<b style = "color:red; font-size:15px">(请选择.txt类型的文件，文件名不重要，上传前<b style="color:blue">确认目前选定的游戏是自己负责的游戏</b>，请谨慎操作！)</b>
			<input type="file" class="form-control" id="itemsfile"
						ng-model="formData.itemsfile" name="itemsfile" />
			<input type="submit" class="btn btn-default" id = "upload"
					value="上传" />
		</p>
        <div class="col-xs-10">
            <p>如果上传失败，请尝试以下步骤：</p>
            <p>1.打开存有item信息的txt文件。</p>
            <p>2.点击菜单栏的文件->另存为。</p>
            <p>3.保存按钮左侧有一个编码选项，请点击并选择UTF-8。</p>
            <p>4.点击保存后，上传新保存的文件。</p>
            <img ng-src="/img/upload_item_pic1.png">
        </div>
	</form>
</div>