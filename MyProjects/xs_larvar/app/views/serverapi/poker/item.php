<script>
	function PokerItemController($http, $scope, alertService){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
				'headers': {'Content-Type': 'application/x-www-form-urlencoded'}
			}).success(function(data){
				var result = data;
				if (result.status == 'OK') {
					alertService.add('success', result.msg)
				}else if(result.status == 'error'){
					alertService.add('danger', result.msg);
				}
			}).error(function(data){
				alertService.add('danger', data.error);
			}) 
		}
	}
</script>
<div class="col-xs-12" ng-controller="PokerItemController">
	<div class="row">
		<div class="eb-content">
			<form method="post" action="" role="form" ng-submit="process('/game-server-api/poker/item')" onsubmit="return false">
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
					<select class="form-control" name="item_id" ng-model="formData.item_id" ng-init="formData.item_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_item')?></option>
						<?php foreach ($items as $key => $value) {?>
							<option value="<?php echo $value->Id?>"><?php echo $value->Id .'--'.$value->Name?></option>
						<?php }?>

					</select>
				</div>
				<div class="form-group" style="padding:0px">
					<input type="text" class="form-control " 
							ng-model="formData.num" name="num" 
							placeholder="<?php echo Lang::get('serverapi.enter_item_num') ?>" />
				</div>
				<input type="submit" value="<?php echo Lang::get('basic.btn_submit')?>" class="btn btn-primary">
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