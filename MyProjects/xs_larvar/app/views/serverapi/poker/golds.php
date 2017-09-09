<script >
	function chipsController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.formData.num = 0;
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$scope.formData.num ++;
			if ($scope.formData.num < 2) {
				$http({
					'method' : 'post',
					'url'	 : url,
					'data'   : $.param($scope.formData),
					'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
				}).success(function(data) {
					alertService.add('success', data.result);
					$scope.formData.num = 0;
				}).error(function(data) {
					alertService.add('danger', data.error);
					$scope.formData.num = 0;
				});
			} else {
				alertService.add('danger', 'Waiting');
			}
		};
	}
</script>

<div class="col-xs-12" ng-controller="chipsController">
	<div class="row" >
		<div class="eb-content">
			<form action="/game-server-api/poker/golds" method="post" role="form" ng-submit="processFrom('/game-server-api/poker/golds')" onsubmit="return false;">		

				<div class="form-group" style="padding: 0px">
					<input type="text" class="form-control ng-pristine ng-invalid ng-invalid-required" 
							ng-model="formData.golds" name="golds" 
							placeholder="<?php echo Lang::get('serverapi.write_golds') ?>" />
				</div>
				<div class="form-group" style="height: 30px; ">
					<div class="col-md-6" style="padding: 0 ;width:260px">
						<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.write_player_id');?>" name="player_id" ng-model="formData.player_id">
					</div>
					<div class="col-md-6" style="padding: 2; width:260px">
						<input  class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.write_player_uid');?>" name="player_uid" ng-model="formData.player_uid">
					</div>
					<div class="col-md-6" style="padding: 2;width:260px">
						<input class="form-control ng-pristine ng-valid" type="text" placeholder="<?php echo Lang::get('serverapi.write_player_name');?>" name="player_name" ng-model="formData.player_name">
					</div>
				</div>
				
				<div class="col-md-6" style="padding: 0px">
					<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" style="display:block"/>	
				</div>
			</form>	 
		</div><!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

</div>