<script>
	function PokerDeleteChipsController($scope, $http, alertService){
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
<div class="col-xs-12" ng-controller="PokerDeleteChipsController">
	<div class="row">
		<div class="eb-content">
			<form method="post" action="" role="form" ng-submit="process('/game-server-api/poker/delete')" onsubmit="return false">
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
					<input type="text" class="form-control"  ng-model="formData.chips" name="chips" 
							placeholder="<?php echo Lang::get('serverapi.enter_player_chips') ?>" />
				</div>
				<div class="col-md-6" style="padding: 0 ;width:800px">
					<select class="form-control" name="content" ng-model="formData.type" ng-init="formData.type=0">
					<option value="0"><?php echo Lang::get('pokerData.defaultContent')?></option>
					<?php foreach ($type as $key => $value) {?>
					<option value="<?php echo $key+1; ?>"><?php echo $value ?></option>
					<?php }?>
					</select>
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