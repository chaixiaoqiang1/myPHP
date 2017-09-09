<script>
	function NsLonelyController($scope, $http, alertService, $filter) {
		$scope.alerts = [];
		$scope.formData = {};
		$scope.process = function(url) {
			alertService.alerts = $scope.alerts;
			$http({
				'method' : 'post',
				'url'	 : url,
				'data'   : $.param($scope.formData),
				'headers': {'Content-Type' : 'application/x-www-form-urlencoded'}
			}).success(function(data) {
				var result = data;
				var len = result.length;
				for (var i=0; i < len; i++) {
					if (result[i].status == 'OK') {
						alertService.add('success', result[i].msg);
					} else if (result[i]['status'] == 'error') {
	            		alertService.add('danger', result[i].msg);
					}
				}
			}).error(function(data) {
	            alertService.add('danger', data.error);
	        });
		};
		
	}
</script>
<div class="col-xs-12" ng-controller="NsLonelyController">
	<div class="row">
		<div class="eb-content">
			<div class="form-group">
				<select class="form-control" name="server_id"
					id="select_game_server" ng-model="formData.server_id"
					ng-init="formData.server_id = 0" multiple="multiple"
					ng-multiple="true" size=20>
					<option value="0"><?php echo Lang::get('serverapi.select_nszj_main_game_server') ?></option>
						<optgroup>
						<?php foreach ($server as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
				</select>

			</div>
			<div class="form-group" style="height: 40px;">
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.open_ns_lonely') ?>"
						ng-click="process('/game-server-api/tournament/lonely/open?action=open')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-primary"
						value="<?php echo Lang::get('serverapi.look_ns_lonely') ?>"
						ng-click="process('/game-server-api/tournament/lonely/open?action=look')" />
				</div>
				<div class="col-md-4" style="padding: 0">
					<input type='button' class="btn btn-danger"
						value="<?php echo Lang::get('serverapi.close_ns_lonely') ?>"
						ng-click="process('/game-server-api/tournament/lonely/open?action=close')" />
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