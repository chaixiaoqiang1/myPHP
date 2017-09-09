<script type="text/javascript">
function sendAllServerGiftBagController($scope, $http, alertService, $filter)
{
    $scope.alerts = [];
    $scope.formData = {};
    $scope.start_time = null;
	$scope.end_time = null;

    $scope.processFrom = function(url) {
	        alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
        	$http({
	            'method' : 'post',
	            'url'    : url,
	            'data'   : $.param($scope.formData),
	            'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
	        }).success(function(data) {
	            $scope.time_period = data.time_period;
	            $scope.monetary_name = data.monetary_name;
	            $scope.server_name = data.server_name;
				$scope.items = data.items;
	        }).error(function(data) {
	            alertService.add('danger', JSON.stringify(data));
	        });
    };
}
</script>
<div class="col-xs-12" ng-controller="sendAllServerGiftBagController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/yysg/count-monetary"
				method="post" role="form"
				ng-submit="processFrom('/game-server-api/yysg/count-monetary')"
				onsubmit="return false;">
				<div class="form-group">
				<p>选择服务器</p>
					<select class="form-control" name="server_id" id="select_game_server" 
						ng-model="formData.server_id" ng-init="formData.server_id=0">
						<option value="0">请选择服务器</option>
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
				<p>选择消耗货币</p>
					<select class="form-control" name="monetary_type" id="monetary_type"
						ng-model="formData.monetary_type" ng-init="formData.monetary_type=0">
						<?php if('mnsg' == $game_code){ ?>
						<option value="0">钻石</option>
						<option value="1">金币</option>
						<option value="2">体力</option>
						<?php }else{ ?>
						<option value="0">元宝</option>
						<option value="1">铜钱</option>
						<option value="2">体力</option>
						<?php } ?>
					</select>
				</div>
				<div class="form-group" style="height: 30px;">
				<p>请选定时间段</p>
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

				<input type="submit" class="btn btn-danger" style="margin-top:10px;"
					value="<?php echo Lang::get('basic.btn_show') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>
		<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>统计时间段</b></td>
					<td><b>货币类型</b></td>
					<td><b>服务器</b></td>
					<td><b>消耗数量</b></td>
				</tr>
			</thead>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{time_period}}</td>
					<td>{{monetary_name}}</td>
					<td>{{server_name}}</td>
					<td>{{0-t.monetary_num}}</td>
				</tr>
			</tbody>
		</table>
		<div ng-show="!!pagination.totalItems">
			<pagination total-items="pagination.totalItems"
				page="pagination.currentPage" class="pagination-sm"
				boundary-links="true" rotate="false"
				items-per-page="pagination.perPage" max-size="10"></pagination>
		</div>
	</div>

	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>