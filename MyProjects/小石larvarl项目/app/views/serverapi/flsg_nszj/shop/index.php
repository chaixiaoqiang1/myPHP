<script>
	function shopController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.shop = {};
		$scope.open_time_from = null;
		$scope.open_time_to = null;
		$scope.$watch('open_time_from', function(){
			console.log($scope.open_time_from);
		});	
		$scope.$watch('open_time_to', function(){
			console.log($scope.open_time_to);
		});	
		$scope.process = function(type) {
			if (type !== 'status' && !confirm('Are you sure?')) {
				return;
			}
			alertService.alerts = $scope.alerts;
			$scope.formData.type = type;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/shop',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				if (type == 'status') {
					$scope.shop = data;
				} else {
					$scope.loadShop();
				}
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}

		
		$scope.loadShop = function() {
			$scope.process('status');
		}

		$scope.openShop = function() {
			$scope.process('open');
		}

		$scope.closeShop = function() {
			$scope.process('close');
		}

		$scope.openLimit = function() {
			$scope.formData.open_time_from = $filter('date')($scope.open_time_from, 'yyyy-MM-dd HH:mm:ss');
			$scope.formData.open_time_to = $filter('date')($scope.open_time_to, 'yyyy-MM-dd HH:mm:ss');
			$scope.process('open_limit');
		}

		$scope.closeLimit = function() {
			$scope.process('close_limit');
		}

		$scope.onItem = function() {
			$scope.process('on_item');
		}

		$scope.offItem = function(shop_id) {
			$scope.formData.shop_id = shop_id;
			$scope.process('off_item');
		}
	}
</script>
<div class="col-xs-12" ng-controller="shopController">
	<div class="row">
		<div class="eb-content">
			<div class="well">
					<select class="form-control" name="server_id" id="select_game_server" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size=10
					ng-change="loadShop()">
						<optgroup label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
			</div>

			<div class="well">
				<input ng-if="shop.is_shop_open == false" type="button"
					class="btn btn-primary"
					value="<?php echo Lang::get('serverapi.open_shop') ?>"
					ng-click="openShop()" /> <input ng-if="shop.is_shop_open == true"
					type="button" class="btn btn-danger"
					value="<?php echo Lang::get('serverapi.close_shop') ?>"
					ng-click="closeShop()" />		
			</div>	
			<div class="well"
				ng-if="shop.is_shop_open == true && shop.bonus_shop_open_time > 0 && shop.bonus_shop_close_time > 0">
				<p><?php echo Lang::get('serverapi.open_shop_time') ?>: {{shop.bonus_shop_open_time_date}}</p>
				<p><?php echo Lang::get('serverapi.close_shop_time') ?>: {{shop.bonus_shop_close_time_date}}</p>
				<p ng-if="shop.bonus_shop_open == true">
						<?php echo Lang::get('serverapi.bonus_shop_open_yes')?>
					</p>
				<p ng-if="shop.bonus_shop_open == false">
						<?php echo Lang::get('serverapi.bonus_shop_open_no')?>
					</p>
				<p>
					<input type="button" class="btn btn-danger"
						value="<?php echo Lang::get('serverapi.close_time_limit') ?>"
						ng-click="closeLimit()" />
				</p>
			</div>

			<div class="well"
				ng-show="shop.is_shop_open == true && shop.bonus_shop_open_time == 0">
				<div class="form-group">
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="open_time_from" init-value="00:10:00"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
					<div class="col-md-6" style="padding: 0">
						<div class="input-group">
							<quick-datepicker ng-model="open_time_to" init-value="23:50:59"></quick-datepicker>
							<i class="glyphicon glyphicon-calendar"></i>
						</div>
					</div>
				</div>
				<div class='clearfix'>
					<br />
				</div>
				<input type="button" class="btn btn-primary"
					value="<?php echo Lang::get('serverapi.open_time_limit') ?>"
					ng-click="openLimit()" />
			</div>

			<div class="well" ng-if="shop.is_shop_open == true">
				<div class="form-group">
					<select class="form-control" name="shop_id"
						ng-model="formData.shop_id" ng-init="formData.shop_id=0">
						<option value="0"><?php echo Lang::get('serverapi.select_shop_item') ?></option>
							<?php foreach ($items as $k => $v) { ?>
							<option value="<?php echo $v->id?>"><?php echo $v->itemid . ':' . $v->help;?></option>
							<?php } ?>		
						</select>
				</div>
				<input type="button" class="btn btn-primary"
					value="<?php echo Lang::get('serverapi.on_shop_item') ?>"
					ng-click="onItem()" />
			</div>
		</div>

		<div class="form-group col-xs-6">
			<span style = "color:red; font-size:16px">提醒：开启限时抢购时，开始时间不能小于当前时间，结束时间不能小于开始时间</span>
			</div>
		</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
	<div class="row margin-top-10" ng-if="shop.is_shop_open == true">
		<div class="col-xs-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th><?php echo Lang::get('serverapi.shop_item_name') ?></th>
						<th><?php echo Lang::get('serverapi.shop_item_id') ?></th>
						<th><?php echo Lang::get('serverapi.shop_item_active') ?></th>
					</tr>
				</thead>
				<tbody>
					<tr ng-repeat="item in shop.items">
						<td>{{item.item_id}}</td>
						<td>{{item.item_name}}</td>
						<td ng-if="item.active == true">
							<button class="btn btn-danger" ng-click="offItem(item.id)">
								<?php echo Lang::get('serverapi.off_shop_item')?>
							</button>
						</td>
						<td ng-if="item.active == false">
							<?php echo Lang::get('serverapi.off_shop_item_done')?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>