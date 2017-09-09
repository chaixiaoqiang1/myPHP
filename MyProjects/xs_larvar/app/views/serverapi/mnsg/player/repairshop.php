<script>
	function RepairShop($http, $scope, alertService, $filter){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : '/game-server-api/mnsg/repair_player_shop',
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				alertService.add('success', data.msg);
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="RepairShop">
	<div class="row">
		<div class="eb-content">
			<form class="form-group" ng-submit="process()" onsubmit="return false" style="margin:aoto">
				<b>请输入需要修复商店的玩家ID，每行一个</b>
					<div class="form-group">
						<textarea required ng-model="formData.player_ids" class="col-xs-8" rows="10">
							
						</textarea>
					</div>
				<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-danger">
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