<script>
	function sameStrongboxPasswd($scope,$http,alertService){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items = data;
				$scope.items2 = null;
				$scope.items3 = null;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};

		$scope.process2 = function() {
			// $scope.formData.player_id = target.getAttribute('player_id');
			alertService.alerts = $scope.alerts;
			$url = '/game-server-api/poker/sameStrongboxPasswdPlayer';
			$http({
				'method' : 'post',
				'url'	 : $url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items2 = data;
				$scope.items = null;
				$scope.items3 = null;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};

		$scope.process3 = function() {
			// $scope.formData.password = target.getAttribute('password');
			alertService.alerts = $scope.alerts;
			$url = '/game-server-api/poker/sameStrongboxPasswdPassword';
			$http({
				'method' : 'post',
				'url'	 : $url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.items3 = data;
				$scope.items = null;
				$scope.items2 = null;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		}
	}

</script>
<div id='query' class="col-xs-12" ng-controller="sameStrongboxPasswd">
	<div class="row">
		<div class="eb-content">
			<div class="clearfix">
				<br />
			</div>


			<div class="form-group" style="height: 30px; margin-top:10px;">
				<div class="col-md-10" style="padding: 0">
				<input type='button' class="btn btn-primary"
					value="<?php echo '全服所有相同密码查询' ?>"
					ng-click="process('/game-server-api/poker/sameStrongboxPasswd')" />
				</div>
			</div>


			<div class="form-group" style="height: 30px;">
				<div class="col-md-2" style="padding: 0 ;width:330px">
					<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.write_player_id');?>" name="player_id" ng-model="formData.player_id">
				</div>
				<div class="col-md-2" style="padding: 30">
					<input type='button' class="btn btn-primary"
						value="<?php echo '找出和该玩家相同密码的其他玩家' ?>"
						ng-click="process2()" />
				</div>
			</div>

			<div class="form-group" style="height: 30px;">
				<div class="col-md-2" style="padding: 0 ;width:330px">
					<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.write_strongbox_password');?>" name="password" ng-model="formData.password">
				</div>
				<div class="col-md-2" style="padding: 30">
					<input type='button' class="btn btn-primary"
						value="<?php echo '找出使用该密码的所有玩家' ?>"
						ng-click="process3()" />
				</div>>

			</div			<br>
			<br>

			<div class="col-xs-12">
				<table class="table table-striped">
						<thead>
							<tr class='info'>
								<td>密码</td>
								<td>相同密码玩家ID</td>
							</tr>
						</thead>
						<tbody>
							<tr ng-repeat="item in items">
								<td>{{item['password']}}</td>
								<td>{{item['name']}}</td>
							</tr>
							<tr ng-repeat="item in items2">
								<td>{{}}</td>
								<td>{{item}}</td>
							</tr>
							<tr ng-repeat="item in items3">
								<td>{{}}</td>
								<td>{{item}}</td>
							</tr>
						</tbody>
				</table>
			</div>
		</div>
	</div>

</div>