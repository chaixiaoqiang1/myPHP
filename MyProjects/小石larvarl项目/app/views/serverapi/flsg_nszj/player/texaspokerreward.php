<script>
function sendRewardController($scope, $http, alertService) {
	$scope.alerts = [];
	$scope.formData = {};
	$scope.processFrom = function() {
        alertService.alerts = $scope.alerts;
        $http({
            'method': 'post',
            'url': '/game-server-api/poker/reward/money',
            'data': $.param($scope.formData),
            'headers': {
                'Content-Type': 'application/x-www-form-urlencoded'
            }
        }).success(function(data) {
            alertService.add('success', 'ok');
        }).error(function(data) {
            alertService.add('danger', data.error);
        });
		
	}	
}
</script>
<div class="col-xs-12" ng-controller="sendRewardController">
	<div class="row">
		<div class="eb-content">
			<form action="" method="post" role="form"
				ng-submit="processFrom()"
				onsubmit="return false;">
				<div class="form-group">
					<input type="text" class="form-control" placeholder="<?php echo Lang::get('serverapi.enter_uid') ?>" required ng-model="formData.uid" name="uid" />
				</div>
				<div class="form-group">
					<input type="text" class="form-control" placeholder="<?php echo Lang::get('serverapi.reward_money') ?>"
						required ng-model="formData.money" name="money" />
				</div>
				<input type="submit" class="btn btn-default"
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
</div>