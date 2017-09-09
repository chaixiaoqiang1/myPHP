<script>
function SdkAdController($scope, $http, alertService, $filter, $modal) {
	$scope.modify = function(id, sdkAdImg, sdkAdUrl) {
        var modalInstance = $modal.open({
            templateUrl: 'modify.html',
            controller: AdModifyController,
            resolve: {
                id : function () {
                    return id;
                },
                sdkAdImg : function () {
                    return sdkAdImg;
                },
                sdkAdUrl : function(){
                    return sdkAdUrl;
                },
            },
            backdrop : false,
            keyboard : false
        });
        modalInstance.result.then(function() {
            location.reload(true);  
        });
    }

    $scope.showimg = function(src){
		var modalInstance = $modal.open({
			templateUrl: 'check.html',
			controller: checkController,
			resolve: {
				src : function () {
					return src;
				}
			},
			backdrop : false,
			keyboard : false
		});
		modalInstance.result.then(function() {
		});		
	}
}

function checkController($scope, $modalInstance, src, $http, alertService) {
	$scope.src = src;
	$scope.cancel = function() {
		$modalInstance.dismiss('cancel');
	}
}

function AdModifyController($scope, $modalInstance, id, sdkAdImg, sdkAdUrl, $http, alertService){
	$scope.modifyinit = {};
    $scope.modifyinit.id = id;
    $scope.modifyinit.sdkAdImg = sdkAdImg;
    $scope.modifyinit.sdkAdUrl = sdkAdUrl;
    $scope.ModifyData = {};

    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    }
    $scope.ModifyForm= function() {
        $http({
            'method' : 'post',
            'url' : '/platform-api/mobilegame/game_package/sdk_ad_info',
            'data' : $.param($scope.ModifyData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            alert(data.msg); 
            $modalInstance.close();
        }).error(function(data) {
            alert('error: ' + data.error_description + '\n');
        });
    }
}
</script>
<div class="col-xs-12" ng-controller="SdkAdController">
	<div class="col-xs-10">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>ID</b></td>
					<td><b><?php echo Lang::get('platformapi.package_name'); ?></b></td>
					<td><b><?php echo Lang::get('platformapi.os_type'); ?></b></td>
					<td><b><?php echo Lang::get('platformapi.sdkAdImg'); ?></b></td>
					<td></td>
					<td><b><?php echo Lang::get('platformapi.sdkAdUrl'); ?></b></td>
					<td></td>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($data as $value) {?>
					<tr>
						<td><?php echo $value['id']; ?></td>
						<td><?php echo $value['package_name']; ?></td>
						<td><?php echo $value['os_type']; ?></td>
						<td><?php echo isset($value['sdkAdImg']) ? $value['sdkAdImg'] : ''; ?></td>
						<td><button ng-click="showimg('<?php echo isset($value['sdkAdImg']) ? $value['sdkAdImg'] : ''; ?>')" class="btn btn-primary"><?php echo Lang::get('slave.check_img'); ?></button></td>
						<td><?php echo isset($value['sdkAdUrl']) ? $value['sdkAdUrl'] : ''; ?></td>
						<td><button class="btn btn-danger" ng-click="modify(<?php echo $value['id']; ?>, '<?php echo isset($value['sdkAdImg']) ? $value['sdkAdImg'] : ''; ?>', '<?php echo isset($value['sdkAdUrl']) ? $value['sdkAdUrl'] : ''; ?>')">Modify</button></td>
					</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>
<script type="text/ng-template" id="modify.html">
        <div class="modal-header">
        </div>
        <form action="/project/release" method="post" role="form" ng-submit="ModifyForm()" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label>ID:</label>    
                <input type="number" class="form-control" readonly required ng-model="ModifyData.id" ng-init="ModifyData.id=modifyinit.id"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.sdkAdImg') ?>:</label>  
           		<a class="btn btn-primary" target="upload_img" href="<?php echo $platform_api_url; ?>">上传图片</a>
           		<label><?php echo Lang::get('platformapi.sdkAdImg_note') ?>:</label>  
                <input type="text" class="form-control" required ng-model="ModifyData.sdkAdImg" ng-init="ModifyData.sdkAdImg=modifyinit.sdkAdImg"/>
            </div>
            <div class="form-group">
                <label><?php echo Lang::get('platformapi.sdkAdUrl') ?>:</label>
                <input type="text" class="form-control" required ng-model="ModifyData.sdkAdUrl" ng-init="ModifyData.sdkAdUrl=modifyinit.sdkAdUrl"/>
            </div>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary"><?php echo Lang::get('platformapi.submit')?></button>
            <div class="btn btn-warning" ng-click="cancel()"><?php echo Lang::get('platformapi.cancel')?></div>
        </div>
        </form>
</script>

<script type="text/ng-template" id="check.html">
        <div class="modal-header">
        </div>
		<div class="modal-body">
			<div class="form-group">
			<img ng-src="{{src}}" style="max-width:100%;">
			</div>
		</div>
        <div class="modal-footer" style="text-align:center;">
			<p style="color:red;text-align:center;"><b><?php echo Lang::get('slave.cilck_to_download_pic') ?></b></p>
            <a class="btn btn-warning" ng-click="cancel()">确认</a>
        </div>
</script>