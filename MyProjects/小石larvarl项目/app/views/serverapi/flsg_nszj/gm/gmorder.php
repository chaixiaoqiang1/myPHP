<script>
	function GmOrderController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function() {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/gm/order',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data.result;
				if (result[0].status == 'ok') {
					alertService.add('success', result[0].msg);
				}else if(result[0]['status'] == 'error'){
					alertService.add('danger', result[0].msg);
				}

			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
		$scope.close = function() {
			alertService.alerts = $scope.alerts;
			$scope.formData.is_close = 1;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/gm/order',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data.result;
				if (result[0].status == 'ok') {
					alertService.add('success', result[0].msg);
				}else if(result[0]['status'] == 'error'){
					alertService.add('danger', result[0].msg);
				}
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="GmOrderController">
	<div class="row">
		<div class="eb-content">
			<form method="post" ng-submit="process()" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" ng-model="formData.server_id" ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($servers as $key => $value) { ?>
						<option value="<?php echo $value->server_id?>"><?php echo $value->server_name;?></option>
						<?php } ?>	
					</select>
				</div>
                    <div><p class="text-danger"><?php echo Lang::get('serverapi.gm_order_tips')?></p></div>
			<div class="col-md-6" style="padding: 0">
					<div class="input-group">
						<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" />
					</div>
			</div>
			<?php if('flsg' == $game_code){?>
				<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<input type='button' ng-click="close('/game-server-api/cross/world-lookup')" 
							class="btn btn-warning" value="<?php echo Lang::get('basic.btn_close') ?>" />
						</div>
				</div>
			<?php }?>
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>