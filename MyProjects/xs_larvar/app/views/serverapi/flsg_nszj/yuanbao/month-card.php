<script>
	function monthCardController($scope, $http, alertService) {
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
				alertService.add('success', 'OK');
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="monthCardController">
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/month-card" method="post"
				role="form"
				ng-submit="processFrom('/game-server-api/month-card')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id"
						id="select_game_server" ng-model="formData.server_id"
						ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 0">
						<input type="text" class="form-control"
							ng-model="formData.order_sn" name="order_sn" required
							placeholder="<?php echo Lang::get('serverapi.enter_order_sn') ?>" />
					</div>
					<div class="col-md-6" style="padding: 2">
						<input type="text" class="form-control" ng-model="formData.uid"
							name="uid" required
							placeholder="<?php echo Lang::get('serverapi.enter_uid') ?>" />
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="form-group" style="height: 30px;">
					<div class="col-md-6" style="padding: 2">
						<input type="text" class="form-control" ng-model="formData.amount"
							name="amount" required
							placeholder="<?php echo Lang::get('serverapi.enter_yuanbao_amount') ?>" />
					</div>
					<div class="col-md-6" style="padding: 2">
						<select class="form-control" name="month_card_type"
							id="select_month_card_type" ng-model="formData.month_card_type"
							ng-init="formData.month_card_type=0">
							<option value="0"><?php echo Lang::get('serverapi.select_month_card_type') ?></option>
							<option value="1"><?php echo Lang::get('serverapi.month_card_type1') ?></option>
						</select>
					</div>
				</div>
				<div class="clearfix">
					<br />
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