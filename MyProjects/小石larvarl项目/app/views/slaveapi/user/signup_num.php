<script> 
function getUserStatController($scope, $http, alertService, $filter) {
    $scope.alerts = [];
    $scope.end_time = null;
    $scope.formData = {};
    $scope.items = {};
    $scope.pay_statics = {};

    $scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
        $scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
		$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        $http({
            'method': 'post',
            'url': '/slave-api/users/signnum',
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

} 
</script>
<div class="col-xs-12" ng-controller="getUserStatController">
	<div class="row">
		<div class="col-xs-12">
			<form action="/slave-api/users/signnum" method="get" role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_ids"
						id="select_game_server" ng-model="formData.server_ids" multiple="multiple" ng-multiple="true" size="8">
						<option value="0">全服查询</option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group" style="height: 35px;">
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="end_time" init-value="23:59:59"></quick-datepicker> 
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<input type="submit" class="btn btn-default" style=""
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
				<div class="form-group">
					<ul>
						<li><b style="color:red;font-size:15px;">因为玩家注册的时候无法区分服务器，因此注册玩家不区分服务器，每次查询都查询的是此段时间内的本游戏所有注册人数</b></li>
						<li><b style="color:red;font-size:15px;">注册玩家数只包括所选时段内明确是本游戏的注册玩家，不包括官网无明确目的的主动注册和目的为其他游戏的注册</b></li>
						<li><b style="color:red;font-size:15px;">创建玩家数只包括满足以上条件的注册玩家截止当前的所有创建的角色数量，并不限制创建角色的时间</b></li>
						<li><b style="color:red;font-size:15px;">因为以上条件的限制，因此此处的创建创建数量可能和其他功能里的创建数有所差异，一般为此处的数量略低</b></li>
					</ul>
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
	<?php if(in_array($game_id, $webgameids)){ ?>
		<div class="col-xs-12">
			<table class="table table-striped">
				<thead>
					<tr class="info">
						<td>实名注册数</td>
						<td>匿名注册数</td>
						<td>实名创建数</td>
						<td>匿名创建数</td>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="t in items">
						<td><b>{{t.sign_not}}</b></td>
						<td><b>{{t.sign_is}}</b></td>
						<td><b>{{t.create_not}}</b></td>
						<td><b>{{t.create_is}}</b></td>
					</tr>
				</tbody>
			</table>
		</div>
	<?php }else{ ?>
	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td>注册数</td>
					<td>创建数</td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td><b>{{t.sign_not - (-t.sign_is)}}</b></td>
					<td><b>{{t.create_not - (-t.create_is)}}</b></td>
				</tr>
			</tbody>
		</table>
	</div>
	<?php } ?>
</div>