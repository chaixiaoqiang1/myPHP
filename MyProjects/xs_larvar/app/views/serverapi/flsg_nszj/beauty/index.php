<script >
	function serverBeautyController($scope, $http, alertService)
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
				alertService.add('success', data.result);
			}).error(function(data) {
				alertService.add('danger', data.error);
			});
		};
	}
</script>

<div class="col-xs-12" ng-controller="serverBeautyController">
	<div class="row" >
		<div class="eb-content">
			<form action="/game-server-api/beauty" method="post" role="form" ng-submit="processFrom('/game-server-api/beauty')" onsubmit="return false;">
				<div class="form-group">
					<select class="form-control" name="server_id" id="select_game_server" ng-model="formData.server_id" ng-init="formData.server_id=0" multiple="multiple" ng-multiple="true" size=10>
						<optgroup label="<?php echo Lang::get('serverapi.select_game_server') ?>">
						<?php foreach ($servers as $k => $v) { ?>
							<option value="<?php echo $v->server_id?>"><?php echo $v->server_name;?></option>
						<?php } ?>		
						</optgroup>
					</select>
				</div>		

				<div class="col-md-6" style="padding: 0px">
					<input type="text" class="form-control ng-pristine ng-invalid ng-invalid-required" 
							ng-model="formData.level" name="level" 
							placeholder="<?php echo Lang::get('serverapi.beauty_change') ?>" />
				</div>
				<br><br>
				
				<div class="col-md-6" style="padding: 0px">
					<input type="submit" class="btn btn-default" value="<?php echo Lang::get('basic.btn_submit') ?>" style="display:block"/>	
				</div>
			</form>	 
		</div><!-- /.col -->
	</div>
	<div class="row margin-top-10">
		<div class="eb-content"> 
			<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">{{alert.msg}}</alert>
		</div>
	</div>

</div>