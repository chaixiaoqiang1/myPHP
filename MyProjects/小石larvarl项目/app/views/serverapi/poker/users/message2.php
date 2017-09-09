<script>
	function PokerSendMessage2($http, $scope, alertService){
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
				var result1 = data.result1;
				var result2 = data.result2;
				//alert(result1.msg);
				if (result1.msg) {
					alertService.add('success', result1.msg);
				}
				if (result2.msg) {
					alertService.add('danger', result2.msg);
				}

			}).error(function(data){
				alertService.add('danger', data.error);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="PokerSendMessage2">
	<div class="row">
		<div class="eb-content">
		<form action="/game-server-api/poker/message-group" method="post" role="form" ng-submit="process('/game-server-api/poker/message-group')" onsubmit="return false;">
			<div class="from-group">
				<label>
					<input type="radio" ng-model="formData.type" name="type" ng-init="formData.type=1" value="1" ng-checked="true"><?php echo Lang::get('serverapi.send_by_player_name')?> 
				</label>
				<label>
					<input type="radio" ng-model="formData.type" name="type"  value="2"><?php echo Lang::get('serverapi.send_by_player_id')?> 
				</label>
			</div>
			<div class="form-group">
				<textarea name="players" ng-model="formData.players" cols="112" rows ="10" placeholder="<?php echo Lang::get('serverapi.enter_players')?>"></textarea>
			</div>
			<div class="form-group">
				<textarea name="message" ng-model="formData.message" cols="112" rows = "10" placeholder="<?php echo Lang::get('serverapi.enter_message')?>"></textarea>
			</div>
			<input type="submit" class="btn btn-danger" value="<?php echo Lang::get('basic.btn_submit')?>">
			</form>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>