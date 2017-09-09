<script>
	function getExpenseController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.items = [];
		$scope.show = 0;
		$scope.processFrom = function() {
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.show = 0;
			$scope.alerts = [];
			$scope.items = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/slave-api/economy/expensesum	',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.show = 1;
				$scope.items = data.result;
				$scope.server = data.allserver;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="getExpenseController">
	<div class="row" id="top">
		<div class="eb-content">
			<form action="/slave-api/economy/parts" method="get" role="form"
				ng-submit="processFrom()" onsubmit="return false;">
				<div class="form-group col-md-8" style="padding-left:0;">
					<select class="form-control" name="server_internal_id"
						id="server_internal_id" ng-model="formData.server_internal_id" ng-init = "formData.server_id = 0"
						size="15" multiple="multiple" required>
						<option value="0">选择全部服务器</option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_internal_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>	
					</select>
				</div>
				<div class="form-group col-md-8" >
					<input type="number" class="form-control" name="interval"
						id="interval" ng-model="formData.interval" required onkeyup="this.value=this.value.replace(/\D/g,'')" onafterpaste="this.value=this.value.replace(/\D/g,'')" placeholder="输入间隔的天数">
				</div>
				<div class="form-group">
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
				</div>
				<div class="form-group col-md-8" >
					<br><font color="#F00">夜夜三国不能查询在线人数</font>
				</div>
				
				<div class="clearfix"></div>
				<br>
				<input type="submit" class="btn btn-danger"
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

	<div class="col-xs-12">
		<table class="table table-striped" ng-if="show == 1">
			<thead>
				<tr class="info">
					<!-- <td><b>服务器id</b></td> -->
					<td><b>服务器名称</b></td>
					<td><b>日期</b></td>
					<td><b>付费总金额</b></td>
					<td><b>付费用户总数</b></td>
					<td><b>付费用户ARPU值</b></td>
					<td><b>在线用户人数</b></td>
					<td><b>平均在线ARPU值</b></td>
					<td><b>创建角色人数</b></td>
					<td><b>创建角色ARPU值</b></td>
					<td><b>注册用户人数</b></td>
					<td><b>注册用户ARPU值</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in server">
		
					<td>{{t.server_name}}</td>
					<td>{{t.date}}</td>
					<td>{{t.sum_dollar | number:2}}</td>
					<td>{{t.player_num}}</td>
					<td ng-if="t.player_num == 0">0.00</td>
					<td ng-if="t.player_num != 0">{{t.sum_dollar/t.player_num | number:2}}</td>
					<td>{{t.avg_online_value | number:0}}</td>
					<td ng-if="t.avg_online_value == 0">0.00</td>
					<td ng-if="t.avg_online_value != 0">{{t.sum_dollar/t.avg_online_value | number:2}}</td>
					<td>{{t.create_num}}</td>
					<td ng-if="t.create_num == 0">0.00</td>
					<td ng-if="t.create_num != 0">{{t.sum_dollar/t.create_num | number:2}}</td>
					<td>{{t.usernum}}</td>
					<td ng-if="t.usernum == 0">0.00</td>
					<td ng-if="t.usernum != 0">{{t.sum_dollar/t.usernum | number:2}}</td>
				</tr>

			</tbody>
		</table>
	</div>

	<div class="col-xs-12">
		<table class="table table-striped" ng-if="show == 1" ng-repeat="t in items">
			<thead>
				<tr class="info">
					
					<td><b>服务器名称</b></td>
					<td><b>日期</b></td>
					<td><b>付费总金额</b></td>
					<td><b>付费用户总数</b></td>
					<td><b>付费用户ARPU值</b></td>
					<td><b>在线用户人数</b></td>
					<td><b>平均在线ARPU值</b></td>
					<td><b>创建角色人数</b></td>
					<td><b>创建角色ARPU值</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="d in t">
					<td>{{d.server_name}}</td>
					<td>{{d.date}}</td>
					<td>{{d.sum_dollar}}</td>
					<td>{{d.player_num}}</td>
					<td ng-if="d.player_num == 0">0.00</td>
					<td ng-if="d.player_num != 0">{{d.sum_dollar/d.player_num | number:2}}</td>
					<td>{{d.avg_online_value | number:0}}</td>
					<td ng-if="d.avg_online_value == 0">0.00</td>
					<td ng-if="d.avg_online_value != 0">{{d.sum_dollar/d.avg_online_value | number:2}}</td>
					<td>{{d.create_num}}</td>
					<td ng-if="d.create_num == 0">0.00</td>
					<td ng-if="d.create_num != 0">{{d.sum_dollar/d.create_num | number:2}}</td>
				</tr>

			</tbody>
		</table>
	</div>
</div>