<script> 
function sdkController($scope, $modalInstance, data, $http, alertService) {
    $scope.modifydata = data;
    $scope.orderData = {};
    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    }
    $scope.modifyForm= function(url) {
        $http({
            'method' : 'post',
            'url' : url,
            'data' : $.param($scope.orderData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $modalInstance.close();
            window.location.reload();
        }).error(function(data) {
            alert('error: ' + data.error + '\n');
        });
    }
}

function officalwebController($scope, $modalInstance, data, $http, alertService) {
    $scope.modifydata = data;
    $scope.orderData = {};
    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    }
    $scope.modifyForm= function(url) {
        $http({
            'method' : 'post',
            'url' : url,
            'data' : $.param($scope.orderData),
            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
        }).success(function(data) {
            $modalInstance.close();
            window.location.reload();
        }).error(function(data) {
            alert('error: ' + data.error + '\n');
        });
    }
}

function getMobileGamesProcedureController($scope, $http, alertService, $modal, $filter) {
    $scope.alerts = [];
    $scope.formData = {};
    $scope.formData.choice_id = 0;
    $scope.total = {};
    $scope.flag = -1;
    $scope.formData.app = [];
    $scope.processForm = function(id) {
		$scope.formData.id = id; 
        alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/slave-api/mobilegame/mobilegamesprocedure',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
        	if(id == 'submit')
        		alertService.add('success', '提交成功');
        	$scope.flag = id;
        	$scope.total = data;
        	if(id == 'mobile_modification_sdk'){
        		var modalInstance = $modal.open({
	            templateUrl: 'sdkmodify.html',
	            controller: sdkController,
	            resolve: {
	                data : function () {
	                    return data;
	                }
	            },
	            backdrop : false,
	            keyboard : false
	        	});
        	}
        	if(id == 'mobile_modification_officalweb'){
        		var modalInstance = $modal.open({
	            templateUrl: 'officalwebmodify.html',
	            controller: officalwebController,
	            resolve: {
	                data : function () {
	                    return data;
	                }
	            },
	            backdrop : false,
	            keyboard : false
	        	});
        	}
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
    };
} 
</script>
<div class="col-xs-12" ng-controller="getMobileGamesProcedureController">
	<div class="row">
		<div class="col-xs-10">
			<form method="post" role="form" ng-submit="processForm('submit')"
				onsubmit="return false;">
				<div class="col-xs-12">
					<?php 	$sign = 1;
					 foreach(DB::table('mobile_operation')->orderby('id')->get() as $v) { ?>
							<?php
							if(floor($v->id / 100) > $sign){
								echo "</ol>";
								$sign = floor($v->id / 100);
							}
							 if ( ($v->id) % 100==0 ) { ?>
								<b><?php echo $v->extra;?></b>
								<ol>
							<?php } ?>
							<?php if (in_array($v->id, $done)) { ?>
								<?php $checked_value = $v->id;?>
							<?php }else{ ?>
								<?php $checked_value = 0 ?>
							<?php }?>
							<?php if($v->operation_type == 1){ ?>
								<li><a href="<?php echo $v->lead_to;?>"><?php echo $v->name;?></a><input type="checkbox" ng-model="formData.app[<?php echo $v->id;?>]" ng-init="formData.app[<?php echo $v->id;?>]=<?php echo $checked_value?>" ng-checked="<?php echo $checked_value?>" ng-true-value="<?php echo $v->id; ?>" ng-false-value="0"  /></li>
							<?php }?>
							<?php if($v->operation_type == 2){ ?>
								<li><input type="button" class="btn btn-default" ng-click="processForm('<?php echo $v->lead_to;?>')" value="<?php echo $v->name;?>"/><input type="checkbox" ng-model="formData.app[<?php echo $v->id;?>]" ng-init="formData.app[<?php echo $v->id;?>]=<?php echo $checked_value?>" ng-checked="<?php echo $checked_value?>" ng-true-value="<?php echo $v->id; ?>" ng-false-value="0" /></li>
							<table ng-if="flag == '<?php echo $v->lead_to;?>'&& flag!='device_list,users' && flag!='third_party' " class="table table-striped">
								<thead>
									<tr class="info">
										<td><?php echo Lang::get('slave.datanum') ?></td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>{{total.num}}</td>
									</tr>
								</tbody>
							</table>
							<table ng-if="flag == '<?php echo $v->lead_to;?>'&& flag=='third_party' " class="table table-striped">
								<thead>
									<tr class="info">
										<td><?php echo Lang::get('slave.datanum') ?></td>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td ng-if="flag == 'third_party'">{{total.num_not_null}}</td>
									</tr>
								</tbody>
							</table>
							<table ng-if="flag == '<?php echo $v->lead_to;?>'&& flag=='device_list,users' " class="table table-striped">
								<thead>
									<tr class='info'>
										<td><?php echo Lang::get('slave.device_list_datanum') ?></td>
										<td>source</td>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="t in total.device_list">
										<td>{{t.num}}</td>
										<td>{{t.source}}</td>
									</tr>
								</tbody>
							</table>
							<table ng-if="flag == '<?php echo $v->lead_to;?>'&& flag=='device_list,users' " class="table table-striped">
								<thead>
									<tr class='info'>
										<td><?php echo Lang::get('slave.users_datanum') ?></td>
										<td>source</td>
									</tr>
								</thead>
								<tbody>
									<tr ng-repeat="t in total.users">
										<td>{{t.num}}</td>
										<td>{{t.source}}</td>
									</tr>
								</tbody>
							</table>
							<?php }?>
							<?php if($v->operation_type == 3){ ?>
								<li><?php echo $v->name;?><input type="checkbox" ng-model="formData.app[<?php echo $v->id;?>]" ng-init="formData.app[<?php echo $v->id;?>]=<?php echo $checked_value?>" ng-checked="<?php echo $checked_value?>" ng-true-value="<?php echo $v->id; ?>" ng-false-value="0" /></li>
							<?php }?>
							<?php if($v->operation_type == 4){ ?>
								<li><a href="" ng-click="processForm('<?php echo $v->lead_to;?>')"/><?php echo $v->name;?></a><input type="checkbox" ng-model="formData.app[<?php echo $v->id;?>]" ng-init="formData.app[<?php echo $v->id;?>]=<?php echo $checked_value?>" ng-checked="<?php echo $checked_value?>" ng-true-value="<?php echo $v->id; ?>" ng-false-value="0"  /></li>
							<?php }?>
						<?php } ?>
						</ol>
				</div> 
				<div class="clearfix">
				</div>
				<input type="submit" class="btn btn-default" style=""
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
	</div>

	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>

<script type="text/ng-template" id="sdkmodify.html">
        <div class="modal-header">
        </div>
        <form method="post" role="form" ng-submit="modifyForm('/slave-api/mobilegame/mobilegamesprocedure')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label>内容:</label>
                <textarea class="form-control" ng-cols="30"  ng-model="orderData.data" autofocus="autofocus" ng-init="orderData.data = modifydata.data" required/></textarea>
            </div>
        </div>
        <input type="hidden" ng-model="orderData.type" ng-init="orderData.type = 'sdkmodify'" name="id"/>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary">确认修改</button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>

<script type="text/ng-template" id="officalwebmodify.html">
        <div class="modal-header">
        </div>
        <form method="post" role="form" ng-submit="modifyForm('/slave-api/mobilegame/mobilegamesprocedure')" onsubmit="return false;">
        <div class="modal-body">
            <div class="form-group">
                <label>内容:</label>
                <textarea class="form-control" ng-cols="30"  ng-model="orderData.data" autofocus="autofocus" ng-init="orderData.data = modifydata.data" required/></textarea>
            </div>
        </div>
        <input type="hidden" ng-model="orderData.type" ng-init="orderData.type = 'officalwebmodify'" name="id"/>
        <div class="modal-footer" style="text-align:center;">
            <button class="btn btn-primary">确认修改</button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
        </form>
</script>