<script>
	function FirstRechargeController($scope, $http, alertService){
		$scope.formData = {};
		$scope.alerts = [];
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data;
				for (var i = 0; i < result.length; i++) {
					if (result[i].statu == 'OK') {
						alertService.add('success', result[i].msg);
					}else{
						alertService.add('danger', result[i].msg);
					}
				};
			}).error(function(data){
				alertService.add('danger', data.error);
			})
		}
	}
</script>
<div class="col-xs-12" ng-controller="FirstRechargeController">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
				<select class="form-control" name="server_id" id="select_game_server" ng-model = "formData.server_id" ng-init="formData.server_id = 0" multiple="multiple" ng-multiple="true" size = 10>
					<option value="0"><?php echo Lang::get('serverapi.select_game_server')?></option>
					<?php foreach ($servers as $key => $value) { ?>
						<option value="<?php echo $value->server_id?>"> <?php echo $value->server_name?></option>
					<?php }?>
				</select>
			</div>

			<div class="form-group" style="height: 30px;">
				<br/>
				<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.recharge_introduce1')?></span>
			</div>
			<br/>
			<div class="form-group" style="height: 40px;">
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.promotion_set') ?>"
						ng-click="process('/game-server-api/recharge/first?type=open')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.promotion_lookup') ?>"
						ng-click="process('/game-server-api/recharge/first?type=look')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-danger"
						value="<?php echo Lang::get('serverapi.promotion_close') ?>"
						ng-click="process('/game-server-api/recharge/first?type=close')" />
				</div>
			</div>
				
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>