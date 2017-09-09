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
	<div class="row">
		<div class="eb-content">
			<form action="/game-server-api/gift-bag/single-server" method="post" role="form"
				ng-submit="processFrom('/game-server-api/gift-bag/single-server')"
				onsubmit="return false;">

				<div class="form-group">
					<select class="form-control" name="server_id" id="server_id"
						ng-model="formData.server_id" ng-init="formData.server_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="row" style="height: 30px;">
					<div class="col-lg-6">
						<select class="form-control" name="gift_bag_id" id="gift_bag_id"
							ng-model="formData.gift_bag_id" ng-init="formData.gift_bag_id=0">
							<option value="0"><?php echo Lang::get('serverapi.select_gift_bag') ?></option>
						<?php foreach ($gifts as $k => $v) { ?>
						<option value="<?php echo $v->id?>"><?php echo $v->id . ':' . $v->name;?></option>
						<?php } ?>		
					</select>
					</div>
					<div class="col-lg-6">
						<input type="text" class="form-control"
							ng-model="formData.password" name="password"
							placeholder="<?php echo Lang::get('serverapi.gift_bag_online_password') ?>" />
					</div>
				</div>
				<div>
					</br>
				</div>
				<div class="row" style="height: 30px;">
					<div class="col-lg-6">
						<div class="input-group">
						 	<input type="text" class="form-control"
								ng-model="formData.player_name" name="player_name"
								placeholder="<?php echo Lang::get('serverapi.enter_player_name') ?>" />
						</div>
					</div>
					<div class="col-lg-6">
						<div class="input-group">
 							<input type="text" class="form-control"
								ng-model="formData.player_id" name="player_id"
								placeholder="<?php echo Lang::get('serverapi.enter_player_id') ?>" />
						</div>
					</div>
				</div>
				<div>
					</br>
				</div>
				<div class="form-group">
					<label>
						<input type="radio" ng-model="formData.send_type" name="send_type" value="" ng-init="formData.send_type='name'" ng-checked="true" value="name"/>
						<?php echo Lang::get('serverapi.gift_for_name')?>
					</label>
					<label>
						<input type="radio" name="send_type" value="id" ng-model="formData.send_type"/>
						<?php echo Lang::get('serverapi.gift_for_id')?>
					</label>	
				</div>
				<div class="form-group">
					<textarea name="player_id_or_names" ng-model="formData.player_id_or_names"
						placeholder="<?php echo Lang::get('serverapi.gift_bag_batch') ?>"
						rows="15" class="form-control"></textarea>
				</div>
				<input type="submit" class="btn btn-danger"
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
			</form>
		</div>
		<!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<div class="col-md-4">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
			</div>
			<div class="col-md-4">
				<a href="/game-server-api/giftbag/lookuppage?app_id=33" target="_blank"><?php echo Lang::get('serverapi.search_gift_record')?></a>
			</div>
		</div>
		
	</div>
</div>