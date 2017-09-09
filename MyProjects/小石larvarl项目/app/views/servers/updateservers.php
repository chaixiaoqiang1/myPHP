<script>
	function UpdateServersController($scope, $http, alertService) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.total = {};
		$scope.details = {};
		$scope.processFrom = function(type) {
			$scope.details = {};
			$scope.formData.submittype = type;
			$scope.alerts = [];
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : '/update/servers',
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				$scope.details = data;
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>
<div class="col-xs-12" ng-controller="UpdateServersController">
	<div class="row">
		<div class="eb-content">
			<form action="" method="get" role="form"
				ng-submit="processFrom('sunbmit')"
				onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="update_type"
						id="update_type" ng-model="formData.update_type" ng-init="formData.update_type=0">
						<option value="0"><?php echo Lang::get('serverapi.select_update_type') ?></option>
						<?php if($show_type_s){ ?><option value="3"><?php echo Lang::get('serverapi.update_type_s') ?></option>	<?php } ?>
						<option value="1"><?php echo Lang::get('serverapi.update_type_f') ?></option>
						<option value="2"><?php echo Lang::get('serverapi.update_type_b') ?></option>	
					</select>
				</div>
				<input type="submit" class="btn btn-default" style=""
					value="<?php echo Lang::get('basic.btn_submit') ?>" />
				<input type="button" class="btn btn-default" style="margin-left:60px"
					value="<?php echo Lang::get('basic.btn_check_process') ?>" ng-click="processFrom('check')" />
				<div class="form-group" ng-if="formData.update_type == 2" style="margin-top:20px">
					<select class="form-control" name="server_ids"
						id="select_game_server" ng-model="formData.server_ids" required multiple="true" size="20">
						<option value="0"><?php echo Lang::get('serverapi.select_all_server') ?></option>
						<?php foreach ($servers as $k => $v){ ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</select>
					<p style="color:red;text-align:center;font-size:18px">注意：后端更全服，此处全服指的是cres对应的服务器</p>
				</div>
				<div class="form-group" ng-if="formData.update_type == 1"  style="margin-top:20px">
					<select class="form-control" name="reses"
						id="reses" ng-model="formData.reses" required multiple="true" size="5">
						<?php foreach ($res as $v) { ?>
							<option value="<?php echo $v->version?>"><?php echo $v->version;?></option>
						<?php } ?>		
					</select>
					<p style="color:red;text-align:center;font-size:18px">注意：上次更新查询结果成功后才能进行下次更新</p>
				</div>
				<div class="form-group" ng-if="formData.update_type == 3"  style="margin-top:20px">
					<select class="form-control" name="language"
						id="language" ng-model="formData.language" required multiple="true" size="5">
						<?php foreach ($type_s_types as $k => $v) { ?>
							<option value="<?php echo $k?>"><?php echo $v;?></option>
						<?php } ?>		
					</select>
					<p style="color:red;text-align:center;font-size:18px"></p>
				</div>
			</form>
		</div>
		<div class="col-xs-4" style="margin-left:40px;margin-top:20px">
			<span ng-repeat="t in details">
				{{t.msg}}<br>
			</span>
		</div>
	</div>
	<div class="row margin-top-10">
		<div class="eb-content">
			<alert ng-repeat="alert in alerts" type="alert.type"
				close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>
</div>