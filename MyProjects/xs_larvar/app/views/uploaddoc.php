<script src="/js/ajaxfileupload.js"></script>
<script>
		$(function () {
            $("#upload").click(function () {
                ajaxFileUpload();
            })
        })
        function ajaxFileUpload() {
        	console.log('testetsa');
            $.ajaxFileUpload
            (
                {
                    url: '/slave-api/mobilegame/uploaddoc', //用于文件上传的服务器端请求地址
                    secureuri: false, //是否需要安全协议，一般设置为false
                    fileElementId: 'docfile', //文件上传域的ID
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

        function downloadController($scope, $http, $filter, alertService) {
            $scope.alerts = [];
            $scope.formData = {};
            $scope.items = [];
            $scope.formData.type = 'download';

            $scope.download= function() {
                $http({
                    'method' : 'post',
                    'url' : "/slave-api/mobilegame/uploaddoc",
                    'data' : $.param($scope.formData),
                    'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
                }).success(function(data) {
                    window.location.replace("/slave-api/mobilegame/uploaddoc?file=" + data.file);
                }).error(function(data) {
                    alert('error: ' + data.error + '\n');
                });
            }
        }
</script>
<div class="col-xs-12">
	<form method="post" role="form" onsubmit="return false;" enctype="multipart/form-data">
		<p>选择文档文件:<br/>
		<b style = "color:red; font-size:15px">(请上传文档(.doc))</b>
			<input type="file" class="form-control" id="docfile"
						ng-model="formData.itemsfile" name="docfile" />
			<input type="submit" class="btn btn-default" id = "upload"
                    value="上传" />
            <input ng-controller="downloadController" type="button" class="btn btn-default" id = "download" ng-click="download()"
            value="下载" />
            
		</p>
	</form>
</div>