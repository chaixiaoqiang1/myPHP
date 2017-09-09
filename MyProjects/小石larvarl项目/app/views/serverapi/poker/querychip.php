<script>
	function queryChip($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.start_time=null;
		$scope.end_time=null;
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
		$scope.lookup = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
		$scope.changequeryChip = function(target) {
			$scope.formData.player_id = target.getAttribute('data');
			alertService.alerts = $scope.alerts;
			$scope.formData.start_time = $filter('date')($scope.start_time, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.end_time = $filter('date')($scope.end_time, 'yyyy-MM-dd HH:mm:ss');
			$url = '/game-server-api/poker/querychip';
			$http({
				'method' : 'post',
				'url'	 : $url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		}
	}
</script>
<div id='query' class="col-xs-12" ng-controller="queryChip">
	<div class="row">
		<div class="eb-content">
			<div class="clearfix">
				<br />
			</div>

			<div class="form-group">
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="start_time" init-value="00:10:00"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
				<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<quick-datepicker ng-model="end_time" init-value="23:50:59"></quick-datepicker>
						<i class="glyphicon glyphicon-calendar"></i>
					</div>
				</div>
			</div>

			<br>

			<div class="form-group" style="height: 30px; margin-top:10px;">
				<div class="col-md-6" style="padding: 0 ;width:260px">
					<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.write_player_id');?>" name="player_id" ng-model="formData.player_id">
				</div>
			</div>

			<div class="col-md-4" style="padding: 0">
				<input type='button' class="btn btn-primary"
					value="<?php echo '查询' ?>"
					ng-click="process('/game-server-api/poker/querychip')" />
			</div>

			<div class="col-xs-12">
				<table class="table table-striped">
					<thead>
						<tr class="info">
							<td>Player ID</td>
							<td>Delta</td>
						</tr>
					</thead>
					<tbody>
						<tr ng-repeat='t in items'>
							<td><a href="" ng-click="changequeryChip($event.target)" data={{t.id}}>{{t.id}}</a></td>
							<td>{{t.delta}}</td>
						</tr>
					</tbody>
				</table>
			</div>

		</div>
	</div>

</div>