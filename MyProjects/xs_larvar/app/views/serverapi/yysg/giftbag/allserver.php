<script>
	function sendGiftBagController($scope, $http, alertService)
	{
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				//alertService.add('success', data.result);
				var result = data.result;
				var len = result.length;
				var i;
				for (i=0; i < len; i++) {
					if (result[i].status === 'ok') {
						alertService.add('success', result[i].msg);
					} else if (result[i].status === 'error') {
						alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(data) {
				alertService.add('danger', JSON.stringify(data));
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="sendGiftBagController">
	<div class="row" >
		<div class="eb-content">
			<form action="/game-server-api/gift-bag/all-server" method="post" role="form" ng-submit="processFrom('/game-server-api/gift-bag/all-server')" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="gift_bag_id" id="gift_bag_id" ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
					</select>
				</div>		
				<div class="form-group">
					<label>
						<input type="radio" ng-model="formData.send_type" name="send_type" ng-init="formData.send_type='name'" ng-checked="true" value="name"/>
						<?php echo Lang::get('serverapi.gift_for_name')?>
					</label>
					<label>
						<input type="radio" name="send_type" value="id" ng-model="formData.send_type"/>
						<?php echo Lang::get('serverapi.gift_for_id')?>
					</label>	
				</div>
				<div class="form-group">
					<textarea name="gift_data" ng-model="formData.gift_data"
						placeholder="<?php echo Lang::get('serverapi.all_server_tip') ?>"
						rows="15" required class="form-control"></textarea>
				</div>
				<input type="submit" class="btn btn-danger" value="<?php echo Lang::get('basic.btn_submit') ?>"/>	
			</form>	 
		</div><!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>