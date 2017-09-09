<script>
	function LogSearchController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/mobilegame/onlinenum',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data.result;
				$scope.total = data.total;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="LogSearchController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group">
                <select class="form-control" name="server_id"
                        id="select_game_server" ng-model="formData.server_id"
                        ng-init="formData.server_id=0" multiple="multiple"
                        ng-multiple="true" size=10>
                        <option value="0">选择全部服务器</option>
                        <?php foreach ($servers as $k => $v) { ?>
                            <option value="<?php echo $v->server_id ?>"><?php echo $v->server_name; ?></option>
                        <?php } ?>
                </select>
            </div>
			<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_check') ?>" />
					</div>
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
	<div class="col-xs-12">
		<table class="table table-striped">
			<thead>
				<tr class="info">
					<td><b>游戏服务器</b></td>
					<td><b>在线人数</b></td>
					<td><b>当前时间</b></td>
				</tr>
			</thead>
			<tbody>
				<tr>
				    <td>{{total.total}}</td>
				    <td>{{total.total_online}}</td>
				    <td>{{total.time}}</td>
				</tr>
				<tr>
				    <td></td>
				    <td></td>
				    <td></td>
				</tr>
			</tbody>
			<tbody>
				<tr ng-repeat="t in items">
					<td>{{t.server_name}}</td>
					<td>{{t.online_num}}</td>
					<td>{{t.time}}</td>
				</tr>
			</tbody>
		</table>
		
	</div>
</div>