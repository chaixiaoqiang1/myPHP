<script>
	function sendAllServerGiftBagController($scope, $http, alertService){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.processFrom = function(url) {
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			if(!confirm("将发送给夜夜三国所有地区七天内登陆过的玩家，是否确定？")){
				return;
			}
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.msg);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="sendAllServerGiftBagController">
	<div class="row">
		<div class="col-xs-8">
			<form action="" method="post" role="form"
				ng-submit="processFrom('/game-server-api/yysg/gift-bag/all-server')"
				onsubmit="return false;">
				<div class="form-group">
					<div class="form-group col-md-6">
						<select class="form-control" name="server_id" id="select_game_server" ng-init="formData.server_id=0" ng-model="formData.server_id" >
							<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
							<?php foreach ($servers as $k => $v) { ?>
								<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
							<?php } ?>		
						</select>
					</div>
				</div>
				<div class="form-group">
					<div class="form-group col-md-6">
						<select class="form-control" name="gift_bag_id" id="gift_bag_id"
							ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
							<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name.'('.$v->desc.')';?></option>
						<?php } ?>		
					</select>
					</div>
				</div>

				<div class="form-group">
					<input type="submit" class="btn btn-danger"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
				</div>
				<div class="form-group">
					<b style="color:red">Note:<?php echo Lang::get('slave.yysg_all_server_note'); ?></b>
				</div>
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