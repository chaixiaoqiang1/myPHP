<script>
	function PokerFreezeGroupController($scope, $http, alertService){
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
				var result1 = data.result1;
				var result2 = data.result2;
				if (result1.msg) {
					alertService.add('success', result1.msg);
				}
				if (result2.msg) {
					alertService.add('danger', result2.msg);
				}

				
			}).error(function(data){
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="PokerFreezeGroupController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/poker/item-group" ng-submit="process('/game-server-api/poker/item-group')" onsubmit="return false;">
				<div class="from-group">
					<label>
						<input type="radio" ng-model="formData.type" name="type" ng-init="formData.type=1" value="1" ng-checked="true"><?php echo Lang::get('serverapi.send_by_player_id')?> 
					</label>
					<label>
						<input type="radio" ng-model="formData.type" name="type"  value="2"><?php echo Lang::get('serverapi.send_by_player_name')?> 
					</label>
				</div>
				
				<div class="form-group">
					<textarea name="player" ng-model="formData.player" cols="112" rows = "10" placeholder="<?php echo Lang::get('serverapi.enter_players')?>"></textarea>
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
					<input type="text" class="form-control" ng-model="formData.num" name="num" placeholder="<?php echo Lang::get('serverapi.enter_item_num') ?>" />
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