<script>
	function setBusinessman($scope,$http,alertService){
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
				$scope.process2('/game-server-api/poker/getBusiness');
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};

		$scope.process2 = function(url){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.man = data.data;
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
	}
</script>	
<div id='query' class="col-xs-12" ng-controller="setBusinessman">

	<div class="form-group" style="height: 30px; margin-top:10px;">
		<div class="col-md-6" style="padding: 0 ;width:260px">
			<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.write_player_id');?>" name="player_id" ng-model="formData.player_id">
		</div>
		<input type='button' class="btn btn-primary"
			value="<?php echo '提交' ?>"
		ng-click="process('/game-server-api/poker/setBusiness')" />
		<input type='button' class="btn btn-primary"
			value="<?php echo '查询币商' ?>"
		ng-click="process('/game-server-api/poker/getBusiness')" />
	</div>
	<div>
		<select class="form-control" name="level" id="form_type"
			ng-model="formData.level" ng-init="formData.level=0">
			<option value="0"><?php echo "等级" ?></option>
			<?php foreach ($level as $k=>$v) { ?>
			<option value="<?php echo $k ?>"><?php echo $v;?></option>
			<?php } ?>		
		</select>
	</div>
	<br>
	<div class="row">
		<div class="eb-content">
			<table class="table table-striped">		
				<td>
					认证用户
					<table class="table table-striped">
						<tr ng-repeat="t in man.renzheng track by $index">
							<td>{{t}}</td>
						</tr>
					</table>
				</td>

				<td>
					普通用户
					<table class="table table-striped">
						<tr ng-repeat="t in man.feirenzheng track by $index">
							<td>{{t}}</td>
						</tr>
					</table>
				</td>
			</table>
		</div>
	</div>

</div>