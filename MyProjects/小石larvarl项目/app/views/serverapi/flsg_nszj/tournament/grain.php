<script>
	function heavenGrainController($http, $scope, alertService){
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url){
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url' : url,
				'data' : $.param($scope.formData),
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
				alertService.add('danger', data.msg);
			});
		}
	}
</script>
<div class="col-xs-12" ng-controller="heavenGrainController">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
				<select class="form-control" name="server_id"
					id="select_game_server" ng-model="formData.server_id"
					ng-init="formData.server_id = 0" multiple="multiple"
					ng-multiple="true" size=20>
					<option value="0"><?php echo Lang::get('serverapi.select_main_game_server') ?></option>
					<optgroup>
						<?php foreach ($server as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
					</optgroup>
				</select>
			</div>
			<div class="form-group" style="height: 30px;">
					<span style = "color:red; font-size:16px;"><?php echo Lang::get('serverapi.heaven_grain_introduce')?><a href="/game-server-api/player/escort" target="_blank">玩家护送状态(点击可打开操作界面)</a>功能修改</span>
				</div>
			<div class="form-group" style="height: 40px;">
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.open_heaven_grain') ?>"
						ng-click="process('/game-server-api/heaven/grain?action=open')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.look_heaven_grain') ?>"
						ng-click="process('/game-server-api/heaven/grain?action=look')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-danger"
						value="<?php echo Lang::get('serverapi.close_heaven_grain') ?>"
						ng-click="process('/game-server-api/heaven/grain?action=close')" />
				</div>
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