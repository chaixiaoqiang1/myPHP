<script> 
function modalMsgController($scope, $modalInstance, item, $http, alertService) {
	$scope.logData = {};
	$scope.logData.uid = item.uid;
	$scope.logData.msg = item.msg.desc;
	$scope.cancel = function() {
		$modalInstance.dismiss('cancel');
	}
	$scope.msg = function() {
		$http({
			'method' : 'post',
			'url' : '/slave-api/payment/order/unpay-msg',
			'data' : $.param($scope.logData),
			'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
		}).success(function(data) {
			$modalInstance.close();
		}).error(function(data) {
			alert(data.error);
		});
	}
	$scope.setmsg = function(msg){
		$scope.logData.msg = msg;
	}
}	
function getPaymentUnpayController($scope, $http, alertService, $filter, $modal) {
    $scope.alerts = [];
    $scope.start_time = null;
    $scope.end_time = null;
    $scope.formData = {};
	$scope.items = [];

    $scope.processFrom = function() {
		alertService.alerts = $scope.alerts;
		$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/payment/order/unpay',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
			$scope.items = data;
		}).error(function(data) {
			alertService.add('danger', data.error);
		});
    };
	$scope.createMsg = function(item) {
		var modalInstance = $modal.open({
			templateUrl: 'create_msg.html',
			controller: modalMsgController,
			resolve: {
				item : function () {
					return item;
				}
			},
			backdrop : false,
			keyboard : false
		});
		modalInstance.result.then(function() {
			$scope.processFrom();	
		});
	}
} 
</script>
<div class="col-xs-12" ng-controller="getPaymentUnpayController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/payment/order/unpay" method="get"
				role="form"
				ng-submit="processFrom('/slave-api/payment/order/unpay')"
				onsubmit="return false;">

				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('slave.show_all_servers') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0 0 0 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class="form-group col-xs-4" style="padding: 0 0 0 0">
					<input type="text" class="form-control"
						placeholder="<?php echo Lang::get('slave.failed_times')?>"
						ng-model="formData.failed_times" required name="failed_times"?>
				</div>
				<div class="form-group col-xs-4">
					<select class="form-control" name="order_by"
						id="order_by" ng-model="formData.order_by"
						ng-init="formData.order_by='count'">
						<option value="count"><?php echo Lang::get('slave.order_by_count') ?></option>
						<option value="dollar_amount"><?php echo Lang::get('slave.order_by_dollar_amount') ?></option>
						<option value="create_time"><?php echo Lang::get('slave.order_by_create_time') ?></option>
						<option value="server_internal_id"><?php echo Lang::get('slave.order_by_server_internal_id') ?></option>
					</select>
				</div>
				<div class="form-group col-xs-4">
					<select class="form-control" name="order_desc"
						id="order_desc" ng-model="formData.order_desc"
						ng-init="formData.order_desc='desc'">
						<option value="desc"><?php echo Lang::get('slave.order_by_desc') ?></option>
						<option value="asc"><?php echo Lang::get('slave.order_by_asc') ?></option>
					</select>
				</div>
				<div class="form-group col-xs-4" style="padding: 0 0 0 0">
					<select class="form-control" name="show_type"
						id="show_type" ng-model="formData.show_type"
						ng-init="formData.show_type='all'">
						<option value="all"><?php echo Lang::get('slave.show_all') ?></option>
						<option value="dealt"><?php echo Lang::get('slave.dealt') ?></option>
						<option value="not_deal"><?php echo Lang::get('slave.not_deal') ?></option>
					</select>
				</div>
				<div class="form-group col-xs-4">
					<input type="submit" class="btn btn-default" style=""
						value="<?php echo Lang::get('basic.btn_submit') ?>" />
				</div>
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<!-- 	<div class="row margin-top-10"> -->
	<div class="col-xs-12" style="padding: 0;">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><?php echo Lang::get('slave.recharge_failed_times');?></td>
					<td><?php echo Lang::get('slave.recharge_failed_amount');?></td>
					<td><?php echo Lang::get('slave.player_id');?></td>
					<td><?php echo Lang::get('slave.player_name');?></td>
					<td><?php echo Lang::get('slave.player_email');?></td>
					<td><?php echo Lang::get('slave.player_uid');?></td>
					<td><?php echo Lang::get('slave.fail_time') ?></td>
					<td><?php echo Lang::get('slave.server_name');?></td>
					<td><?php echo Lang::get('slave.recharge_failed_msg') ?></td>
					<td><?php echo Lang::get('slave.record_time') ?></td>
					<td><?php echo Lang::get('slave.recorder') ?></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.count}}</td>
					<td>{{t.dollar_amount|number:2}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.login_email}}</td>
					<td><a href="/slave-api/payment/order?uid={{t.uid}}" target ="blank">{{t.uid}}</a></td>
					<td>{{t.create_time}}</td>
					<td>{{t.server_name}}</td>
					<td ng-if="t.msg" ng-click="createMsg(t)"><div class="btn btn-info">{{t.msg.desc}}</div></td>
					<td ng-if="!t.msg" ng-click="createMsg(t)"><div class="btn btn-danger">Record</div></td>
					<td>{{t.msg.ctime}}</td>
					<td>{{t.msg.username}}</td>
				</tr>
				</body>
		
		</table>
	</div>
</div>

<script type="text/ng-template" id="create_msg.html">
        <div class="modal-header">
        </div>
		<form ng-submit="msg()" onsubmit="return false;">
        <div class="modal-body">
			<div class="form-group">
				<label>
				<?php echo Lang::get('slave.recharge_failed_msg')?>
				</label>
				<textarea class="form-control" ng-model="logData.msg" rows="5">
				</textarea>
			</div>
			<div>
			    <a class="btn btn-primary" ng-click="setmsg('<?php echo Lang::get('basic.success'); ?>')"><?php echo Lang::get('basic.success'); ?></a>
		        <a class="btn btn-primary" ng-click="setmsg('<?php echo Lang::get('basic.fail'); ?>')"><?php echo Lang::get('basic.fail'); ?></a>
		        <a class="btn btn-primary" ng-click="setmsg('<?php echo Lang::get('basic.told_caiwu'); ?>')"><?php echo Lang::get('basic.told_caiwu'); ?></a>
		        <a class="btn btn-primary" ng-click="setmsg('<?php echo Lang::get('basic.told_player'); ?>')"><?php echo Lang::get('basic.told_player'); ?></a>
			</div
        </div>
        <div class="modal-footer" style="text-align:center;">
			<button class="btn btn-primary"><?php echo Lang::get('basic.btn_submit')?></button>
            <a class="btn btn-warning" ng-click="cancel()">Cancel</a>
        </div>
		</form>
</script>