<script>
	function GSController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(type) {
			alertService.alerts = $scope.alerts;
			$scope.formData.type = type;
			$http({
				'method' : 'post',
				'url'	 : '/game-server-api/gs',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		}
		$scope.setSuperGM = function() {
			$scope.process('setSuperGM');
		}
		$scope.setSuperCustomer = function() {
			$scope.process('setSuperCustomer');
		}
		$scope.contact = function() {
			$scope.process('contact');
		}
	}
</script>
<div class="col-xs-12" ng-controller="GSController">
	<div class="row">
		<div class="eb-content">
			<div class="well">
				<div class="form-group">
					<select class="form-control" name="server_id1"
						id="select_game_server1" ng-model="formData.server_id1"
						ng-init="formData.server_id1=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="player_name1"
						placeholder="<?php echo Lang::get('serverapi.enter_super_gm_name') ?>"
						required ng-model="formData.player_name1" name="player_name1" />
				</div>
				<div class="form-group">
					<label> <input type="checkbox" ng-init="formData.is_super_gm=0"
						ng-true-value="1" ng-false-value="0"
						ng-model="formData.is_super_gm" />
						<?php echo Lang::get('serverapi.is_super_gm')?>
					</label>
				</div>
				<input type="submit" class="btn btn-primary"
					value="<?php echo Lang::get('serverapi.setSuperGM') ?>"
					ng-click="setSuperGM()" />
			</div>
			<div class="well">
				<div class="form-group">
					<select class="form-control" name="server_id2"
						id="select_game_server2" ng-model="formData.server_id2"
						ng-init="formData.server_id2=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<input type="text" class="form-control" id="player_name2"
						placeholder="<?php echo Lang::get('serverapi.enter_super_customer_name') ?>"
						required ng-model="formData.player_name2" name="player_name2" />
				</div>
				<div class="form-group">
					<label> <input type="checkbox"
						ng-init="formData.is_super_customer=0" ng-true-value="1"
						ng-false-value="0" ng-model="formData.is_super_customer" />
						<?php echo Lang::get('serverapi.is_super_customer')?>
					</label>
				</div>
				<input type="submit" class="btn btn-danger"
					value="<?php echo Lang::get('serverapi.setSuperCustomer') ?>"
					ng-click="setSuperCustomer()" />
			</div>
			<div class="well">
				<div class="form-group">
					<select class="form-control" name="server_id3"
						id="select_game_server3" ng-model="formData.server_id3"
						ng-init="formData.server_id3=0">
						<option value="0"><?php echo Lang::get('serverapi.select_game_server') ?></option>
						<?php foreach ($servers as $k => $v) { ?>
						<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
				</div>
				<div class="form-group">
					<div class="col-md-6" style="padding: 0">
						<input type="text" class="form-control" id="player_name31"
							placeholder="<?php echo Lang::get('serverapi.enter_super_customer_name') ?>"
							required ng-model="formData.player_name31" name="player_name31" />
					</div>
					<div class="col-md-6" style="padding: 2">
						<input type="text" class="form-control" id="player_name32"
							placeholder="<?php echo Lang::get('serverapi.enter_super_gm_name') ?>"
							required ng-model="formData.player_name32" name="player_name32" />
					</div>
				</div>
				<div class='clearfix'>
					<br />
				</div>
				<div class="form-group">
					<label> <input type="checkbox"
						ng-init="formData.gs_add_or_remove=0" ng-true-value="1"
						ng-false-value="0" ng-model="formData.gs_add_or_remove" />
						<?php echo Lang::get('serverapi.gs_add_or_remove')?>
					</label>
				</div>
				<input type="submit" class="btn btn-primary"
					value="<?php echo Lang::get('serverapi.setGSContact') ?>"
					ng-click="contact()" />
			</div>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>