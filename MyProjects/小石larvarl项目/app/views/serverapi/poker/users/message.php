<script>
	function PokerSendMessage($http, $scope, alertService){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data;
				if (result.status=="ok") {
					alertService.add('success', result.msg);
				}else{
					alertService.add('danger', result.error);
				}
			}).error(function(data){
				alertService.add('danger', data.error);
			})
		}
	}
</script>
<div class="col-xs-12" ng-controller="PokerSendMessage">
	<div class="row">
		<div class="eb-content">
			<form method="post" action="" role="form" ng-submit="process('/game-server-api/poker/message')" onsubmit="return false">
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
					<textarea ng-model="formData.message" name="message" placeholder="<?php echo Lang::get('serverapi.enter_message')?>" cols="112" rows="10"></textarea>
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