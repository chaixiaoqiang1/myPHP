<script>
	function PokerFreezeController($scope, $http, alertService){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : url,
				'data' :$.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data;
				if (result.status == 'ok') {
					alertService.add('success', result.msg);
				}else{
					alertService.add('danger', result.msg);
				}
				
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="PokerFreezeController">
	<div class="row">
		<div class="eb-content">
			<form method="post" action="" role="form" ng-submit="process('/game-server-api/poker/freeze')" onsubmit="return false">
				<div class="form-group" style="padding:0px">
					<input type="text" class="form-control " 
							ng-model="formData.player_name" name="player_name" 
							placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>" />
				</div>
				<div class="form-group" style="padding:0px">
					<input type="text" class="form-control " 
							ng-model="formData.player_id" name="player_id" 
							placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>" />
				</div>
				<div class="form-group" style="padding:0px">
					<select class="form-control" name="is_freeze" ng-model="formData.is_freeze" ng-init="formData.is_freeze=2">
						<option value="2"><?php echo Lang::get('serverapi.enter_freeze')?></option>
						<option value="1"><?php echo Lang::get('serverapi.select_freeze')?></option>
						<option value="0"><?php echo Lang::get('serverapi.select_freeze2')?></option>
					</select>
				</div>
				<div class="form-group" style="padding:0px">
					<input type="text" class="form-control" name="why_freeze" ng-model="formData.why_freeze" required placeholder="Why?" />
				</div>
				<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-primary">
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>