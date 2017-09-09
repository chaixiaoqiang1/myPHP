<script>
	function outflowController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time = null;
		$scope.end_time = null;
		$scope.login_start_time = null;
		$scope.login_end_time = null;
		$scope.formData = {};
		$scope.total = {};
		$scope.processFrom = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.login_start_time = $filter('date')($scope.login_start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.login_end_time = $filter('date')($scope.login_end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/player/outflow',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.total = data.result_info;
				$scope.miss = data.result;
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="outflowController">
	<div class="row">
		<div class="eb-content">
			<form action="/slave-api/player/outflow" method="get" role="form" ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" id="select_game_server" ng-model="formData.server_id"  ng-init="formData.server_id='0'" multiple="multiple" ng-multiple="true" size=5>
					        <option value="0"><b>请选择服务器</b></option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<select class="form-control" name="is_pay" id="is_pay" ng-model="formData.is_pay"  ng-init="formData.is_pay='0'">
					        <option value="0"><b>全部用户</b></option>
							<option value="1">已付费用户</option>
					</select>
				</div>
				<div class="form-group">
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<label>创建时间介于：</label><quick-datepicker ng-model="start_time" init-value="00:00:00"></quick-datepicker> 
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

				<div class="clearfix"></div>
				<p></p>
				
				<div class="col-md-6" style="padding: 0">   
					<div class="input-group">
						<label>登陆时间介于：</label><quick-datepicker ng-model="login_start_time" init-value="00:00:00"></quick-datepicker> 
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>

				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="login_end_time" init-value="23:59:59"></quick-datepicker> 
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				
				<div class="clearfix"></div>
				<p></p>
                <div class="form-group">
				<b>连续未登陆天数:</b>	<input type="text" name="miss_days" ng-model="formData.miss_days" class="form-control" placeholder="请输入天数"/>
				</div>

				<input type="submit" class="btn btn-default" style="" value="<?php echo Lang::get('basic.btn_submit') ?>" />

			</form>
		</div>
			<p><font color="red">功能简述:</font></p>
	        <p><font color="red">1）查询时间（一），查询时间内创建角色数量；</font></p>
	        <p><font color="red">2）查询时间（二），查询时间（一）内创建角色，在查询时间内的留存情况；</font></p>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

	<div class="col-xs-12">
		<table class="table table-striped" style="max-height:600px">
			<thead>
				<tr class="info">
					<td><b>所在服</b></td>
					<td><b>玩家ID</b></td>
					<td><b>玩家名</b></td>
					<td><b>创建时间</b></td>
					<td><b>创建IP</b></td>
					<td><b>最后登陆时间</b></td>
					<td><b>最后登陆等级</b></td>
					<td><b>登陆次数</b></td>
					<td><b>充值金额($)</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in total">
					<td>{{t.server_name}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.created_time}}</td>
					<td>{{t.created_ip}}</td>
					<td>{{t.action_time}}</td>
					<td>{{t.last_level}}</td>
					<td>{{t.times}}</td>
					<td>{{t.pay_amount}}</td>
				</tr>
			</tbody>
		</table>
	</div>

	<div class="col-xs-12">
		<table class="table table-striped" style="max-height:600px">
			<thead>
				<tr class="info">
					<td><b>所在服</b></td>
					<td><b>玩家ID</b></td>
					<td><b>玩家名</b></td>
					<td><b>创建时间</b></td>
					<td><b>创建IP</b></td>
					<td><b>最后登陆时间</b></td>
					<td><b>登陆次数</b></td>
					<td><b>最后登陆等级</b></td>
					<td><b>连续未登陆天数</b></td>
					<td><b>充值金额($)</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in miss">
					<td>{{t.server_name}}</td>
					<td>{{t.player_id}}</td>
					<td>{{t.player_name}}</td>
					<td>{{t.created_time}}</td>
					<td>{{t.created_ip}}</td>
					<td>{{t.action_time}}</td>
					<td>{{t.times}}</td>
					<td>{{t.last_level}}</td>
					<td>{{t.miss_days}}</td>
					<td>{{t.pay_amount}}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>



